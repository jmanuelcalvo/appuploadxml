<?php
$targetDir = __DIR__ . '/upload/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Eliminar archivo si se recibe la petición
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $filePath = $targetDir . $fileToDelete;
    if (file_exists($filePath)) {
        unlink($filePath);
        $message = "Archivo $fileToDelete eliminado con éxito.";
    } else {
        $message = "El archivo no existe.";
    }
}

$files = array_diff(scandir($targetDir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos cargados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Archivos cargados</h1>
        </header>
        <main>
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
            <?php if (empty($files)) { ?>
                <p>No hay archivos cargados.</p>
            <?php } else { ?>
                <table border="1" cellpadding="10" cellspacing="0" style="margin: 0 auto;">
                    <tr>
                        <th>Nombre</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($files as $file) { 
                        $filePath = $targetDir . $file;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($file) ?></td>
                            <td><?= filesize($filePath) ?> bytes</td>
                            <td><?= date("Y-m-d H:i:s", filemtime($filePath)) ?></td>
                            <td>
                                <a href="upload/<?= urlencode($file) ?>" download>Descargar</a> |
                                <a href="list_files.php?view=<?= urlencode($file) ?>">Ver</a> |
                                <a href="list_files.php?delete=<?= urlencode($file) ?>" onclick="return confirm('¿Seguro que quieres eliminar <?= htmlspecialchars($file) ?>?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
            <a href="index.php" class="btn">Volver al inicio</a>

            <?php
            // Vista de archivo seleccionado
            if (isset($_GET['view'])) {
                $fileToView = basename($_GET['view']);
                $filePath = $targetDir . $fileToView;
                if (file_exists($filePath)) {
                    echo "<h2>Viendo archivo: " . htmlspecialchars($fileToView) . "</h2>";
                    echo "<pre style='text-align:left; background:#f0f0f0; padding:1rem; border:1px solid #ccc; max-height:400px; overflow:auto;'>";
                    echo htmlspecialchars(file_get_contents($filePath));
                    echo "</pre>";
                    echo "<a href='list_files.php' class='btn'>Cerrar vista</a>";
                }
            }
            ?>
        </main>
    </div>
</body>
</html>

