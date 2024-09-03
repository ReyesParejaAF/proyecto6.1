<?php include 'partials/Adminhead.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas</title>
    <link rel="stylesheet" href="/assets/Brayan/styles/admin.css">
</head>
<body>
    <div class="container-fluid p-0">
        <div id="after-nav" class="d-flex flex-column">
            <div id="after-nav-col-1" class="w-100 text-center">
                <div class="row">
                    <a href="#">
                        <h2>Gestión de citas</h2>
                    </a>
                </div>
            </div>
            <div id="after-nav-col-3" class="w-100">
                <div id="chek">
                    <div class="chekin"></div>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="/index_citas.php?action=create" class="btn btn-success">Agregar Cita</a> 
                        <a href="#" class="btn btn-warning btn-custom">Inventario productos</a>
                    </div>
                    <table id="customers" class="w-100">
                        <thead>
                            <tr>
                                <th scope="col">Id Cita</th>
                                <th scope="col">Nombre Propietario</th>
                                <th scope="col">Número de Contacto</th>
                                <th scope="col">Nombre Mascota</th>
                                <th scope="col">Raza Mascota</th>
                                <th scope="col">Servicio</th>
                                <th scope="col">Fecha de la Cita</th>
                                <th scope="col">Hora de la Cita</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="appointment">
                            <?php foreach ($citas as $cita) : ?>
                                <tr>
                                    <td><?php echo $cita['id_cita']; ?></td>
                                    <td><?php echo $cita['nombre_propietario']; ?></td>
                                    <td><?php echo $cita['numero_contacto']; ?></td>
                                    <td><?php echo $cita['nombre_mascota']; ?></td>
                                    <td><?php echo $cita['raza_mascota']; ?></td>
                                    <td><?php echo $cita['servicio']; ?></td>
                                    <td><?php echo $cita['fecha_cita']; ?></td>
                                    <td><?php echo $cita['hora_cita']; ?></td>
                                    <td>
                                        <a href="index_citas.php?action=edit&id=<?php echo $cita['id_cita']; ?>" class="btn editar-btn">Editar</a>
                                        <a href="index_citas.php?action=delete&id=<?php echo $cita['id_cita']; ?>" class="btn eliminar-btn">Eliminar</a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editarCita(id) {
            window.location.href = 'index.php?action=edit&id=' + id;
        }

        function eliminarCita(id) {
            if (confirm("¿Está seguro de que desea eliminar esta cita?")) {
                window.location.replace('index.php?action=delete&id=' + id);
            }
        }
    </script>
</body>
</html>
