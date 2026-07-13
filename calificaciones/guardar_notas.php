<?php   
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    die("Acceso no permitido");
}

$id_nivel = $_POST['id_nivel'];
$evaluacion = trim($_POST['evaluacion']); // trim() elimina espacios sobrantes al inicio y al final.

$notas_1 = $_POST['nota_1'];
$notas_2 = $_POST['nota_2'];
$notas_finales = $_POST['nota_final'];
$observaciones = $_POST['observacion'];

        if (empty($evaluacion)) {
            die("Debe escribir el nombre de la evaluación.");
        }

        foreach ($notas_1 as $id_estudiante => $nota_1) {

            // Obtener Nota 2

            if (isset($notas_2[$id_estudiante])) {

                 if ($notas_2[$id_estudiante] == "") {

                $nota_2 = null;

            } else {

                $nota_2 = $notas_2[$id_estudiante];
            }

            } else {
                   $nota_2 = null;
            }

            // Obtener Nota Final

            if (isset($notas_finales[$id_estudiante])) {
                $nota_final = $notas_finales[$id_estudiante];
            } else {
                $nota_final = null;
            }

            // Obtener Observación

            if (isset($observaciones[$id_estudiante])) {
                $observacion = trim($observaciones[$id_estudiante]);
            } else {
                $observacion = "";
            }
    
            // Validar Nota 1

            if ($nota_1 === "" || !is_numeric($nota_1) || $nota_1 < 0 || $nota_1 > 20) {
                die("La Nota 1 del estudiante con ID $id_estudiante debe estar entre 0 y 20.");
            }

            // Validar nota 2 (solo si fue escrita)

                if ($nota_2 !== null) {
                    if (!is_numeric($nota_2) || $nota_2 < 0 || $nota_2 > 20) {
                    die("La Nota 2 del estudiante con ID $id_estudiante debe estar entre 0 y 20.");
                }
            }

            // Validar Nota Final

     if ($nota_final === null || $nota_final === "" || !is_numeric($nota_final) || $nota_final < 0 || $nota_final > 20) {
            die("La Nota Final del estudiante con ID $id_estudiante debe estar entre 0 y 20.");
    }
        
        // Preparar el valor de Nota 2 para la consulta
            if ($nota_2 === null) {

                $valor_nota_2 = "NULL";

            } else {

                $valor_nota_2 = "'$nota_2'";

            }
        
        // Consulta

            $sql_guardar = "
            INSERT INTO calificaciones
            (id_estudiante, id_nivel, evaluacion, nota_1, nota_2, nota_final, observacion)

            VALUES
            ('$id_estudiante',
            '$id_nivel',
            '$evaluacion',
            '$nota_1',
            $valor_nota_2,
            '$nota_final',
            '$observacion')
            ";
      // Ejecutamos   
    $resultado_guardar = mysqli_query($conexion, $sql_guardar);

        if (!$resultado_guardar){
            die ("Error al guardar las notas: " .mysqli_error($conexion));
        }
} // todo está dentro del foreach.Porque cada vuelta del foreach trabaja con un estudiante diferente.Por eso esas variables no pueden estar fuera del foreach, ya que cambiarán en cada estudiante.

   header("Location: index.php?guardado=1");// Regresa nuevamente a index.php."
    exit();