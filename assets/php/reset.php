<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: text/html; charset=utf-8');
header('Content-Type: application/json');

// $archivo = fopen('data.txt', 'r');
// $numRegistros = 0;

// while (($line = fgets($archivo)) !== false) {
//     $contenido = unserialize($line);

//     if (isset($contenido['nombre'])) {
//         $numRegistros = $numRegistros + 1;
//     }
// }

$data = file_get_contents('php://input');
$results = [];

if(isset($data)){
    if(file_exists('data.txt')){
        if(unlink('data.txt')){
            echo "Registros limpiados";
        } else {
            echo "Error al eliminar archivo";
        }
    }
} else {
    echo "Fail";
}

?>