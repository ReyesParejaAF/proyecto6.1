<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Citas</title>
    <link rel="stylesheet" href="assets/Andres/styles/admin.css">
    <?php include 'partials/header.php'; ?>
    <style>
        /* Estilos adicionales para el calendario */
        .calendar-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
            background-color: #fffbe0; /* Fondo blanco amarillento */
            padding: 10px;
            border-radius: 10px;
        }

        .calendar-day {
            padding: 5px;
            border: 1px solid #ccc;
            background-color: #fffbe0; /* Fondo blanco amarillento */
            text-align: center;
            font-size: 14px;
        }

        .calendar-day.booked {
            background-color: #ffcccc; /* Fondo rojo para ocupado */
        }

        .calendar-day.available {
            background-color: #ccffcc; /* Fondo verde para disponible */
            cursor: pointer;
        }
        .calendar-day.selected {
            background-color: #b0c4de; /* Azul claro para el horario seleccionado */
            color: white;
        }

        .calendar-day.current {
            background-color: #b3d1ff; /* Fondo azul claro para la hora actual */
            font-weight: bold;
        }

        .calendar-time {
            text-align: center;
            font-weight: bold;
            background-color: #fffbe0; /* Fondo blanco amarillento */
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            background-color: #ffffde; /* Fondo blanco amarillento */
        }

        .calendar-nav {
            grid-column: span 8;
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #ffffde; /* Fondo blanco amarillento */
            border-bottom: 1px solid #ccc;
            border-radius: 10px 10px 0 0;
        }

        .calendar-footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="calendar-wrapper">
        <div class="calendar">
            <div class="calendar-nav">
                <?php if ($startOfWeek > new DateTime('today')): ?>
                    <a href="index.php?controller=FormularioController&action=calendario&week=<?= $currentWeek - 1 ?>">← Semana Anterior</a>
                <?php else: ?>
                    <span style="color: gray;">← Semana Anterior</span>
                <?php endif; ?>
                <span id="current-week"><?= $startOfWeek->format('d M Y') ?> - <?= $endOfWeek->format('d M Y') ?></span>
                <a href="index.php?controller=FormularioController&action=calendario&week=<?= $currentWeek + 1 ?>">Siguiente Semana →</a>
            </div>

            <div class="calendar-time">Hora</div>
            <?php
            // Cabecera con los días de la semana
            $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            foreach ($days as $day) {
                echo "<div class='calendar-day-header'>$day</div>";
            }

            // Mostrar horas de 08:00 a 17:00
            $hours = range(8, 17);
            foreach ($hours as $hour) {
                echo "<div class='calendar-time'>" . sprintf('%02d:00', $hour) . "</div>";
                for ($i = 0; $i < 7; $i++) {
                    $day = clone $startOfWeek;
                    $day->modify("+$i day");

                    $timeSlotStart = $day->format('Y-m-d') . ' ' . sprintf('%02d:00:00', $hour);
                    $timeSlotEnd = $day->format('Y-m-d') . ' ' . sprintf('%02d:59:59', $hour);

                    $isBooked = false;
                    $isCurrent = false;

                    foreach ($citas as $cita) {
                        if (isset($cita['fecha_cita']) && isset($cita['hora_cita'])) {
                            $citaStart = $cita['fecha_cita'] . ' ' . $cita['hora_cita'];
                            $citaEnd = date('Y-m-d H:i:s', strtotime($citaStart . ' +1 hour'));

                            // Verificar si hay una intersección entre el rango de tiempo de la cita y el intervalo de la celda
                            if (($citaStart < $timeSlotEnd) && ($citaEnd > $timeSlotStart)) {
                                $isBooked = true;
                                break;
                            }

                            // Verificar si es la hora actual de la cita que se está editando
                            if (isset($citaActual) && $cita['fecha_cita'] == $citaActual['fecha_cita'] && $cita['hora_cita'] == $citaActual['hora_cita']) {
                                $isCurrent = true;
                            }
                        }
                    }

                    if ($isCurrent) {
                        echo "<div class='calendar-day current' data-date='" . $day->format('Y-m-d') . "' data-time='" . sprintf('%02d:00:00', $hour) . "'>";
                        echo "Actual";
                    } else {
                        echo "<div class='calendar-day " . ($isBooked ? 'booked' : 'available') . "' data-date='" . $day->format('Y-m-d') . "' data-time='" . sprintf('%02d:00:00', $hour) . "'>";
                        echo $isBooked ? 'Ocupado' : 'Disponible';
                    }
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>

    <div class="calendar-footer">
        <a href="index.php?controller=FormularioController&action=create" class="btn btn-primary">Volver a Crear Cita</a>
    </div>

    <script>
        document.querySelectorAll('.calendar-day.available').forEach(function(day) {
            day.addEventListener('click', function() {
                const selectedDate = day.getAttribute('data-date');
                const selectedTime = day.getAttribute('data-time');
                
                // Asigna la fecha y la hora seleccionadas al formulario principal
                window.opener.document.getElementById('fecha').value = selectedDate;
                window.opener.document.getElementById('hora').value = selectedTime.substring(0, 5);
                
                window.close(); // Cierra la ventana del calendario
            });
        });
    </script>
</body>
</html>