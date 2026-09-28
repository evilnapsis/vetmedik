<?php
namespace App\Service;

use PacientData;

/**
 * Servicio de negocio para la administración de mascotas / pacientes y propietarios.
 */
class PacientService {
    public function getAllPacients(): array {
        return PacientData::getAll();
    }

    public function getPacientById(int $id) {
        return PacientData::find($id);
    }

    public function searchPacients(string $query): array {
        return PacientData::getLike($query);
    }

    public function createPacient(array $data): bool {
        $p = new PacientData();
        $p->no = trim($data['no'] ?? ('VET-' . strtoupper(substr(uniqid(), -5))));
        $p->name = trim($data['name'] ?? '');
        $p->species = trim($data['species'] ?? 'Canino');
        $p->breed = trim($data['breed'] ?? '');
        $p->gender = trim($data['gender'] ?? 'Macho');
        $p->color = trim($data['color'] ?? '');
        $p->weight = (float)($data['weight'] ?? 0.0);
        $p->day_of_birth = !empty($data['day_of_birth']) ? $data['day_of_birth'] : null;
        $p->owner_name = trim($data['owner_name'] ?? '');
        $p->owner_phone = trim($data['owner_phone'] ?? '');
        $p->owner_email = trim($data['owner_email'] ?? '');
        $p->owner_address = trim($data['owner_address'] ?? '');
        $p->sick = trim($data['sick'] ?? '');
        $p->medicaments = trim($data['medicaments'] ?? '');
        $p->alergy = trim($data['alergy'] ?? '');
        $p->created_at = date('Y-m-d H:i:s');
        return $p->save();
    }

    public function updatePacient(int $id, array $data): bool {
        $p = PacientData::find($id);
        if (!$p) return false;

        $p->no = trim($data['no'] ?? $p->no);
        $p->name = trim($data['name'] ?? $p->name);
        $p->species = trim($data['species'] ?? $p->species);
        $p->breed = trim($data['breed'] ?? $p->breed);
        $p->gender = trim($data['gender'] ?? $p->gender);
        $p->color = trim($data['color'] ?? $p->color);
        $p->weight = (float)($data['weight'] ?? $p->weight);
        $p->day_of_birth = !empty($data['day_of_birth']) ? $data['day_of_birth'] : $p->day_of_birth;
        $p->owner_name = trim($data['owner_name'] ?? $p->owner_name);
        $p->owner_phone = trim($data['owner_phone'] ?? $p->owner_phone);
        $p->owner_email = trim($data['owner_email'] ?? $p->owner_email);
        $p->owner_address = trim($data['owner_address'] ?? $p->owner_address);
        $p->sick = trim($data['sick'] ?? $p->sick);
        $p->medicaments = trim($data['medicaments'] ?? $p->medicaments);
        $p->alergy = trim($data['alergy'] ?? $p->alergy);

        return $p->save();
    }

    public function deletePacient(int $id): bool {
        $pacient = PacientData::find($id);
        return $pacient ? $pacient->delete() : false;
    }
}
