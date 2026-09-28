<?php
namespace App\Controller;

use App\Service\PacientService;
use App\Service\MedicService;
use App\Service\ReservationService;
use StatusData;
use PaymentRecordData;
use ViewEngine;

class HomeController {
    public function index(): void {
        $pacientService = new PacientService();
        $medicService = new MedicService();
        $reservationService = new ReservationService();

        $pacients = $pacientService->getAllPacients();
        $medics = $medicService->getAllMedics();
        $upcomingReservations = $reservationService->getUpcomingReservations();
        $pendingReservations = $reservationService->getPendingReservations();
        $allReservations = $reservationService->getAllReservations();

        // Mostrar las citas más relevantes (próximas si existen, o las más recientes)
        $displayReservations = !empty($upcomingReservations) 
            ? array_slice($upcomingReservations, 0, 9) 
            : array_slice($allReservations, 0, 9);

        // Ingresos totales registrados
        $payments = PaymentRecordData::getAll();
        $totalIncome = 0.0;
        foreach ($payments as $p) {
            $totalIncome += (float)$p->amount;
        }

        // 1. Distribución de Mascotas por Especie
        $speciesCounts = [];
        foreach ($pacients as $pacient) {
            $spec = !empty($pacient->species) ? trim($pacient->species) : 'Otros';
            $speciesCounts[$spec] = ($speciesCounts[$spec] ?? 0) + 1;
        }
        if (empty($speciesCounts)) {
            $speciesCounts = ['Canino' => 0, 'Felino' => 0, 'Aves' => 0, 'Otros' => 0];
        }

        // 2. Distribución de Consultas por Estatus
        $statusCounts = [
            'Pendiente' => 0,
            'Aplicada' => 0,
            'No Asistió' => 0,
            'Cancelada' => 0
        ];
        $statuses = StatusData::getAll();
        $statusMap = [];
        foreach ($statuses as $st) {
            $statusMap[$st->id] = $st->name;
        }
        foreach ($allReservations as $r) {
            $stName = $statusMap[$r->status_id] ?? 'Pendiente';
            if (isset($statusCounts[$stName])) {
                $statusCounts[$stName]++;
            } else {
                $statusCounts[$stName] = 1;
            }
        }

        // 3. Actividad mensual de citas (últimos 6 meses)
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mKey = date('Y-m', strtotime("-$i months"));
            $mLabel = date('M Y', strtotime("-$i months"));
            $monthlyLabels[] = $mLabel;
            $monthlyData[$mKey] = 0;
        }
        foreach ($allReservations as $r) {
            $rMonth = substr($r->date_at, 0, 7);
            if (isset($monthlyData[$rMonth])) {
                $monthlyData[$rMonth]++;
            }
        }

        ViewEngine::render('home/index.html.twig', [
            'pacients_count' => count($pacients),
            'medics_count' => count($medics),
            'upcoming_count' => count($upcomingReservations),
            'pending_count' => count($pendingReservations),
            'total_income' => $totalIncome,
            'recent_reservations' => $displayReservations,
            'species_labels' => array_keys($speciesCounts),
            'species_data' => array_values($speciesCounts),
            'status_labels' => array_keys($statusCounts),
            'status_data' => array_values($statusCounts),
            'monthly_labels' => $monthlyLabels,
            'monthly_data' => array_values($monthlyData),
        ]);
    }
}
