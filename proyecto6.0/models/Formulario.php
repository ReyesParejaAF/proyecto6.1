<?php

require_once 'database.php';

class Formulario {
    private $conn;
    private $table_name = "citas";

    public $id_cita;
    public $nombre_propietario;
    public $numero_contacto;
    public $nombre_mascota;
    public $raza_mascota;
    public $servicio;
    public $fecha_cita;
    public $hora_cita;
    public $id_mascota;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function getCitasByWeek($startOfWeek, $endOfWeek) {
        $startOfWeekFormatted = $startOfWeek->format('Y-m-d');
        $endOfWeekFormatted = $endOfWeek->format('Y-m-d');
    
        $query = "
            SELECT fecha_cita, hora_cita 
            FROM " . $this->table_name . " 
            WHERE fecha_cita BETWEEN ? AND ?
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $startOfWeekFormatted, $endOfWeekFormatted);
        $stmt->execute();
        $result = $stmt->get_result();
        $citas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        return $citas;
    }

    public function isTimeSlotTaken($fecha, $hora) {
        $horaInicio = new DateTime($hora);
        $horaFin = clone $horaInicio;
        $horaFin->modify('+1 hour');

        $query = "
            SELECT COUNT(*) 
            FROM " . $this->table_name . " 
            WHERE 
                fecha_cita = ? 
                AND (
                    (hora_cita <= ? AND ADDTIME(hora_cita, '01:00:00') > ?) OR
                    (? <= hora_cita AND ADDTIME(?, '01:00:00') > hora_cita)
                )
        ";
    
        $stmt = $this->conn->prepare($query);

        // Asigna los valores formateados a variables antes de pasarlas a bind_param
        $horaFinStr = $horaFin->format('H:i:s');
        $horaInicioStr = $horaInicio->format('H:i:s');

        $stmt->bind_param("sssss", 
            $fecha, 
            $horaFinStr, 
            $horaInicioStr,
            $horaInicioStr,
            $horaInicioStr
        );

    
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    
        return $count > 0;  
    }

    public function create() {
        // Verificar si el id_mascota existe en la tabla mascota antes de insertar
        $query = "SELECT id_mascota FROM mascota WHERE id_mascota = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_mascota);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows === 0) {
            echo "<script>alert('Error: Mascota no encontrada.'); window.location.href='index.php?controller=FormularioController&action=create';</script>";
            return false;
        }
    
        $stmt->close();
    
        // Inserción de datos en la tabla citas
        $query = "INSERT INTO " . $this->table_name . " 
                    (nombre_propietario, numero_contacto, nombre_mascota, raza_mascota, servicio, fecha_cita, hora_cita, id_mascota) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación del statement: " . $this->conn->error);
        }
    
        $stmt->bind_param("sssssssi", 
            $this->nombre_propietario, 
            $this->numero_contacto, 
            $this->nombre_mascota, 
            $this->raza_mascota, 
            $this->servicio, 
            $this->fecha_cita, 
            $this->hora_cita,
            $this->id_mascota
        );
    
        $result = $stmt->execute();
        if ($result === false) {
            die("Error en la ejecución del statement: " . $stmt->error);
        }
    
        $stmt->close();
        return $result;
    }

    public function getMascotaDataByUserId($user_id) {
        $query = "SELECT u.nombre AS nombre_propietario, u.telefono AS numero_contacto, m.nombre_mascota, m.raza AS raza_mascota 
                  FROM usuario u 
                  LEFT JOIN mascota m ON u.id = m.id 
                  WHERE u.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $mascota_data = $result->fetch_assoc();
        $stmt->close();
        return $mascota_data;
    }

    public function getMascotaIdByUserId($user_id) {
        $query = "SELECT id_mascota FROM mascota WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $stmt->bind_result($id_mascota); 
        $stmt->fetch(); 
        $stmt->close();
        
        return $id_mascota; 
    }

    public function deleteById($id_cita) {
        if ($this->conn->connect_error) {
            die("Error en la conexión a la base de datos: " . $this->conn->connect_error);
        }
    
        $query = "DELETE FROM " . $this->table_name . " WHERE id_cita = ?";
        $stmt = $this->conn->prepare($query);
    
        if ($stmt === false) {
            die("Error en la preparación del statement: " . $this->conn->error);
        }
    
        $stmt->bind_param("i", $id_cita);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows > 0;
        $stmt->close();
    
        return $affected_rows;
    }

    public function getById($id_cita, $id_mascota) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_cita = ? AND id_mascota = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación del statement: " . $this->conn->error);
        }
    
        $stmt->bind_param("ii", $id_cita, $id_mascota);
        $stmt->execute();
        $result = $stmt->get_result();
        $cita = $result->fetch_assoc();
        $stmt->close();
        return $cita;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                    SET nombre_propietario = ?, numero_contacto = ?, nombre_mascota = ?, raza_mascota = ?, servicio = ?, fecha_cita = ?, hora_cita = ?
                    WHERE id_cita = ? AND id_mascota = ?";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación del statement: " . $this->conn->error);
        }

        $stmt->bind_param("sssssssii", 
            $this->nombre_propietario, 
            $this->numero_contacto, 
            $this->nombre_mascota, 
            $this->raza_mascota, 
            $this->servicio, 
            $this->fecha_cita, 
            $this->hora_cita,
            $this->id_cita,
            $this->id_mascota
        );

        $result = $stmt->execute();
        if ($result === false) {
            die("Error en la ejecución del statement: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }

    public function getAllByMascota($mascota_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_mascota = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación del statement: " . $this->conn->error);
        }
    
        $stmt->bind_param("i", $mascota_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $citas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $citas;
    }

    public function checkAvailability($fecha, $hora) {
        $horaInicio = new DateTime($hora);
        $horaFin = clone $horaInicio;
        $horaFin->modify('+1 hour');

        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                 WHERE fecha_cita = ? AND 
                 ((hora_cita BETWEEN ? AND ?) OR 
                  (ADDTIME(hora_cita, '01:00:00') BETWEEN ? AND ?) OR
                  (hora_cita <= ? AND ADDTIME(hora_cita, '01:00:00') >= ?))";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssss", 
            $fecha, 
            $horaInicio->format('H:i:s'), 
            $horaFin->format('H:i:s'), 
            $horaInicio->format('H:i:s'), 
            $horaFin->format('H:i:s'), 
            $horaInicio->format('H:i:s'), 
            $horaFin->format('H:i:s')
        );
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count === 0;
    }
}