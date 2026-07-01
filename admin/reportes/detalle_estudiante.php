<?php

require_once '../../conexion.php';

if (!isset($_GET['id_inscripcion'])) {
    die("No se recibió la inscripción.");
}

$id_inscripcion = intval($_GET['id_inscripcion']); // Convierte el dato recibido por URL a número entero.
$estado = isset($_GET['estado']) ? $_GET['estado'] : "";
$nivel = isset($_GET['nivel']) ? $_GET['nivel'] : "";
$anio = isset($_GET['anio']) ? $_GET['anio'] : "";
$mes = isset($_GET['mes']) ? $_GET['mes'] : "";


$consulta = "
    SELECT
        personas.cedula,
        personas.nacionalidad,
        personas.nombre,
        personas.apellido,
        personas.genero,
        personas.telefono,
        personas.direccion,
        personas.contacto_emergencia,
        estudiantes.email,
        estudiantes.nivel_instruccion,
        inscripciones.nivel_academico,
        inscripciones.estado,
        inscripciones.fecha_inscripcion
    FROM inscripciones
    INNER JOIN estudiantes
        ON inscripciones.id_estudiante = estudiantes.id_estudiante
    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
    WHERE inscripciones.id_inscripcion = $id_inscripcion
";

$resultado = $conexion->query($consulta);

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

$fila = $resultado->fetch_assoc();

if (!$fila) {
    die("No se encontró información para esta inscripción.");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Estudiante</title>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafdff;
            padding: 40px;
            color: #111111;
        }

        .detalle-card {
        background: #ffffff;
        border: 1px solid #eaeaea;
        border-radius: 8px;
        padding: 30px;
        max-width: 850px;
        margin: auto;
        }
        h1 {
            margin-bottom: 25px;
        }

        .dato {
            display: flex;
            margin-bottom: 14px;
            align-items: flex-start;
        }

        .etiqueta {
            width: 240px;
            font-weight: bold;
            color: #333333;
            flex-shrink: 0;
        }

        .valor {
            flex: 1;
            word-break: break-word;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 16px;
            background-color: #111111;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-volver:hover {
            background-color: #333333;
        }
    </style>
</head>

<body>

    <div class="detalle-card">

        <h1>Detalle del Estudiante</h1>

        <div class="dato">
            <div class="etiqueta">Cédula:</div>
            <div class="valor"><?php echo htmlspecialchars($fila['cedula']); ?></div>
        </div>

        <div class="dato">
            <div class="etiqueta">Nacionalidad:</div>
            <div class="valor"><?php echo htmlspecialchars($fila['nacionalidad']); ?></div>
        </div>

        <div class="dato">
            <div class="etiqueta">Nombre:</div>
            <div class="valor"><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></div>
        </div>

        <div class="dato">
            <div class="etiqueta">Apellido:</div>
            <div class="valor"><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></div>
        </div>

        <div class="dato">
            <div class="etiqueta">Género:</div>
            <div class="valor">
                <?php
                if(!empty($fila['genero'])){
                    echo htmlspecialchars($fila['genero']);
                }else{
                    echo "No registrado";
                }
                ?>
            </div>
        </div>

        <div class="dato">
            <div class="etiqueta">Teléfono:</div>
            <div class="valor">
                <?php
                if(!empty($fila['telefono'])){
                    echo htmlspecialchars($fila['telefono']);
                }else{
                    echo "No registrado";
                }
                ?>
            </div>
        </div>

        <div class="dato">
            <div class="etiqueta">Dirección:</div>
            <div class="valor">
                <?php
                if(!empty($fila['direccion'])){
                    echo htmlspecialchars($fila['direccion']);
                }else{
                    echo "No registrada";
                }
                ?>
            </div>
        </div>

        <div class="dato">
            <div class="etiqueta">Contacto emergencia:</div>
            <div class="valor">
                <?php
                if(!empty($fila['contacto_emergencia'])){
                    echo htmlspecialchars($fila['contacto_emergencia']);
                }else{
                    echo "No registrado";
                }
                ?>
            </div>
        </div>

        <div class="dato">
            <div class="etiqueta">Correo:</div>
            <div class="valor">
                <?php
                if(!empty($fila['email'])){
                    echo htmlspecialchars($fila['email']);
                }else{
                    echo "No registrado";
                }
                ?>
            </div>
        </div>

        <div class="dato">
            <div class="etiqueta">Nivel de instrucción:</div>
            <div class="valor">
                <?php
                if(!empty($fila['nivel_instruccion'])){
                    echo htmlspecialchars($fila['nivel_instruccion']);
                }else{
                    echo "No registrado";
                }
                ?>
            </div>
        </div>

        <div class="dato">
            <div class="etiqueta">Fecha de inscripción:</div>
            <div class="valor"><?php echo htmlspecialchars($fila['fecha_inscripcion']); ?></div>
        </div>

        <div class="dato">
            <div class="etiqueta">Nivel académico:</div>
            <div class="valor"><?php echo htmlspecialchars($fila['nivel_academico']); ?></div>
        </div>

        <div class="dato">
            <div class="etiqueta">Estado:</div>
            <div class="valor">
                <?php
                if($fila['estado'] == 1){
                    echo "<span style='color:green;font-weight:bold;'>Activo</span>";
                }else{
                    echo "<span style='color:red;font-weight:bold;'>Inactivo</span>";
                }
                ?>
            </div>
        </div>

        <a href="reporte_estudiantes.php?estado=<?php echo $estado; ?>&nivel=<?php echo urlencode($nivel); ?>&anio=<?php echo $anio; ?>&mes=<?php echo $mes; ?>" class="btn-volver">
         ← Volver al reporte
</a>

    </div>

</body>
</html>