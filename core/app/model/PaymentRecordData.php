<?php

/**
 * Modelo para registro y comprobante de pagos por servicios veterinarios en VetMedik.
 */
class PaymentRecordData extends LbModel {
	public static $tablename = "payment_record";

	public $id;
	public $reservation_id;
	public $pacient_id;
	public $amount;
	public $payment_method;
	public $notes;
	public $user_id;
	public $created_at;

	public function __construct() {
		$this->reservation_id = 0;
		$this->pacient_id = 0;
		$this->amount = 0.0;
		$this->payment_method = "Efectivo";
		$this->notes = "";
		$this->user_id = 0;
		$this->created_at = date('Y-m-d H:i:s');
	}

	public static function getById($id) {
		return static::find($id);
	}

	public static function getAll(): array {
		$db = static::getDb();
		$stmt = $db->query("SELECT * FROM " . static::$tablename . " ORDER BY id DESC");
		return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class);
	}

	public static function getByReservationId(int $resId) {
		$db = static::getDb();
		$stmt = $db->prepare("SELECT * FROM " . static::$tablename . " WHERE reservation_id = :rid ORDER BY id DESC LIMIT 1");
		$stmt->execute(['rid' => $resId]);
		$stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class);
		return $stmt->fetch() ?: null;
	}

	public static function getByDateRange($start, $end): array {
		$db = static::getDb();
		$stmt = $db->prepare("SELECT * FROM " . static::$tablename . " WHERE DATE(created_at) >= :s AND DATE(created_at) <= :e ORDER BY id DESC");
		$stmt->execute(['s' => $start, 'e' => $end]);
		return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class);
	}

	public function getReservation() {
		return $this->reservation_id ? ReservationData::find($this->reservation_id) : null;
	}

	public function getPacient() {
		return $this->pacient_id ? PacientData::find($this->pacient_id) : null;
	}

	public function getUser() {
		return $this->user_id ? UserData::find($this->user_id) : null;
	}
}
