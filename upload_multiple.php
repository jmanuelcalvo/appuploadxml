<?php
// Lee la cookie del tema o usa 'redhat' por defecto
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'redhat';

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
<body class="theme-<?= htmlspecialchars($theme) ?>">
    <div class="container">
        <header>
            <a href="index.php">
            <img src="logo/Red_Hat_Logo_2019.svg" alt="Logo" class="logo">
            </a>
            <h1>Cargar archivos XML (masivo)</h1>
        </header>
        <main>
            <?php if (!empty($messages)) { echo "<ul>"; foreach ($messages as $msg) { echo "<li>$msg</li>"; } echo "</ul>"; } ?>
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                <input type="file" name="xmlFiles[]" accept=".xml" multiple required>
                <button type="submit" class="btn">Subir</button>
            </form>

            <div id="progressContainer" style="display: none; margin-top: 20px;">
                <div style="font-weight: bold; text-align: left;">Progreso de la carga:</div>
                <div id="progressBar" class="progress-bar"></div>
                <div id="progressText" style="text-align: right; margin-top: 5px;">0%</div>
            </div>

            <a href="index.php" class="btn">Volver al inicio</a>
        </main>
    </div>

    <script>
        const form = document.getElementById('uploadForm');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            progressContainer.style.display = 'block';

            const xhr = new XMLHttpRequest();
            const formData = new FormData(form);

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', function() {
                alert('Archivos subidos exitosamente.');
                window.location.reload(); // Recarga la página para mostrar el mensaje de éxito
            });

            xhr.open('POST', 'upload_multiple.php', true);
            xhr.send(formData);
        });
    </script>
</body>
</html>
