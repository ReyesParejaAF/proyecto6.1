<?php include 'partials/header.php' ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <link rel="stylesheet" href="assets/andres/styles/admin.css">
    <style>
        #message {
            color: green;
            font-weight: bold;
            text-align: center;
            font-size: 1.4em; 
            margin-bottom: 20px; 
            font-family: 'Times New Roman', Times, serif; 
        }
        .top-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .top-buttons a {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .top-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="citas1">
        <div class="top-buttons">
            <h5>Agende su cita:</h5>
            <a href="index.php?controller=FormularioController&action=list" class="btn btn-primary">Ver Mis Citas</a>
            <a href="index.php?controller=WelcomeController&action=index" class="btn btn-secondary ms-2">Volver a Bienvenida</a>
            <a href="index.php?controller=FormularioController&action=calendario" target="_blank">Ver horarios disponibles</a>
        </div>
    
        <?php if (isset($_SESSION['success_message'])): ?>
            <div id="message" style="color:green;">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div id="message" style="color:red;">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <form id="citaForm" action="index.php?controller=FormularioController&action=create" method="POST" onsubmit="return validarFormulario()">
            <label for="nombre">Nombre del propietario:*</label>
            <input type="text" name="name" id="nombre" value="<?php echo isset($mascota_data['nombre_propietario']) ? htmlspecialchars($mascota_data['nombre_propietario']) : ''; ?>" required>

            <label for="contacto">Numero de contacto:*</label>
            <input type="tel" name="tel" id="contacto" value="<?php echo isset($mascota_data['numero_contacto']) ? htmlspecialchars($mascota_data['numero_contacto']) : ''; ?>" required>

            <label for="nmascota">Nombre de la mascota:</label>
            <input type="text" name="mascota" id="nmascota" value="<?php echo isset($mascota_data['nombre_mascota']) ? htmlspecialchars($mascota_data['nombre_mascota']) : ''; ?>">

            <label for="rmascota">Raza de la mascota:*</label>
            <input type="text" name="razamascota" id="rmascota" value="<?php echo isset($mascota_data['raza_mascota']) ? htmlspecialchars($mascota_data['raza_mascota']) : ''; ?>" required>

            <label for="servicio">Servicio:*</label>
            <input type="text" name="servicio" id="servicio" required>

            <label for="fecha">Fecha de la cita:*</label>
            <input type="date" name="fecha" id="fecha" onfocus="openCalendar()" required><br><br>

            <label for="hora">Hora de la cita:*</label>
            <input type="time" name="hora" id="hora" onfocus="openCalendar()" required>
            
            <br><br>

            <div class="bottom-buttons">
            <a href="?controller=catalogo&action=catalogo">Agregar productos</a>
                <input type="submit" value="Agendar">
            </div>
        </form>

        <script>
            function openCalendar() {
                const calendarWindow = window.open('index.php?controller=FormularioController&action=calendario', '_blank', 'width=800,height=600');

                // Verifica si la ventana se cerró correctamente
                calendarWindow.addEventListener('beforeunload', function() {
                    window.opener = null;
                });
            }
            
            function validarFormulario() {
                var nombre = document.getElementById('nombre').value.trim();
                var contacto = document.getElementById('contacto').value.trim();
                var nmascota = document.getElementById('rmascota').value.trim();
                var rmascota = document.getElementById('rmascota').value.trim();
                var servicio = document.getElementById('servicio').value.trim();
                var fecha = document.getElementById('fecha').value;
                var hora = document.getElementById('hora').value;

                var textoRegex =  /^[A-Za-zñÑ\s]+$/;;

                if (!textoRegex.test(nombre)) {
                    alert('El campo "Nombre del propietario" debe contener solo letras.');
                    return false;
                }

                if (!textoRegex.test(nmascota)) {
                    alert('El campo "Nombre de la mascota" debe contener solo letras.');
                    return false;
                }    
                if (!textoRegex.test(rmascota)) {
                    alert('El campo "Raza de la mascota" debe contener solo letras.');
                    return false;
                }

                if (!textoRegex.test(servicio)) {
                    alert('El campo "Servicio" debe contener solo letras.');
                    return false;
            }

                if (nombre === '' || contacto === '' || rmascota === '' || servicio === '' || fecha === '' || hora === '') {
                    alert('Por favor, complete los campos obligatorios (*) para proceder al envío.');
                    return false;
                }

                var telRegex = /^3\d{9}$/;

                if (!telRegex.test(contacto)) {
                    alert('Por favor, ingrese un número de contacto valido.');
                    return false;
                }

                var selectedDate = new Date(fecha + 'T' + hora);
                var dayOfWeek = selectedDate.getDay();
                var selectedHour = parseInt(hora.split(':')[0], 10);
                var selectedMinutes = parseInt(hora.split(':')[1], 10);
                
                if (dayOfWeek >= 1 && dayOfWeek <= 5) { 
                    if (selectedHour < 8 || (selectedHour >= 18 && selectedMinutes > 0)) {
                        alert('El horario de atención de lunes a viernes es de 8:00 AM a 6:00 PM. Seleccione una hora dentro de este rango.');
                        return false;
                    }
                } else if (dayOfWeek === 0 || dayOfWeek === 6) { 
                    if (selectedHour < 11 || (selectedHour >= 16 && selectedMinutes > 0)) {
                        alert('El horario de atención de sábados y domingos es de 11:00 AM a 4:00 PM. Seleccione una hora dentro de este rango.');
                        return false;
                    }
                }

                var today = new Date();
                var selectedDate = new Date(fecha + 'T' + hora);

                if (selectedDate < today) {
                    alert('Seleccione una fecha y hora futura para la cita.');
                    return false;
                }

                return true;
        }

            function validarFecha() {
                var fecha = document.getElementById('fecha').value;
                var today = new Date().toISOString().split('T')[0];
            }

            document.getElementById('hora').addEventListener('change', function() {
                var fecha = document.getElementById('fecha').value;
                var hora = this.value;

                if (fecha && hora) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'index.php?controller=FormularioController&action=checkAvailability', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            if (xhr.responseText === 'ocupado') {
                                alert('La hora seleccionada ya está ocupada. Por favor, elija otra hora.');
                                document.getElementById('hora').value = ''; 
                            }
                        }
                    };
                    xhr.send('fecha=' + encodeURIComponent(fecha) + '&hora=' + encodeURIComponent(hora));
                }
            });

            document.getElementById('hora').addEventListener('change', function() {
                const fechaSeleccionada = document.getElementById('fecha').value;
                const horaSeleccionada = this.value;

                const ocupados = document.querySelectorAll('.calendar-day.booked');
                let horaOcupada = false;

                ocupados.forEach(function(ocupado) {
                    const fechaOcupada = ocupado.getAttribute('data-date');
                    const horaOcupadaInicio = ocupado.getAttribute('data-time');

                    if (fechaSeleccionada === fechaOcupada && horaSeleccionada === horaOcupadaInicio.substring(0, 5)) {
                        horaOcupada = true;
                    }
                });

                if (horaOcupada) {
                    alert('Esta hora ya está ocupada. Por favor, seleccione otra hora.');
                    this.value = '';  
                }
            });
        </script>
    </div>
</body>
</html>