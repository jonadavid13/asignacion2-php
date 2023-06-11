<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: text/html; charset=utf-8');
header('Content-Type: application/json');

$results = array();
$registros = array();

$json_array = [];
$empleado = [];
$info = [];

$numRegistros = 0;
$actual = "none";

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['Nombre']) && isset($data['Edad']) && isset($data['EstadoCivil']) && isset($data['Sexo']) && isset($data['Sueldo'])){

    // Tratamiento de datos y almacenamiento local en Arrays

    $nombre = $data['Nombre'];
    $edad = $data['Edad'];
    $edoCivil = $data['EstadoCivil'];
    $sexo = $data['Sexo'];
    $sueldo = $data['Sueldo'];

    if($sexo == "Masculino"){   // Definiendo el estado civil dependiendo del sexo
        switch ($edoCivil) {
            case "ec-1":
                $edoCivil = "Soltero";
                break;
            case "ec-2":
                $edoCivil = "Casado";
                break;
            case "ec-3":
                $edoCivil = "Viudo";
                break;
        }
    } else {
        switch ($edoCivil) {
            case "ec-1":
                $edoCivil = "Soltera";
                break;
            case "ec-2":
                $edoCivil = "Casada";
                break;
            case "ec-3":
                $edoCivil = "Viuda";
                break;
        }
    }

    switch ($sueldo) {
        case "sueldo-1":
            $sueldo = "Menos de 1000 Bs. ";
            break;
        case "sueldo-2":
            $sueldo = "Entre 1000 y 2500 Bs.";
            break;
        case "sueldo-3":
            $sueldo = "Más de 2500 Bs.";
            break;
    }

    $empleado = array(
        "nombre" => $nombre,
        "edad" => $edad,
        "estadoCivil" => $edoCivil,
        "sexo" => $sexo,
        "sueldo" => $sueldo,
    );

    // Guardar datos localmente en un archivo
    $file = fopen('data.txt', 'a');
    fwrite($file, serialize($empleado) . PHP_EOL);
    fclose($file);

    // Leer datos guardados en archivo local
    $archivo = fopen('data.txt', 'r');

    // Variables para almacenar datos solicitados
    $edadPromedioHombres = 0;
    $sumEdadesHombres = 0;
    $total_hombres = 0;
    $total_mujeres = 0;
    $hombres_casados_2500 = 0;
    $viudas_1000 = 0;

    while (($line = fgets($archivo)) !== false) { // Leer cada línea en el archivo data.txt para extraer cada registro y agregarlo al array de registros
        $contenido = unserialize($line);

        if (isset($contenido['nombre']) && isset($contenido['edad']) && isset($contenido['estadoCivil']) && isset($contenido['sexo']) && isset($contenido['sueldo'])) {
            $empleado = array(
                "nombre" => $contenido['nombre'],
                "edad" => $contenido['edad'],
                "estadoCivil" => $contenido['estadoCivil'],
                "sexo" => $contenido['sexo'],
                "sueldo" => $contenido['sueldo'],
            );

            if($empleado["sexo"] == "Femenino"){
                $total_mujeres += 1;

                if($empleado["estadoCivil"] == "Viuda" && $empleado["sueldo"] != "Menos de 1000 Bs. "){
                    $viudas_1000 += 1;
                }
            } else {
                $total_hombres += 1;
                $sumEdadesHombres += $empleado["edad"];

                if($empleado["estadoCivil"] == "Casado" && $empleado["sueldo"] == "Más de 2500 Bs."){
                    $hombres_casados_2500 += 1;
                }
            }

            array_push($registros, $empleado);
            $numRegistros += 1;
        }
    }
    if($total_hombres > 0){
        $edadPromedioHombres = $sumEdadesHombres / $total_hombres;
        $edadPromedioHombres = floor($edadPromedioHombres);
    }

    // Información de respuesta
    $info = array(
        "totalMujeres" => $total_mujeres,
        "hombresCasados2500" => $hombres_casados_2500,
        "viudasMas1000" => $viudas_1000,
        "edadesPromedioHombres" => $edadPromedioHombres,
        "totalHombres" => $total_hombres
    );
    
    // Estableciendo mensajes de respuesta
    $message = "Registro exitoso";
    
    $json_array = array(
        "status" => "success",
        "message" => $message,
        "registros" => (string)$numRegistros
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