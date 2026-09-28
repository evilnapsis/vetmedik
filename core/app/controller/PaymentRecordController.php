<?php
namespace App\Controller;

use App\Service\PaymentRecordService;
use App\Service\ReservationService;
use App\Service\PacientService;
use ViewEngine;
use Request;

/**
 * Controlador para la gestión y registro de pagos por servicios veterinarios.
 */
class PaymentRecordController {
	private $paymentService;
	private $reservationService;
	private $pacientService;
	private $baseFolder;

	public function __construct() {
		$this->paymentService = new PaymentRecordService();
		$this->reservationService = new ReservationService();
		$this->pacientService = new PacientService();
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	public function index() {
		$payments = $this->paymentService->getAll();
		$pendingReservations = $this->paymentService->getPendingReservations();
		$totalCollected = array_reduce($payments, fn($acc, $p) => $acc + (float)$p->amount, 0.0);

		ViewEngine::render('payments/index.html.twig', [
			'payments' => $payments,
			'pending_reservations' => $pendingReservations,
			'total_collected' => $totalCollected
		]);
	}

	public function new() {
		$selectedReservationId = (int)Request::get('reservation_id', 0);
		$selectedReservation = null;
		if ($selectedReservationId > 0) {
			$selectedReservation = $this->reservationService->getReservationById($selectedReservationId);
		}

		$pendingReservations = $this->paymentService->getPendingReservations();

		ViewEngine::render('payments/new.html.twig', [
			'selected_reservation' => $selectedReservation,
			'pending_reservations' => $pendingReservations,
			'selected_id' => $selectedReservationId,
			'current_date' => date('Y-m-d H:i:s')
		]);
	}

	public function create() {
		$userId = $_SESSION['user_id'] ?? 1;
		$paymentId = $this->paymentService->registerPayment(Request::post(), $userId);
		$_SESSION['success'] = 'Pago registrado exitosamente';
		header('Location: ' . $this->baseFolder . '/payments/receipt/' . $paymentId);
		exit;
	}

	public function receipt($vars) {
		$id = (int)$vars['id'];
		$payment = $this->paymentService->getById($id);
		if (!$payment) {
			header('Location: ' . $this->baseFolder . '/payments');
			exit;
		}

		$reservation = $payment->getReservation();
		$pacient = $payment->getPacient();
		$medic = $reservation ? $reservation->getMedic() : null;
		$user = $payment->getUser();

		ViewEngine::render('payments/receipt.html.twig', [
			'payment' => $payment,
			'reservation' => $reservation,
			'pacient' => $pacient,
			'medic' => $medic,
			'user' => $user
		]);
	}

	public function delete($vars) {
		$id = (int)$vars['id'];
		$this->paymentService->deletePayment($id);
		$_SESSION['success'] = 'Registro de pago eliminado';
		header('Location: ' . $this->baseFolder . '/payments');
		exit;
	}
}
