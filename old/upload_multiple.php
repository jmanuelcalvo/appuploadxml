<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlFiles'])) {
    $files = $_FILES['xmlFiles'];
    $targetDir = __DIR__ . '/upload/';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $messages = [];
    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = basename($files['name'][$i]);
        $targetFile = $targetDir . $fileName;
        if (strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)) === 'xml') {
            if (move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
                $messages[] = "$fileName cargado con éxito.";
            } else {
                $messages[] = "Error al subir $fileName.";
            }
        } else {
            $messages[] = "$fileName no es un archivo XML válido.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar archivos XML</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Cargar archivos XML (masivo)</h1>
        </header>
        <main>
            <?php if (!empty($messages)) { echo "<ul>"; foreach ($messages as $msg) { echo "<li>$msg</li>"; } echo "</ul>"; } ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="xmlFiles[]" accept=".xml" multiple required>
                <button type="submit" class="btn">Subir</button>
            </form>
            <a href="index.php" class="btn">Volver al inicio</a>
        </main>
    </div>
</body>
</html>
