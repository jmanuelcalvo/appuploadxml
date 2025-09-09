<?php

// Definimos la carpeta donde se guardarán los archivos XML.
$uploadDir = __DIR__ . '/upload/';

// Aseguramos que la carpeta de subida exista.
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Establecemos el encabezado para que la respuesta sea JSON.
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Verificamos si la petición GET no tiene el parámetro 'file'.
if ($method === 'GET' && !isset($_GET['file'])) {
    handleListRequest($uploadDir);
} else {
    // Si la petición es POST o una GET con el parámetro 'file',
    // ejecutamos la lógica original.
    switch ($method) {
        case 'POST':
            handlePostRequest($uploadDir);
            break;
        case 'GET':
            handleGetRequest($uploadDir);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
}

/**
 * Función para manejar la subida de archivos vía POST.
 */
function handlePostRequest($uploadDir) {
    if (empty($_FILES['xmlFile'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No se encontró el archivo. Usa "xmlFile" como nombre del campo.']);
        return;
    }

    $file = $_FILES['xmlFile'];
    $fileName = basename($file['name']);
    $uploadFile = $uploadDir . $fileName;

    $fileType = mime_content_type($file['tmp_name']);
    if ($fileType !== 'application/xml' && $fileType !== 'text/xml') {
        http_response_code(415);
        echo json_encode(['error' => 'Tipo de archivo no válido. Solo se permiten archivos XML.']);
        return;
    }

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        http_response_code(201);
        echo json_encode(['message' => 'Archivo subido exitosamente.', 'file' => $fileName]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al subir el archivo.']);
    }
}

/**
 * Función para manejar la consulta de un archivo específico vía GET.
 */
function handleGetRequest($uploadDir) {
    if (empty($_GET['file'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nombre de archivo no especificado.']);
        return;
    }

    $fileName = basename($_GET['file']);
    $filePath = $uploadDir . $fileName;

    if (file_exists($filePath)) {
        http_response_code(200);
        header('Content-Type: application/xml');
        readfile($filePath);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Archivo no encontrado.']);
    }
}

/**
 * Función para manejar la petición de listado de archivos.
 */
function handleListRequest($uploadDir) {
    // Escaneamos el directorio y eliminamos '.' y '..'
    $files = array_diff(scandir($uploadDir), array('.', '..'));
    $xmlFiles = [];

    foreach ($files as $file) {
        // Obtenemos información de cada archivo
        $filePath = $uploadDir . $file;
        if (is_file($filePath)) {
            $xmlFiles[] = [
                'name' => htmlspecialchars($file),
                'size' => filesize($filePath),
                'modified' => date("Y-m-d H:i:s", filemtime($filePath))
            ];
        }
    }

    http_response_code(200);
    echo json_encode(['files' => $xmlFiles]);
}

?>
