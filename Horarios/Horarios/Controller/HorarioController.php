<?php
require_once '../Model/GestorHorario.php';
require_once '../Model/FranjaHorario.php';

class HorarioController
{
    private $gestorHorario;

    function __construct()
    {
        $this->gestorHorario = new GestorHorario(); // Inicializa el GestorHorario

        try {
            if (isset($_POST['insertar'])) {
                try {
                    $curso = Curso::From($_POST['curso']) ?? throw new Exception("Curso inválido");
                    $dia = Semana::From($_POST['dia']) ?? throw new Exception("Día inválido");
                    $hora = Hora::From($_POST['hora']) ?? throw new Exception("Hora inválida");
                    $materia = Materia::From($_POST['materia']) ?? throw new Exception("Materia inválida");
                    $clase = Clase::From($_POST['clase']) ?? throw new Exception("Clase inválida");
                    $color = Color::From($_POST['color']) ?? throw new Exception("Color inválido");
                    $tipoFranja = TipoFranja::tryFrom($_POST['tipoFranja']) ?? throw new Exception("Tipo de franja inválido");

                    $franja = new FranjaHorario(
                        $curso,
                        $clase,
                        $materia,
                        $dia,
                        $hora,
                        $tipoFranja,
                        $color
                    );

                    $this->gestorHorario->insertarHora($franja);
                    echo "Franja horaria insertada con éxito.";
                } catch (Exception $e) {
                    echo "Error al insertar la franja horaria: " . $e->getMessage();
                }
            }

            if (isset($_POST['eliminar'])) {
                // Implementar la lógica para eliminar franjas horarias
            }

            if (isset($_POST['cargar'])) {

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

            if (isset($_POST['generar'])) {
                $tipoHorario = $_POST['tipohorario']; // Capturamos el tipo de horario seleccionado
                $gestorHorario = new GestorHorario();
                $gestorHorario->generarHorario($tipoHorario);
                // Implementar la lógica para generar horarios
            }
        } catch (Exception $e) {
            echo '<p style="color:red">Excepción: ', $e->getMessage(), "</p><br>";
        }
    }

    public function mostrarHorario()
    {
        // Llama al método mostrarHorario del GestorHorario
        $this->gestorHorario->mostrarHorario();
    }
}
