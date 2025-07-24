<?php
session_start();
require_once '../db_connect.php';
$result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = 'captcha_enabled'");
$captcha_enabled = $result ? ($result->fetch_assoc()['setting_value'] ?? '0') : '0';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    if ($captcha_enabled == '1' && (!isset($_SESSION['captcha']) || strtolower($captcha) != strtolower($_SESSION['captcha']))) {
        $error = "验证码错误";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: index.php');
                    exit;
                }
            }
            $error = "用户名或密码错误";
            $stmt->close();
        } else {
            $error = "系统错误，请稍后再试";
        }
    }
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!-- /**
 * =========================================================================
 * 
 *                      XingHan-Team 官网程序 
 * =========================================================================
 * 
 * @package     XingHan-Team Official Website
 * @author      XingHan Development Team
 * @copyright   Copyright (c) 2024, XingHan-Team
 * @link        https://www.ococn.cn
 * @since       Version 1.0.0
 * @filesource  By 奉天
 * 
 * =========================================================================
 * 
 * XingHan-Team 星涵网络工作室官方网站管理系统
 * 版权所有 (C) 2024 XingHan-Team。保留所有权利。
 * 
 * 本软件受著作权法和国际公约的保护。未经授权，不得以任何形式或方式复制、分发、
 * 传播、展示、执行、复制、发行、或以其他方式使用本软件。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */ -->
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <link rel="stylesheet" href="../assets/admin/css/oneui.min-5.6.css">
    <style>
        .bg-login {
            background: linear-gradient(135deg, #6c76f4 0%, #4b55e8 100%);
        }

        .login-container {
            max-width: 420px;
            margin: 0 auto;
            padding: 2rem;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
        }

        .form-control:focus {
            border-color: #4b55e8;
            box-shadow: 0 0 0 0.2rem rgba(75, 85, 232, 0.25);
        }

        .btn-login {
            background: #4b55e8;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #3640d6;
            transform: translateY(-1px);
        }

        .captcha-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .captcha-input {
            flex: 1;
        }

        .captcha-image {
            height: 42px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="page-container">
        <main id="main-container">
            <div class="hero-static d-flex align-items-center">
                <div class="w-100">
                    <div class="bg-login min-vh-100 d-flex align-items-center">
                        <div class="login-container">
                            <div class="login-form">
                                <h2 class="login-title">管理员登录</h2>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>
                                <form class="js-validation-signin" action="" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <div class="py-3">
                                        <div class="mb-4">
                                            <input type="text" class="form-control form-control-lg form-control-alt" id="username" name="username" placeholder="用户名" required>
                                        </div>
                                        <div class="mb-4">
                                            <input type="password" class="form-control form-control-lg form-control-alt" id="password" name="password" placeholder="密码" required>
                                        </div>
                                        <?php if ($captcha_enabled == '1'): ?>
                                        <div class="mb-4 captcha-container">
                                            <input type="text" class="form-control form-control-lg form-control-alt captcha-input" id="captcha" name="captcha" placeholder="验证码" required>
                                            <img src="generate_captcha.php" alt="验证码" onclick="this.src='generate_captcha.php?'+Math.random();" class="captcha-image">
                                        </div>
                                        <?php endif; ?>
                                        <div class="remember-me">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">记住我</label>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-lg btn-alt-primary w-100">
                                            登录
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/admin/js/oneui.app.min-5.6.js"></script>
</body>
</html>
