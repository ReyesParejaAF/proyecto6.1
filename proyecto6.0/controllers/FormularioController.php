<?php

require_once __DIR__ . '\..\models\Formulario.php';

class FormularioController {
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = 1;
            }

            $user_id = $_SESSION['user_id'];
            $formulario = new Formulario();
            
            $id_mascota = $formulario->getMascotaIdByUserId($user_id);

            if (!$id_mascota) {
                echo "<script>alert('No se encontró una mascota asociada a este usuario. Por favor, registre una mascota primero.'); window.location.href='index.php?controller=WelcomeController&action=index';</script>";
                return;
            }

            $formulario->nombre_propietario = isset($_POST['name']) ? $_POST['name'] : '';
            $formulario->numero_contacto = isset($_POST['tel']) ? $_POST['tel'] : '';
            $formulario->nombre_mascota = isset($_POST['mascota']) ? $_POST['mascota'] : '';
            $formulario->raza_mascota = isset($_POST['razamascota']) ? $_POST['razamascota'] : '';
            $formulario->servicio = isset($_POST['servicio']) ? $_POST['servicio'] : ''; 
            $formulario->fecha_cita = isset($_POST['fecha']) ? $_POST['fecha'] : '';
            $formulario->hora_cita = isset($_POST['hora']) ? $_POST['hora'] : '';
            $formulario->id_mascota = $id_mascota;

            // Verificar si la fecha y hora seleccionadas ya están ocupadas
            if ($formulario->isTimeSlotTaken($formulario->fecha_cita, $formulario->hora_cita)) {
                echo "<script>alert('La hora seleccionada ya está ocupada. Por favor, seleccione otra hora.'); window.location.href='index.php?controller=FormularioController&action=create';</script>";
                return;
            }

            if ($formulario->create()) {
                echo "<script>alert('Cita agendada exitosamente.'); window.location.href='index.php?controller=FormularioController&action=create';</script>";
            } else {
                echo "<script>alert('Error al agendar la cita.'); window.location.href='index.php?controller=FormularioController&action=create';</script>";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = 1;
            }

            $user_id = $_SESSION['user_id'];
            $formulario = new Formulario();
            $mascota_data = $formulario->getMascotaDataByUserId($user_id);

            require_once 'views/create.php';
        } else {
            echo "Método de solicitud no permitido.";
        }
    }

    public function list() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
        }
    
        $user_id = $_SESSION['user_id'];
        $formulario = new Formulario();
        
        $id_mascota = $formulario->getMascotaIdByUserId($user_id);
    
        $citas = $formulario->getAllByMascota($id_mascota);
    
        if (empty($citas)) {
            echo "<script>alert('No se encontraron citas para este usuario.');</script>";
        }
    
        require_once 'views/lista_citas.php';
    }
    
    public function calendario() {
        $formulario = new Formulario();
    
        $currentWeek = isset($_GET['week']) ? intval($_GET['week']) : 0;
    
        $startOfWeek = new DateTime();
        $startOfWeek->modify(($currentWeek * 7) . ' days');
        $startOfWeek->modify('monday this week');
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->modify('+6 days');
    
        $citas = $formulario->getCitasByWeek($startOfWeek, $endOfWeek);
        
        $citaActual = null;
        if (isset($_GET['id_cita'])) {
            $citaActual = $formulario->getById($_GET['id_cita'], $_SESSION['user_id']);
        }

        require_once 'views/calendario.php';
    }

    public function delete() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
        }

        $user_id = $_SESSION['user_id'];
        $id_cita = $_GET['id'] ?? null;

        if ($id_cita) {
            $formulario = new Formulario();
            if ($formulario->deleteById($id_cita, $user_id)) {
                echo "<script>alert('Cita borrada exitosamente.'); window.location.href='index.php?controller=FormularioController&action=list';</script>";
            } else {
                echo "<script>alert('Error al borrar la cita.'); window.location.href='index.php?controller=FormularioController&action=list';</script>";
            }
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = 1;
            }
    
            $user_id = $_SESSION['user_id'];
            $id_cita = $_POST['id_cita'] ?? null;
            $formulario = new Formulario();
    
            $id_mascota = $formulario->getMascotaIdByUserId($user_id);
    
            if (!$id_mascota) {
                echo "<script>alert('No se encontró una mascota asociada a este usuario.');</script>";
                return;
            }
    
            if ($id_cita) {
                $formulario->id_cita = $id_cita;
                $formulario->nombre_propietario = $_POST['name'] ?? '';
                $formulario->numero_contacto = $_POST['tel'] ?? '';
                $formulario->nombre_mascota = $_POST['mascota'] ?? '';
                $formulario->raza_mascota = $_POST['razamascota'] ?? '';
                $formulario->servicio = $_POST['servicio'] ?? '';
                $formulario->fecha_cita = $_POST['fecha'] ?? '';
                $formulario->hora_cita = $_POST['hora'] ?? '';
                $formulario->id_mascota = $id_mascota;
    
                // Verificar si la fecha y hora seleccionadas ya están ocupadas
                if ($formulario->isTimeSlotTaken($formulario->fecha_cita, $formulario->hora_cita)) {
                    echo "<script>alert('La hora seleccionada ya está ocupada. Por favor, seleccione otra hora.'); window.location.href='index.php?controller=FormularioController&action=edit&id_cita=' . $id_cita;</script>";
                    return;
                }
    
                if ($formulario->update()) {
                    echo "<script>alert('Cita actualizada exitosamente.'); window.location.href='index.php?controller=FormularioController&action=list';</script>";
                } else {
                    echo "<script>alert('Error al actualizar la cita.'); window.location.href='index.php?controller=FormularioController&action=edit&id_cita=' . $id_cita;</script>";
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = 1;
            }
    
            $user_id = $_SESSION['user_id'];
            $id_cita = $_GET['id'] ?? null;
            $formulario = new Formulario();
    
            $id_mascota = $formulario->getMascotaIdByUserId($user_id);
    
            if (!$id_mascota) {
                echo "<script>alert('No se encontró una mascota asociada a este usuario.');</script>";
                return;
            }
    
            if ($id_cita) {
                $cita = $formulario->getById($id_cita, $id_mascota);
    
                if ($cita) {
                    require_once 'views/editar_cita.php';
                } else {
                    echo "<script>alert('Cita no encontrada.'); window.location.href='index.php?controller=FormularioController&action=list';</script>";
                }
            }
        } else {
            echo "Método de solicitud no permitido.";
        }
    }
    
    public function checkAvailability() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha'] ?? null;
            $hora = $_POST['hora'] ?? null;
    
            if ($fecha && $hora) {
                $formulario = new Formulario();
                $isAvailable = $formulario->checkAvailability($fecha, $hora);
    
                if (!$isAvailable) {
                    echo 'ocupado';
                } else {
                    echo 'disponible';
                }
            }
        }
    }
}