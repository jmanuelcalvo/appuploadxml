<?php
// Lee la cookie del tema o usa 'redhat' por defecto
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'redhat';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlFile'])) {
    $file = $_FILES['xmlFile'];
    $targetDir = __DIR__ . '/upload/';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($file['name']);
    if (strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)) === 'xml') {
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $message = "Archivo cargado con éxito.";
        } else {
            $message = "Error al subir el archivo.";
        }
    } else {
        $message = "Solo se permiten archivos XML.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar archivo XML</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?= htmlspecialchars($theme) ?>">
    <div class="container">
        <header>
            <a href="index.php">
            <img src="logo/Red_Hat_Logo_2019.svg" alt="Logo" class="logo">
            </a>
            <h1>Cargar archivo XML (unitario)</h1>
        </header>
        <main>
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="xmlFile" accept=".xml" required>
                <button type="submit" class="btn">Subir</button>
            </form>
            <a href="index.php" class="btn">Volver al inicio</a>
        </main>
    </div>
    <script>
        function changeTheme(theme) {
            document.cookie = "theme=" + theme + "; path=/; max-age=" + 60*60*24*30;
        }
    </script>
</body>
</html>
