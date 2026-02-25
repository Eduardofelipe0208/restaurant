<?php
/**
 * Admin Login Page
 * Digital Menu Restaurant PWA
 */
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container { height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .login-box { width: 100%; max-width: 400px; padding: 2.5rem; border-radius: 24px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box glass">
            <h1 style="text-align: center; margin-bottom: 2rem;">Acceso Admin</h1>
            <form id="login-form">
                <input type="text" id="username" placeholder="Usuario" required class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                <input type="password" id="password" placeholder="Contraseña" required class="btn-primary" style="background: rgba(255,255,255,0.05); text-align: left; margin-bottom: 1rem;">
                <button type="submit" class="btn-primary">Entrar</button>
            </form>
            <p id="error-msg" style="color: #ff4d4d; margin-top: 1rem; text-align: center; display: none;"></p>
        </div>
    </div>

    <script>
        document.getElementById('login-form').onsubmit = async (e) => {
            e.preventDefault();
            const response = await fetch('../api/admin/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username: document.getElementById('username').value,
                    password: document.getElementById('password').value
                })
            });
            const data = await response.json();
            if (data.status === 'success') {
                localStorage.setItem('admin_token', data.token);
                window.location.href = 'dashboard.php';
            } else {
                const err = document.getElementById('error-msg');
                err.textContent = data.message;
                err.style.display = 'block';
            }
        };
    </script>
</body>
</html>
