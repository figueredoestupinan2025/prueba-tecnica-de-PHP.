<?php
/**
 * CONTODA - Sistema de Facturación
 * Login
 */

session_start();

// If already logged in, redirect to index
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$message = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Simple authentication (in production, use database)
    // For demo: admin / admin123
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: index.php");
        exit;
    } elseif ($username === 'contoda' && $password === 'contoda123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CONTODA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #1f2937 100%);
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 40px 30px 30px;
            text-align: center;
            color: white;
        }
        
        .login-header .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 36px;
        }
        
        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        .input-group-text {
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #6b7280;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.5);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me input {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }
        
        .remember-me label {
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 13px;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 13px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .divider span {
            padding: 0 15px;
        }
        
        .demo-credentials {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 15px;
            font-size: 13px;
            color: #6b7280;
        }
        
        .demo-credentials strong {
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-store"></i>
                </div>
                <h1>CONTODA</h1>
                <p>Sistema de Facturación</p>
            </div>
            
            <div class="login-body">
                <?php if ($message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Usuario
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Ingrese su usuario" required autocomplete="username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Ingrese su contraseña" required autocomplete="current-password">
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Recordarme</label>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </form>
                
                <div class="divider">
                    <span>Información</span>
                </div>
                
                <div class="demo-credentials">
                    <strong>Credenciales de demostración:</strong><br>
                    Usuario: <code>admin</code> | Contraseña: <code>admin123</code><br>
                    O: <code>contoda</code> | <code>contoda123</code>
                </div>
            </div>
            
            <div class="login-footer">
                &copy; <?php echo date('Y'); ?> CONTODA - Sistema de Facturación
            </div>
        </div>
    </div>
</body>
</html>
