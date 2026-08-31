<?php
$config = config('AppConfig');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>API Documentation | <?php echo $config->appName ?: "My Web App"; ?></title>
    <!-- add favicon -->
    <link rel="shortcut icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-themes@3.0.1/themes/3.x/theme-material.min.css">

    <style>
        .topbar,
        .info {
            display: none;
        }

        .swagger-ui .scheme-container {
            background-color: lightblue;
        }
    </style>
</head>

<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-standalone-preset.js"></script> <!-- Add this -->
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "<?= base_url('apidocs/spec') ?>", // Point to dynamic spec endpoint
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset // Ensure this is properly referenced
                ],
                layout: "StandaloneLayout",
                docExpansion: 'none'
            });
            window.ui = ui;
        };
    </script>
</body>

</html>