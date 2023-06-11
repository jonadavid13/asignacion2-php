<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: text/html; charset=utf-8');
header('Content-Type: application/json');

function promedio($array){
    $sumatoria = 0;
    $promedio = 0;

    if(count($array) > 0){
        foreach($array as $nota){
            $sumatoria += $nota;
        }
        $promedio = $sumatoria / count($array);
    } else {
        return "N/A";
    }

    return number_format($promedio, 1);
}

$results = array();
$registros = array();

$json_array = [];
$estudiante = [];
$info = [];

$numRegistros = 0;
$actual = "none";

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Nombre']) && isset($data['Cedula']) && isset($data['notaMatematicas']) && isset($data['notaFisica']) && isset($data['notaProgramacion'])){

    // Tratamiento de datos y almacenamiento local en Arrays

    $nombre = $data['Nombre'];
    $cedula = $data['Cedula'];
    $notaMatematicas = $data['notaMatematicas'];
    $notaFisica = $data['notaFisica'];
    $notaProgramacion = $data['notaProgramacion'];

    $estudiante = array(
        "nombre" => $nombre,
        "cedula" => $cedula,
        "notaMatematicas" => $notaMatematicas,
        "notaFisica" => $notaFisica,
        "notaProgramacion" => $notaProgramacion,
    );

    // Guardar datos localmente en un archivo
    $file = fopen('data.txt', 'a');
    fwrite($file, serialize($estudiante) . PHP_EOL);
    fclose($file);

    // Leer datos guardados en archivo local
    $archivo = fopen('data.txt', 'r');

    // Variables para almacenar datos solicitados
    $cantidadAlumnos = 0;
    $aprobaronTodas = 0;
    $aprobaronUna = 0;
    $aprobaronDos = 0;

    // Matemáticas
    $notasMath = array();
    $aprobadosMath = 0;
    $aplazadosMath = 0;
    $notaMaxMath = 0;
    
    // Física
    $notasFisica = array();
    $aprobadosFisica = 0;
    $aplazadosFisica = 0;
    $notaMaxFisica = 0;
    
    // Programación
    $notasProg = array();
    $aprobadosProg = 0;
    $aplazadosProg = 0;
    $notaMaxProg = 0;
    
    while (($line = fgets($archivo)) !== false) { // Leer cada línea en el archivo data.txt para extraer cada registro y agregarlo al array de registros
        $contenido = unserialize($line);
        
        if (isset($contenido['nombre']) && isset($contenido['cedula']) && isset($contenido['notaMatematicas']) && isset($contenido['notaFisica']) && isset($contenido['notaProgramacion'])) {
            $materiasAprobadas = 0;

            $estudiante = array(
                "nombre" => $contenido['nombre'],
                "cedula" => $contenido['cedula'],
                "notaMatematicas" => $contenido['notaMatematicas'],
                "notaFisica" => $contenido['notaFisica'],
                "notaProgramacion" => $contenido['notaProgramacion'],
            );

            // Procedimientos para cada materia
            array_push($notasMath, $estudiante['notaMatematicas']);
            if($estudiante['notaMatematicas'] > $notaMaxMath){
                $notaMaxMath = $estudiante['notaMatematicas'];
            }
            if($estudiante['notaMatematicas'] >= 10){
                $aprobadosMath += 1;
                $materiasAprobadas += 1;
            } else {
                $aplazadosMath += 1;
            }

            array_push($notasFisica, $estudiante['notaFisica']);
            if($estudiante['notaFisica'] > $notaMaxFisica){
                $notaMaxFisica = $estudiante['notaFisica'];
            }
            if($estudiante['notaFisica'] >= 10){
                $aprobadosFisica += 1;
                $materiasAprobadas += 1;
            } else {
                $aplazadosFisica += 1;
            }

            array_push($notasProg, $estudiante['notaProgramacion']);
            if($estudiante['notaProgramacion'] > $notaMaxProg){
                $notaMaxProg = $estudiante['notaProgramacion'];
            }
            if($estudiante['notaProgramacion'] >= 10){
                $aprobadosProg += 1;
                $materiasAprobadas += 1;
            } else {
                $aplazadosProg += 1;
            }

            if($materiasAprobadas > 0){
                switch($materiasAprobadas){
                    case 1:
                        $aprobaronUna += 1;
                        break;
                    case 2:
                        $aprobaronDos += 1;
                        break;
                    case 3:
                        $aprobaronTodas += 1;
                        break;
                }
            }

            array_push($registros, $estudiante);
            $cantidadAlumnos += 1;
        }
    }
    $promedioMath = promedio($notasMath);
    $promedioFisica = promedio($notasFisica);
    $promedioProg = promedio($notasProg);

    if($materiasAprobadas > 0){}

    // Información de respuesta
    $info = array(
        "promedioMatematicas" => $promedioMath,
        "promedioFisica" => $promedioFisica,
        "promedioProgramacion" => $promedioProg,
        "aprobadosMatematicas" => $aprobadosMath,
        "aprobadosFisica" => $aprobadosFisica,
        "aprobadosProgramacion" => $aprobadosProg,
        "aplazadosMatematicas" => $aplazadosMath,
        "aplazadosFisica" => $aplazadosFisica,
        "aplazadosProg" => $aplazadosProg,
        "notaMaxMatematicas" => $notaMaxMath,
        "notaMaxFisica" => $notaMaxFisica,
        "notaMaxProgramacion" => $notaMaxProg,
        "aprobaronTodas" => $aprobaronTodas,
        "aprobaronUna" => $aprobaronUna,
        "aprobaronDos" => $aprobaronDos,
    );
    
    // Estableciendo mensajes de respuesta
    $message = "Registro exitoso";
    
    $json_array = array(
        "status" => "success",
        "message" => $message,
        "registros" => (string)$cantidadAlumnos
    );
    
    array_push($results, $json_array);
    array_push($results, $registros);
    array_push($results, $info);
} else {
    $json_array = array(
        "status" => "error",
        "message" => "Error con los datos"
    );
    array_push($results, $json_array);
}

echo json_encode($results);
?>