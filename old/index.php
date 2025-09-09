<?php
// Para guardar la preferencia del tema
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'redhat';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Archivos XML</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="theme-<?= htmlspecialchars($theme) ?>">
    <div class="container">
        <header>
            <img src="red_hat_logo.svg" alt="Logo" class="logo">
            <h1>Gestor de Archivos XML</h1>
        </header>
        <main>
            <div style="margin-bottom: 2rem;">
                <label for="theme-selector">Selecciona un tema:</label>
                <select id="theme-selector" onchange="changeTheme(this.value)">
                    <option value="redhat" <?= $theme == 'redhat' ? 'selected' : '' ?>>Tipo Red Hat</option>
                    <option value="sri" <?= $theme == 'sri' ? 'selected' : '' ?>>Tipo SRI</option>
                </select>
            </div>

            <a href="upload_single.php" class="btn">Cargar archivo XML (unitario)</a>
            <a href="upload_multiple.php" class="btn">Cargar archivos XML (masivo)</a>
            <a href="list_files.php" class="btn">Visualizar archivos cargados</a>
        </main>
    </div>

    <script>
        function changeTheme(theme) {
            // Elimina la clase actual del body
            document.body.className = '';
            
            // Añade la nueva clase del tema
            if (theme === 'sri') {
                document.body.classList.add('theme-sri');
            } else {
                document.body.classList.add('theme-redhat');
            }

            // Guarda la preferencia en una cookie para recordarla en futuras visitas
            document.cookie = "theme=" + theme + "; path=/";
        }
    </script>
</body>
</html>
