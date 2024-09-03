<?php include 'partials/Adminhead.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height">
    <title>Crear Cita</title>
    <link rel="stylesheet" href="/assets/Brayan/styles/admin.css">
    <script>
        function validateForm() {
            const nombrePropietario = document.getElementById('nombre_propietario').value;
            const numeroContacto = document.getElementById('numero_contacto').value;
            const nombreMascota = document.getElementById('nombre_mascota').value;
            const razaMascota = document.getElementById('raza_mascota').value;
            const servicio = document.getElementById('servicio').value;

            const letters = /^[A-Za-zñÑ\s]+$/;
            const numbers = /^[0-9]+$/;

            if (!letters.test(nombrePropietario)) {
                alert('El nombre del propietario solo debe contener letras');
                return false;
            }
            if (!numbers.test(numeroContacto)) {
                alert('El número de contacto solo debe contener números');
                return false;
            }
            if (!letters.test(nombreMascota)) {
                alert('El nombre de la mascota solo debe contener letras');
                return false;
            }
            if (!letters.test(razaMascota)) {
                alert('La raza de la mascota solo debe contener letras');
                return false;
            }
            if (!letters.test(servicio)) {
                alert('El servicio solo debe contener letras');
                return false;
            }
            return true;
        }
    </script>
</head>
<body id="edit-page-body">

    <div class="container-fluid d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="container d-flex flex-column justify-content-center align-items-center">
            <h2>Crear Cita</h2>
            <form action="index_citas.php?action=create" method="POST" class="form-edit" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label for="nombre_propietario" class="form-label">Nombre Propietario</label>
                    <input type="text" class="form-control" id="nombre_propietario" name="nombre_propietario" required>
                </div>
                <div class="mb-3">
                    <label for="numero_contacto" class="form-label">Número de Contacto</label>
                    <input type="text" class="form-control" id="numero_contacto" name="numero_contacto" required>
                </div>
                <div class="mb-3">
                    <label for="nombre_mascota" class="form-label">Nombre Mascota</label>
                    <input type="text" class="form-control" id="nombre_mascota" name="nombre_mascota" required>
                </div>
                <div class="mb-3">
                    <label for="raza_mascota" class="form-label">Raza Mascota</label>
                    <input type="text" class="form-control" id="raza_mascota" name="raza_mascota" required>
                </div>
                <div class="mb-3">
                    <label for="servicio" class="form-label">Servicio</label>
                    <input type="text" class="form-control" id="servicio" name="servicio" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_cita" class="form-label">Fecha de la Cita</label>
                    <input type="date" class="form-control" id="fecha_cita" name="fecha_cita" required>
                </div>
                <div class="mb-3">
                    <label for="hora_cita" class="form-label">Hora de la Cita</label>
                    <select class="form-control" id="hora_cita" name="hora_cita" required>
                        <option value="">Seleccione una hora</option>
                        <!-- Lunes a Viernes -->
                        <?php
                        $horarios = ['07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '01:00-02:00', '02:00-03:00', '03:00-04:00', '04:00-05:00', '05:00-:06:00'];
                        $sabadoHorarios = ['07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00'];
                        $today = date('l');
                        $isSaturday = ($today == 'Saturday');
                        $availableHorarios = $isSaturday ? $sabadoHorarios : $horarios;
                        foreach ($availableHorarios as $horario) {
                            echo "<option value=\"$horario\">$horario</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Crear</button>
                <a href="/index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
