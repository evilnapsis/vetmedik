<?php
namespace App\Service;

use PaymentRecordData;
use ReservationData;

/**
 * Servicio de negocio para la gestión de cobros y pagos de servicios veterinarios.
 */
class PaymentRecordService {
	public function getAll(): array {
		return PaymentRecordData::getAll();
	}

	public function getById(int $id) {
		return PaymentRecordData::find($id);
	}

	public function getByReservationId(int $resId) {
		return PaymentRecordData::getByReservationId($resId);
	}

	public function getPendingReservations(): array {
		$db = \Database::getPdo();
		$stmt = $db->query(
			"SELECT * FROM reservation WHERE payment_id = 1 AND price > 0 ORDER BY date_at DESC, time_at DESC"
		);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, \ReservationData::class);
	}

	public function registerPayment(array $data, int $userId): int {
		$resId = (int)($data['reservation_id'] ?? 0);
		$res = ReservationData::find($resId);

		$p = new PaymentRecordData();
		$p->reservation_id = $resId;
		$p->pacient_id = $res ? $res->pacient_id : (int)($data['pacient_id'] ?? 0);
		$p->amount = (float)($data['amount'] ?? ($res ? $res->price : 0.0));
		$p->payment_method = trim($data['payment_method'] ?? 'Efectivo');
		$p->notes = trim($data['notes'] ?? '');
		$p->user_id = $userId;
		$p->created_at = !empty($data['created_at']) ? $data['created_at'] : date('Y-m-d H:i:s');
		$p->save();

		// Actualizar estado de pago en la cita a "Pagado" (id=2)
		if ($res) {
			$res->payment_id = 2;
			// Si la cita estaba pendiente y se liquida el servicio, se puede marcar como atendida si aplica
			if (!empty($data['mark_attended']) && $res->status_id == 1) {
				$res->status_id = 2;
			}
			$res->save();
		}

		return $p->id;
	}

	public function deletePayment(int $id): bool {
		$pay = PaymentRecordData::find($id);
		if (!$pay) return false;

		$resId = $pay->reservation_id;
		$deleted = $pay->delete();
		
		// Si se borra el pago, devolver la cita a estado Pendiente de pago (id=1)
		if ($deleted && $resId) {
			$res = ReservationData::find($resId);
			if ($res) {
				$res->payment_id = 1;
				$res->save();
			}
		}

		return $deleted;
	}
}
