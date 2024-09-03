<?php
require_once __DIR__ . '/Database.php';

class Cita {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    public function getCitas() {
        $query = "SELECT * FROM citas";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCitaById($id) {
        $query = "SELECT * FROM citas WHERE id_cita = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateCita($id, $data) {
        $query = "UPDATE citas SET nombre_propietario = ?, numero_contacto = ?, nombre_mascota = ?, raza_mascota = ?, servicio = ?, fecha_cita = ?, hora_cita = ? WHERE id_cita = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssssi", // "i" added for $id
            $data['nombre_propietario'],
            $data['numero_contacto'],
            $data['nombre_mascota'],
            $data['raza_mascota'],
            $data['servicio'],
            $data['fecha_cita'],
            $data['hora_cita'],
            $id
        );
        $stmt->execute();
    }

    public function createCita($data) {
        $query = "INSERT INTO citas (nombre_propietario, numero_contacto, nombre_mascota, raza_mascota, servicio, fecha_cita, hora_cita) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssss",
            $data['nombre_propietario'],
            $data['numero_contacto'],
            $data['nombre_mascota'],
            $data['raza_mascota'],
            $data['servicio'],
            $data['fecha_cita'],
            $data['hora_cita'],
        );
        $stmt->execute();
    }

    public function isHoraReservada($fecha_cita, $hora_cita) {
        $query = "SELECT COUNT(*) FROM citas WHERE fecha_cita = ? AND hora_cita = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $fecha_cita, $hora_cita);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }

    public function deleteCita($id) {
        $query = "DELETE FROM citas WHERE id_cita = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}
