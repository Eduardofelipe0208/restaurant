<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrando Sesión...</title>
</head>
<body>
    <script>
        localStorage.removeItem('admin_token');
        window.location.href = 'index.php';
    </script>
</body>
</html>
