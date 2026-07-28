<?php

require '../vendor/autoload.php';
require_once '../conexion.php';

session_start();

if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'admin')
) {
    header("Location: ../login.php");
    exit();
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$id_nivel = $_GET['id_nivel'];
$evaluacion = $_GET['evaluacion'];

$sql = "
SELECT
personas.nombre,
personas.apellido,
calificaciones.descripcion_nota_1,
calificaciones.descripcion_nota_2,
calificaciones.nota_1,
calificaciones.nota_2,
calificaciones.nota_final,
calificaciones.observacion

FROM calificaciones

INNER JOIN estudiantes
ON calificaciones.id_estudiante = estudiantes.id_estudiante

INNER JOIN personas
ON estudiantes.id_persona = personas.id_persona

WHERE calificaciones.id_nivel = '$id_nivel'
AND calificaciones.evaluacion = '$evaluacion'

ORDER BY personas.apellido ASC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    die("No se encontraron calificaciones.");
}

$sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
$resultado_nivel = mysqli_query($conexion, $sql_nivel);
$fila_nivel = mysqli_fetch_assoc($resultado_nivel);
$nombre_nivel = $fila_nivel['nivel_academico'];

$fila_encabezado = mysqli_fetch_assoc($resultado);

$nombre_actividad_1 = "Nota 1";
$nombre_actividad_2 = "Nota 2";

if ($fila_encabezado['descripcion_nota_1'] != "") {
    $nombre_actividad_1 = ucwords(strtolower($fila_encabezado['descripcion_nota_1']));
}

if ($fila_encabezado['descripcion_nota_2'] != "") {
    $nombre_actividad_2 = ucwords(strtolower($fila_encabezado['descripcion_nota_2']));
}

$mostrar_actividad_2 = false;

if ($fila_encabezado['descripcion_nota_2'] != "") {
    $mostrar_actividad_2 = true;
}

mysqli_data_seek($resultado, 0);

$spreadsheet = new Spreadsheet();
$hoja = $spreadsheet->getActiveSheet();

$hoja->setTitle("Calificaciones");

// Insertar el logo de la institución
$ruta_logo = "../images/logo_azul.png";

if (file_exists($ruta_logo)) {

    $logo = new Drawing();

    $logo->setName("Logo");
    $logo->setDescription("Logo Iglesia Dios en Casa");
    $logo->setPath($ruta_logo);
    $logo->setHeight(80);
    $logo->setCoordinates("A1");
    $logo->setWorksheet($hoja);

}

$hoja->setCellValue("A1", "Reporte de Calificaciones");
$hoja->mergeCells("A1:F1");

$hoja->setCellValue("A2", "Nivel");
$hoja->setCellValue("B2", $nombre_nivel);

$hoja->setCellValue("A3", "Evaluación");
$hoja->setCellValue("B3", ucwords(strtolower($evaluacion)));

$hoja->setCellValue("A5", "Nombre");
$hoja->setCellValue("B5", "Apellido");
$hoja->setCellValue("C5", $nombre_actividad_1);

$columna = "D";

if ($mostrar_actividad_2 == true) {
    $hoja->setCellValue("D5", $nombre_actividad_2);
    $columna = "E";
}

$hoja->setCellValue($columna . "5", "Nota Final");

$columna_observacion = chr(ord($columna) + 1);

$hoja->setCellValue($columna_observacion . "5", "Observación");

$fila_excel = 6;

while ($fila = mysqli_fetch_assoc($resultado)) {

    $hoja->setCellValue("A" . $fila_excel, ucwords(strtolower($fila['nombre'])));
    $hoja->setCellValue("B" . $fila_excel, ucwords(strtolower($fila['apellido'])));
    $hoja->setCellValue("C" . $fila_excel, $fila['nota_1']);

    $columna_actual = "D";

    if ($mostrar_actividad_2 == true) {

        if ($fila['nota_2'] == null || $fila['nota_2'] == "") {
            $nota_2 = "No registrada";
        } else {
            $nota_2 = $fila['nota_2'];
        }

        $hoja->setCellValue("D" . $fila_excel, $nota_2);

        $columna_actual = "E";
    }

    $hoja->setCellValue($columna_actual . $fila_excel, $fila['nota_final']);

    $columna_observacion_actual = chr(ord($columna_actual) + 1);

    if ($fila['observacion'] == null || $fila['observacion'] == "") {
        $observacion = "Sin observación";
    } else {
        $observacion = ucwords(strtolower($fila['observacion']));
    }

    $hoja->setCellValue(
        $columna_observacion_actual . $fila_excel,
        $observacion
    );

    $fila_excel++;
}

$ultima_columna = $columna_observacion;

$hoja->getStyle("A1:" . $ultima_columna . "1")
    ->getFont()
    ->setBold(true)
    ->setSize(14);

$hoja->getStyle("A1:" . $ultima_columna . "1")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

$hoja->getStyle("A5:" . $ultima_columna . "5")
    ->getFont()
    ->setBold(true);

$hoja->getStyle("A5:" . $ultima_columna . ($fila_excel - 1))
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

foreach (range("A", $ultima_columna) as $letra) {
    $hoja->getColumnDimension($letra)->setAutoSize(true);
}

$nombre_archivo = "calificaciones.xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=" . $nombre_archivo);
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

exit();