<?php

/**
 * Modelo de Pacientes / Mascotas y Dueños en VetMedik.
 */
class PacientData extends LbModel {
	public static $tablename = "pacient";

	public $id;
	public $no;
	public $name;
	public $species;
	public $breed;
	public $gender;
	public $color;
	public $weight;
	public $day_of_birth;
	public $owner_name;
	public $owner_phone;
	public $owner_email;
	public $owner_address;
	public $image;
	public $sick;
	public $medicaments;
	public $alergy;
	public $is_favorite;
	public $is_active;
	public $created_at;

	public function __construct() {
		$this->no = "";
		$this->name = "";
		$this->species = "Canino";
		$this->breed = "";
		$this->gender = "Macho";
		$this->color = "";
		$this->weight = 0.0;
		$this->day_of_birth = date('Y-m-d');
		$this->owner_name = "";
		$this->owner_phone = "";
		$this->owner_email = "";
		$this->owner_address = "";
		$this->image = "";
		$this->sick = "";
		$this->medicaments = "";
		$this->alergy = "";
		$this->is_favorite = 0;
		$this->is_active = 1;
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

	public static function getLike(string $q): array {
		$db = static::getDb();
		$stmt = $db->prepare(
			"SELECT * FROM " . static::$tablename . " WHERE name LIKE :q OR owner_name LIKE :q OR breed LIKE :q OR species LIKE :q ORDER BY id DESC"
		);
		$stmt->execute(['q' => "%{$q}%"]);
		return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class);
	}

	public static function getByDateRange($start, $end): array {
		$db = static::getDb();
		$stmt = $db->prepare("SELECT * FROM " . static::$tablename . " WHERE DATE(created_at) >= :s AND DATE(created_at) <= :e ORDER BY id DESC");
		$stmt->execute(['s' => $start, 'e' => $end]);
		return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::class);
	}

	public function getReservations(): array {
		return ReservationData::getAllByPacientId($this->id);
	}
}
