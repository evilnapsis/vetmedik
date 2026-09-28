<?php
namespace App\Service;

use ReservationData;
use MedicData;
use PacientData;
use PaymentData;
use StatusData;

/**
 * Servicio de negocio para filtrado, generación y exportación de reportes clínicos y financieros.
 */
class ReportService {
    public function generateReport(array $filters): array {
        $reservations = ReservationData::getByFilter($filters);
        
        $totalRevenue = 0.0;
        $pendingRevenue = 0.0;
        $attendedCount = 0;

        foreach ($reservations as $r) {
            $price = (float)$r->price;
            if ($r->payment_id == 2) {
                $totalRevenue += $price;
            } else {
                $pendingRevenue += $price;
            }

            if ($r->status_id == 2) {
                $attendedCount++;
            }
        }

        return [
            'reservations' => $reservations,
            'total_count' => count($reservations),
            'total_revenue' => $totalRevenue,
            'pending_revenue' => $pendingRevenue,
            'attended_count' => $attendedCount,
        ];
    }
}
