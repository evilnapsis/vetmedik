<?php
namespace App\Controller;

use App\Service\ReportService;
use App\Service\PacientService;
use App\Service\MedicService;
use Request;
use ViewEngine;
use StatusData;
use PaymentData;

/**
 * Controlador de reportes clínicos y financieros con exportación CSV para VetMedik.
 */
class ReportController {
    private ReportService $reportService;
    private PacientService $pacientService;
    private MedicService $medicService;

    public function __construct() {
        $this->reportService = new ReportService();
        $this->pacientService = new PacientService();
        $this->medicService = new MedicService();
    }

    public function index(): void {
        $startDate = Request::get('start_date') ?: (Request::get('start_at') ?: date('Y-m-01'));
        $finishDate = Request::get('end_date') ?: (Request::get('finish_at') ?: date('Y-m-d'));

        $filters = [
            'medic_id' => Request::get('medic_id'),
            'pacient_id' => Request::get('pacient_id'),
            'status_id' => Request::get('status_id'),
            'start_at' => $startDate,
            'finish_at' => $finishDate,
        ];

        $reportData = $this->reportService->generateReport($filters);
        $pacients = $this->pacientService->getAllPacients();
        $medics = $this->medicService->getAllMedics();
        $statuses = StatusData::getAll();
        $payments = PaymentData::getAll();

        ViewEngine::render('reports/index.html.twig', [
            'reservations' => $reportData['reservations'],
            'total_count' => $reportData['total_count'],
            'total_revenue' => $reportData['total_revenue'],
            'pending_revenue' => $reportData['pending_revenue'],
            'attended_count' => $reportData['attended_count'],
            'pacients' => $pacients,
            'medics' => $medics,
            'statuses' => $statuses,
            'payments' => $payments,
            'start_date' => $startDate,
            'end_date' => $finishDate,
            'filters' => $filters
        ]);
    }

    public function exportCsv(): void {
        $startDate = Request::get('start_date') ?: (Request::get('start_at') ?: date('Y-m-01'));
        $finishDate = Request::get('end_date') ?: (Request::get('finish_at') ?: date('Y-m-d'));

        $filters = [
            'medic_id' => Request::get('medic_id'),
            'pacient_id' => Request::get('pacient_id'),
            'status_id' => Request::get('status_id'),
            'start_at' => $startDate,
            'finish_at' => $finishDate,
        ];

        $reportData = $this->reportService->generateReport($filters);
        $reservations = $reportData['reservations'];

        $filename = "reporte_veterinaria_{$startDate}_al_{$finishDate}.csv";

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'Folio Cita',
            'Fecha',
            'Hora',
            'Mascota / Paciente',
            'Especie',
            'Raza',
            'Propietario / Dueño',
            'Teléfono Dueño',
            'Veterinario',
            'Servicio / Motivo',
            'Diagnóstico',
            'Costo ($)',
            'Estado Cita',
            'Estado Pago'
        ]);

        foreach ($reservations as $r) {
            $pacient = $r->getPacient();
            $medic = $r->getMedic();
            $status = $r->getStatus();
            $pay = $r->getPayment();

            fputcsv($output, [
                $r->id,
                $r->date_at,
                $r->time_at,
                $pacient ? $pacient->name : 'N/A',
                $pacient ? $pacient->species : 'N/A',
                $pacient ? $pacient->breed : '-',
                $pacient ? $pacient->owner_name : 'N/A',
                $pacient ? $pacient->owner_phone : '-',
                $medic ? ($medic->name . ' ' . $medic->lastname) : 'N/A',
                $r->title ?: 'Consulta',
                $r->sick ?: '-',
                number_format((float)$r->price, 2, '.', ''),
                $status ? $status->name : 'N/A',
                $pay ? $pay->name : 'Pendiente'
            ]);
        }

        fclose($output);
        exit;
    }
}
