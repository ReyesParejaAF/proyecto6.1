<?php
require_once __DIR__ . '/../models/Cita.php';

class CitaController {
    private $citaModel;

    public function __construct() {
        $this->citaModel = new Cita();
    }

    public function index() {
        $citas = $this->citaModel->getCitas();
        include 'views/cita/indexAgen.php';
    }

    public function edit($id) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errors = $this->validateData($_POST);
            if (!empty($errors)) {
                $cita = $_POST;
                include __DIR__ . '/../views/cita/edit.php';
                return;
            }
            $data = [
                'nombre_propietario' => $_POST['nombre_propietario'],
                'numero_contacto' => $_POST['numero_contacto'],
                'nombre_mascota' => $_POST['nombre_mascota'],
                'raza_mascota' => $_POST['raza_mascota'],
                'servicio' => $_POST['servicio'],
                'fecha_cita' => $_POST['fecha_cita'],
                'hora_cita' => $_POST['hora_cita']
            ];
            $this->citaModel->updateCita($id, $data);
            header('Location: /index_citas.php');
            exit();
        } else {
            $cita = $this->citaModel->getCitaById($id);
            include __DIR__ . '/../views/cita/edit.php';
        }
    }
    public function create() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errors = $this->validateData($_POST);
            if (!empty($errors)) {
                $cita = $_POST;
                include __DIR__ . '/../views/cita/create.php';;
                return;
            }
            $fecha_cita = $_POST['fecha_cita'];
            $hora_cita = $_POST['hora_cita'];
            
            // Verificar disponibilidad de la hora
            if ($this->citaModel->isHoraReservada($fecha_cita, $hora_cita)) {
                $errors[] = "La hora seleccionada ya está reservada.";
                $cita = $_POST;
                include __DIR__ . '/../views/cita/create.php';
                return;
            }
            
            $data = [
                'nombre_propietario' => $_POST['nombre_propietario'],
                'numero_contacto' => $_POST['numero_contacto'],
                'nombre_mascota' => $_POST['nombre_mascota'],
                'raza_mascota' => $_POST['raza_mascota'],
                'servicio' => $_POST['servicio'],
                'fecha_cita' => $fecha_cita,
                'hora_cita' => $hora_cita
            ];
            $this->citaModel->createCita($data);
            header('Location: /index_citas.php');
            exit();
        } else {
            include __DIR__ . '/../views/cita/create.php';
        }
    }
    

    private function validateData($data) {
        $errors = [];
        if (!preg_match("/^[A-Za-zñÑ\s]+$/", $data['nombre_propietario'])) {
            $errors[] = "El nombre del propietario solo debe contener letras.";
        }
        if (!preg_match("/^[0-9]+$/", $data['numero_contacto'])) {
            $errors[] = "El número de contacto solo debe contener números.";
        }
        if (!preg_match("/^[A-Za-zñÑ\s]+$/", $data['nombre_mascota'])) {
            $errors[] = "El nombre de la mascota solo debe contener letras.";
        }
        if (!preg_match("/^[A-Za-zñÑ\s]+$/", $data['raza_mascota'])) {
            $errors[] = "La raza de la mascota solo debe contener letras.";
        }
        if (!preg_match("/^[A-Za-zñÑ\s]+$/", $data['servicio'])) {
            $errors[] = "El servicio solo debe contener letras.";
        }
        return $errors;
    }

    public function delete($id) {
        $this->citaModel->deleteCita($id);
        header('Location: /index_citas.php');
        exit();
    }
}
