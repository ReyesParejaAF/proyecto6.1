<?php
require_once 'controllers/CitaController.php';

$citaController = new CitaController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'edit':
        if ($id) {
            $citaController->edit($id);
        } else {
            $citaController->index();
        }
        break;
    case 'create':
        $citaController->create();
        break;
    case 'delete':
        if ($id) {
            $citaController->delete($id);
        } else {
            $citaController->index();
        }
        break;
    default:
        $citaController->index();
        break;
}
?>

<script>
    function editarCita(id) {
    window.location.href = 'index_citas.php?action=edit&id=' + id;
}

function eliminarCita(id) {
    if (confirm("¿Está seguro de que desea eliminar esta cita?")) {
        window.location.replace('index_citas.php?action=delete&id=' + id);
    }
}

</script>
