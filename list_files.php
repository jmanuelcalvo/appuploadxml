<?php
// Lee la cookie del tema o usa 'redhat' por defecto
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'redhat';

// Directorio de subida
$targetDir = __DIR__ . '/upload/';

// Aseguramos que el directorio exista
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Lógica para eliminar archivo
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

// Vista de archivo seleccionado
$viewFileContent = null;
if (isset($_GET['view'])) {
    $fileToView = basename($_GET['view']);
    $filePath = $targetDir . $fileToView;
    if (file_exists($filePath)) {
        $viewFileContent = htmlspecialchars(file_get_contents($filePath));
        $message = "Viendo archivo: " . htmlspecialchars($fileToView);
    } else {
        $message = "El archivo no existe.";
    }
}

// ---- Lógica de Paginación y Búsqueda ----
$allFiles = array_diff(scandir($targetDir), ['.', '..']);
$xmlFiles = [];

// Filtramos solo archivos XML
foreach ($allFiles as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'xml') {
        $xmlFiles[] = $file;
    }
}

// Lógica del buscador: filtra la lista de archivos si hay un término de búsqueda
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchQuery)) {
    $xmlFiles = array_filter($xmlFiles, function($file) use ($searchQuery) {
        return str_contains(strtolower($file), strtolower($searchQuery));
    });
}

// Ordenamos los archivos por fecha de modificación, del más nuevo al más viejo
array_multisort(
    array_map('filemtime', array_map(function($f) use ($targetDir) { return $targetDir . $f; }, $xmlFiles)),
    SORT_DESC,
    $xmlFiles
);

$totalFiles = count($xmlFiles);

// Cantidad de archivos por página (obtenida de la URL o valor por defecto)
$perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
$perPage = in_array($perPage, [20, 50, 100, 200]) ? $perPage : 20;

// Página actual (obtenida de la URL o valor por defecto)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Aseguramos que la página sea al menos 1

$totalPages = ceil($totalFiles / $perPage);

// Calculamos el inicio de la paginación
$offset = ($page - 1) * $perPage;

// Obtenemos los archivos para la página actual
$filesToShow = array_slice($xmlFiles, $offset, $perPage);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos cargados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?= htmlspecialchars($theme) ?>">
    <div class="container">
        <header>
            <a href="index.php">
            <img src="logo/Red_Hat_Logo_2019.svg" alt="Logo" class="logo">
            </a>
            <h1>Archivos cargados</h1>
        </header>
        <main>
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
            <?php if ($viewFileContent !== null) { ?>
                <h2><?= $message ?></h2>
                <pre><?= $viewFileContent ?></pre>
                <a href='list_files.php' class='btn'>Cerrar vista</a>
            <?php } else { ?>
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn">Buscar</button>
                </form>

                <?php if (empty($xmlFiles)) { ?>
                    <p>No hay archivos XML cargados.</p>
                <?php } else { ?>
                    <?php if (empty($filesToShow) && !empty($searchQuery)) { ?>
                        <p>No se encontraron archivos con el término de búsqueda "<?= htmlspecialchars($searchQuery) ?>".</p>
                    <?php } else { ?>
                        <div class="list-controls">
                            <p>Mostrando <?= count($filesToShow) ?> de <?= $totalFiles ?> archivos.</p>
                            <form method="GET" class="per-page-form">
                                <label for="per_page">Archivos por página:</label>
                                <select name="per_page" id="per_page" onchange="this.form.submit()">
                                    <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
                                    <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                                    <option value="200" <?= $perPage == 200 ? 'selected' : '' ?>>200</option>
                                </select>
                                <input type="hidden" name="page" value="1">
                                <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery) ?>">
                            </form>
                        </div>
        
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tamaño</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filesToShow as $file) {
                                    $filePath = $targetDir . $file; ?>
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
                            </tbody>
                        </table>
        
                        <div class="pagination">
                            <?php if ($totalPages > 1) {
                                $searchParam = !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '';
                                if ($page > 1) { ?>
                                    <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?><?= $searchParam ?>">&laquo; Anterior</a>
                                <?php }
                                for ($i = 1; $i <= $totalPages; $i++) { ?>
                                    <a href="?page=<?= $i ?>&per_page=<?= $perPage ?><?= $searchParam ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                                <?php }
                                if ($page < $totalPages) { ?>
                                    <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?><?= $searchParam ?>">Siguiente &raquo;</a>
                                <?php }
                            } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <a href="index.php" class="btn">Volver al inicio</a>
        </main>
    </div>
    <script>
        function changeTheme(theme) {
            document.body.className = '';
            if (theme === 'sri') {
                document.body.classList.add('theme-sri');
            } else {
                document.body.classList.add('theme-redhat');
            }
            document.cookie = "theme=" + theme + "; path=/; max-age=" + 60*60*24*30;
        }
    </script>
</body>
</html>
