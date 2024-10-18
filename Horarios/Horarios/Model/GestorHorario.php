<?php

class GestorHorario {
    private $filePath = "../Horarios/horarios.dat";
    private $HorasMáximasSemanales = 18; // Añadimos el límite semanal de horas
    private $ComplementariasMáximas = 6; // Ponemos el máximo de horas complementarias por semana
    public function insertarHora(FranjaHorario $franja) {
        // Asegurar que el directorio existe y que sino lo crea, le da permisos de escritura, lectura y ejecución
        if (!file_exists('horarios')) {
            mkdir('horarios', 0777, true);
        }

        // Leer el archivo si existe para posteriormente que lea el contenido de dicho archivo, para ello creamos el array horarios
        $horarios = [];
        if (file_exists($this->filePath)) {
            //Creamos la variable que obtiene el contenido del archivo
            $contenido = file_get_contents($this->filePath);
            if ($contenido) {
                // Separar las franjas horarias por "@" y almacenarlas en el array creado con un explode separador sobre la variable contenido
                $horarios = explode('@', $contenido);
            }
        }

        //Le damos la condición de los recreos para que no pueda meter en toda la semana en la franja de estas horas que pertenecen al recreo
        if (($franja->hora == '10:45-11:15') || ($franja->hora == '17:15-17:45')) {
                throw new Exception("Error: Esta franja horaria está reservada para los recreos.");
        }

        // Le damos que la franja horaria del martes a las 14:30-15:25 es reservada
        if ($franja->dia == Semana::Martes && $franja->hora == Hora::Octava) {
            if ($franja->materia != Materia::REUNIÓN_DEPARTAMENTO || $franja->tipoFranja != TipoFranja::Complementaria) {
                throw new Exception("Error: Franja horaria reservada para una reunión de departamento.");
            }
        }

        // Le decimos que solo puede haber una "Reunión de departamento" por semana (que está reservada para los martes una vez cada dos semanas)
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 4 && $campos[3] == Materia::REUNIÓN_DEPARTAMENTO->value) {
                // Ya existe una reunión de departamento en el horario
                throw new Exception("Error Horario: Ya existe la franja horaria de reunión de departamento.");
            }
        }

        // Validar si ya existe la franja horaria
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 3 && $campos[1] == $franja->dia->value && $campos[2] == $franja->hora->codigoHora()) {
                throw new Exception("Error Horario: La franja de hora ya existe, elige otra que esté disponible.");
            }
        }

        // Validar el número de franjas horarias de la misma materia por día - Condición 2
        $contadorFranjaMateria = 0;
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 4 && $campos[1] == $franja->dia->value && $campos[3] == $franja->materia->value) {
                $contadorFranjaMateria++;
                if ($contadorFranjaMateria >= 3) {
                    throw new Exception("Error Horario: La franja horaria ha superado el número de horas por día.");
                }
            }
        }

               // Verificar la **Condición 10**: Solo una franja de tutoría por semana
               foreach ($horarios as $registro) {
                $campos = explode(';', $registro);
                if (count($campos) >= 4 && $campos[3] == Materia::TUTORÍA->value) {
                    // Ya existe una tutoría en el horario
                    throw new Exception("Error Horario: La tutoría ya está establecida en el horario semanal.");
                }
            }

                // **Condición 11**: No se pueden establecer tres guardias seguidas
                if ($franja->materia == Materia::GUARDIA) {
                    $horasGuardia = [];
        
                    // Buscar todas las guardias en el mismo día
                    foreach ($horarios as $registro) {
                        $campos = explode(';', $registro);
                        if (count($campos) >= 3 && $campos[1] == $franja->dia->value && $campos[3] == Materia::GUARDIA->value) {
                            $horasGuardia[] = $campos[2]; // Añadir la hora de la guardia
                        }
                    }
                                // Ordenar las horas de guardia encontradas
            sort($horasGuardia);
        
        // Validar el número de franjas lectivas en el mismo día - Condición 3
        $contadorFranjaLectiva = 0;
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 7 && $campos[1] == $franja->dia->value && $campos[6] == TipoFranja::Lectiva->value) {
                $contadorFranjaLectiva++;
                if ($contadorFranjaLectiva >= 5) {
                    throw new Exception("Error Horario: El número de horas lectivas durante el día se ha superado.");
                }
            }
        }

        // Validar el número de franjas no lectivas en el mismo día - Condición 4
        $contadorFranjaNoLectiva = 0;
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 7 && $campos[1] == $franja->dia->value 
                && $campos[6] != TipoFranja::Lectiva->value && $campos[6] != TipoFranja::Recreo->value) {
                // Contamos todas las franjas no lectivas (excepto recreos)
                $contadorFranjaNoLectiva++;
                if ($contadorFranjaNoLectiva >= 3) {
                    throw new Exception("Error Horario: El número de horas complementarias durante este día se ha superado.");
                }
            }
        }

        // Validar el número de franjas lectivas durante la semana - Condición 5
        $contadorHorasLectivasSemana = 0;
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 7 && $campos[6] == TipoFranja::Lectiva->value) {
                // Contamos todas las franjas lectivas a lo largo de la semana
                $contadorHorasLectivasSemana++;
                if ($contadorHorasLectivasSemana >= 18) {
                    throw new Exception("Error Horario: El número de horas lectivas durante la semana se ha superado.");
                }
            }
        }

        // Validar el número de franjas no lectivas durante la semana - Condición 6
        $contadorHorasNoLectivasSemana = 0;
        foreach ($horarios as $registro) {
            $campos = explode(';', $registro);
            if (count($campos) >= 7 && $campos[6] != TipoFranja::Lectiva->value && $campos[6] != TipoFranja::Recreo->value) {
                // Contamos todas las franjas no lectivas a lo largo de la semana (excepto recreos)
                $contadorHorasNoLectivasSemana++;
                if ($contadorHorasNoLectivasSemana >= 6) {
                    throw new Exception("Error Horario: El número de horas complementarias durante la semana se ha superado.");
                }
            }
        }

        // Formatear la franja horaria para el archivo
       $registroNuevo = "{$franja->curso->value};{$franja->dia->value};{$franja->hora->codigoHora()};{$franja->materia->value};{$franja->clase->value};{$franja->color->value};{$franja->tipoFranja->value}@";
       file_put_contents($this->filePath, $registroNuevo, FILE_APPEND);
        // Escribir en el archivo
        file_put_contents($this->filePath, $registroNuevo, FILE_APPEND);
    }
}

    public function eliminarHora(FranjaHorario $franja) {
        // Verificar si el archivo existe
        if (!file_exists($this->filePath)) {
            throw new Exception("Error Horario: No se encontró el archivo de horarios.");
        }

        // Leer el contenido del archivo
        $contenido = file_get_contents($this->filePath);

        if (!$contenido) {
            throw new Exception("Error Horario: No hay franjas horarias registradas.");
        }

        // Separar las franjas horarias por "@"
        $horarios = explode('@', $contenido);

        // Buscar la franja horaria a eliminar
        $franjaEncontrada = false;
        $nuevoContenido = [];
        $existenCotutorias = false;

        foreach ($horarios as $registro) {
            if (empty(trim($registro))) {
                continue;
            }

            $campos = explode(';', $registro);

            // Verificar si es una franja de "Recreo" o "Reunión de departamento"
            if ($campos[3] == Materia::RECREO->value || $campos[3] == Materia::REUNIÓN_DEPARTAMENTO->value) {
                if ($campos[1] == $franja->dia->value && $campos[2] == $franja->hora->codigoHora()) {
                    throw new Exception("Error Eliminar hora: La franja horaria preestablecida, no se puede eliminar.");
                }
            }

            // Verificar si estamos eliminando una Tutoría y si existen Cotutorías
            if ($campos[3] == Materia::TUTORÍA->value) {
                if ($campos[1] == $franja->dia->value && $campos[2] == $franja->hora->codigoHora()) {
                    // Si es una franja de Tutoría, verificamos si hay Cotutorías en el archivo
                    foreach ($horarios as $reg) {
                        $regCampos = explode(';', $reg);
                        if ($regCampos[3] == Materia::COTUTORIA->value) {
                            $existenCotutorias = true;
                            break;
                        }
                    }

                    if ($existenCotutorias) {
                        throw new Exception("Error Eliminar hora: No se puede eliminar la tutoría, se deben eliminar primero el resto de cotutorías.");
                    }
                }
            }

            // Comprobamos si el registro corresponde con el día y la hora de la franja que se desea eliminar
            if (count($campos) >= 3 && $campos[1] == $franja->dia->value && $campos[2] == $franja->hora->codigoHora()) {
                // Si coincide, marcamos la franja como encontrada y no la añadimos al nuevo contenido
                $franjaEncontrada = true;
            } else {
                // Si no coincide, añadimos el registro al nuevo contenido
                $nuevoContenido[] = $registro;
            }
        }

        // Si no se encuentra la franja, lanzamos la excepción de la condición 12
        if (!$franjaEncontrada) {
            throw new Exception("Error Eliminar Hora: La hora y el día seleccionado no existe, no se puede eliminar.");
        }

        // Actualizamos el archivo con el nuevo contenido sin la franja eliminada
        file_put_contents($this->filePath, implode('@', $nuevoContenido));

        // Mensaje de éxito
        echo "La franja horaria ha sido eliminada correctamente.";
    }

    public function mostrarHorario() {
        // Definir días y horas del horario
        $diasSemana = ['L', 'M', 'X', 'J', 'V'];  // Lunes a Viernes
        $horasDia = [
            '1' => '08:00 - 08:55',
            '2' => '08:55 - 09:50',
            '3' => '09:50 - 10:45',
            '4' => '10:45 - 11:15',
            '5' => '11:15 - 12:10',
            '6' => '12:10 - 13:05',
            '7' => '13:05 - 14:00',
            '8' => '14:30 - 15:25',
            '9' => '15:25 - 16:20',
            '10' => '16:20 - 17:15',
            '11' => '17:45 - 18:40',
            '12' => '18:40 - 19:35',
            '13' => '19:35 - 20:30',

        ];

        // Inicializar matriz vacía para el horario
        $horario = [];
        foreach ($diasSemana as $dia) {
            foreach (array_keys($horasDia) as $hora) {
                $horario[$dia][$hora] = '';
            }
        }

        // Leer el archivo de horarios si existe
        if (file_exists($this->filePath)) {
            $contenido = file_get_contents($this->filePath);
            $franjas = explode('@', $contenido);

            foreach ($franjas as $franja) {
                if (trim($franja) == '') continue;
                $datosFranja = explode(';', $franja);

                // Formato: Curso;Dia;Hora;Materia;Clase;Color;Tipo
                if (count($datosFranja) >= 7) {
                    list($curso, $dia, $hora, $materia, $clase, $color, $tipo) = $datosFranja;
                    
                    // Formar el contenido de la celda
                    if ($tipo === 'L') {
                        // Es una hora lectiva, mostrar Curso, Materia, Clase con color
                        $horario[$dia][$hora] = "<div style='background-color: $color;'>
                            <strong>$curso</strong><br>$materia<br>$clase
                        </div>";
                    } else {
                        // Es una hora complementaria o recreo, solo mostrar la materia
                        $horario[$dia][$hora] = "<div style='background-color: $color;'>
                            <strong>$materia</strong>
                        </div>";
                    }
                }
            }
        }

        // Mostrar la tabla HTML
        echo '<table class="table table-bordered text-center">';
        echo '<thead><tr><th>Hora/Día</th>';
        foreach ($diasSemana as $dia) {
            echo "<th>$dia</th>";
        }
        echo '</tr></thead>';
        echo '<tbody>';
        
        // Generar filas de la tabla por cada hora
        foreach ($horasDia as $horaCodigo => $horaRango) {
            echo "<tr><td>$horaRango</td>";
            foreach ($diasSemana as $dia) {
                $contenidoCelda = $horario[$dia][$horaCodigo] ?: '';
                echo "<td>$contenidoCelda</td>";
            }
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

        // Función para sobrescribir el contenido de horarios.dat con un nuevo archivo
        public function subirFichero($rutaFicheroSubido) {
            // Comprobar si el archivo subido existe y es válido
            if (file_exists($rutaFicheroSubido) && is_readable($rutaFicheroSubido)) {
                // Leer el contenido del archivo subido
                $contenido = file_get_contents($rutaFicheroSubido);
    
                // Asegurarse de que la carpeta 'horarios' existe, si no, crearla
                if (!is_dir("horarios")) {
                    mkdir("horarios", 0777, true);
                }
    
                // Sobrescribir el contenido de horarios.dat con el contenido del archivo subido
                file_put_contents($this->filePath, $contenido);
    
                echo "El archivo se ha subido y el horario ha sido actualizado con éxito.";
            } else {
                // Manejo de errores si no se puede acceder al archivo subido
                echo "Error: No se pudo procesar el archivo subido. Verifique que sea válido.";
            }
        }

     // Función para generar el horario basado en el tipo proporcionado
     public function generarHorario($tipoHorario) {
        // Leer el archivo horarios.dat
        if (!file_exists($this->filePath)) {
            echo "Error: No hay datos para generar el horario.";
            return;
        }
        
        $contenido = file_get_contents($this->filePath);
        $franjas = explode('@', $contenido);
        $horario = [];

        // Definir las horas para los diferentes tipos de horario
        $horasManana = range(1, 7); // 1-7 (mañana)
        $horasTarde = range(8, 14); // 8-14 (tarde)

        // Variables para controlar las horas totales
        $totalLectivasSemana = 0;
        $totalComplementariasSemana = 0;

        foreach ($franjas as $franja) {
            $campos = explode(';', $franja);
            if (count($campos) < 7) {
                continue; // Si no tiene el formato correcto, saltarlo
            }

            list($curso, $dia, $hora, $materia, $clase, $color, $tipoFranja) = $campos;

            $horaInt = intval($hora);

            // Validaciones de franjas según tipo de horario
            if ($tipoHorario == 'mañana' && !in_array($horaInt, $horasManana)) {
                continue; // Solo incluir horas de mañana
            }

            if ($tipoHorario == 'tarde' && !in_array($horaInt, $horasTarde)) {
                continue; // Solo incluir horas de tarde
            }

            if ($tipoHorario == 'mixto' && !in_array($horaInt, array_merge($horasManana, $horasTarde))) {
                continue; // Incluir horas tanto de mañana como de tarde
            }

            // Verificar y controlar el número de horas lectivas y complementarias
            if ($tipoFranja == 'L') {
                // Contar horas lectivas
                $totalLectivasSemana++;
                if ($totalLectivasSemana > $this->HorasMáximasSemanales) {
                    echo "Error Horario: El número de horas lectivas durante la semana se ha superado.";
                    return;
                }
            } else {
                // Contar horas complementarias
                $totalComplementariasSemana++;
                if ($totalComplementariasSemana > $this->ComplementariasMáximas ) {
                    echo "Error Horario: El número de horas complementarias durante la semana se ha superado.";
                    return;
                }
            }

            // Almacenar la franja horaria para el horario generado
            $horario[$dia][$hora] = [
                'curso' => $curso,
                'materia' => $materia,
                'clase' => $clase,
                'color' => $color,
                'tipo' => $tipoFranja
            ];
        }

        // Imprimir el horario generado en formato HTML
        $this->imprimirHorario($horario, $tipoHorario);
    }

    // Función auxiliar para imprimir el horario en HTML
    private function imprimirHorario($horario, $tipoHorario) {
        $diasSemana = ['L', 'M', 'X', 'J', 'V'];
        $horas = $tipoHorario == 'mañana' ? range(1, 7) : ($tipoHorario == 'tarde' ? range(8, 14) : range(1, 14));

        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Hora/Día</th>";
        foreach ($diasSemana as $dia) {
            echo "<th>{$dia}</th>";
        }
        echo "</tr></thead><tbody>";

        foreach ($horas as $hora) {
            echo "<tr><td>{$hora}</td>";
            foreach ($diasSemana as $dia) {
                if (isset($horario[$dia][$hora])) {
                    $franja = $horario[$dia][$hora];
                    $color = $franja['color'];
                    $curso = $franja['curso'];
                    $materia = $franja['materia'];
                    $clase = $franja['clase'];
                    echo "<td style='background-color: {$color}'>{$curso} - {$materia} - {$clase}</td>";
                } else {
                    echo "<td></td>";
                }
            }
            echo "</tr>";
        }

        echo "</tbody></table>";
    }
    public function cargarhorario(){
        if (isset($_FILES['fhorario']) && $_FILES['fhorario']['error'] === UPLOAD_ERR_OK) {
            // Ruta temporal del archivo subido
            $archivoTemporal = $_FILES['fhorario']['tmp_name'];

            // Verificar si el archivo no está vacío
            if (filesize($archivoTemporal) > 0) {
                // Crear el directorio "datos_temporales" si no existe
                $directorioTemporal = '../datos_temporales';
                if (!is_dir($directorioTemporal)) {
                    mkdir($directorioTemporal, 0777, true);
                }

                // Ruta completa donde se almacenará el archivo temporal
                $rutaDestino = $directorioTemporal . '/datos_temp.dat';

                // Mover el archivo subido al directorio temporal
                if (move_uploaded_file($archivoTemporal, $rutaDestino)) {
                    try {
                        // Instanciar el gestor de horarios
                        $gestorHorario = new GestorHorario();

                        // Volcar el contenido del archivo temporal en horarios.dat
                        $gestorHorario->subirFichero($rutaDestino);

                        // Redirigir a HorariosView.php si todo salió bien
                        header("Location: ../View/HorariosView.php");
                        exit;
                    } catch (Exception $e) {
                        // Mostrar error en caso de que ocurra una excepción
                        echo $e->getMessage();
                    }
                } else {
                    echo "Error: No se pudo mover el archivo al directorio de destino.";
                }
            } else {
                echo "Error: El archivo subido está vacío.";
            }
        } else {
            echo "Error: No se ha subido ningún archivo o ha ocurrido un problema durante la carga.";
        }
    }
    }
    



