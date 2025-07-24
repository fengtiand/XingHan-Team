<?php
require_once 'db_connect.php';
require_once 'send_mail.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('无效的请求方法');
    }

    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('请输入有效的邮箱地址');
    }

    $stmt = $conn->prepare("SELECT id FROM team_members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('该邮箱已经是团队成员');
    }

    $code = sprintf("%06d", mt_rand(0, 999999));

    session_start();
    $_SESSION['email_code'] = [
        'email' => $email,
        'code' => $code,
        'expire' => time() + 600 
    ];

    $site_name_result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_title'");
    $site_name = $site_name_result->fetch_assoc()['setting_value'] ?? '星涵网络工作室';

    $subject = "【{$site_name}】验证码";
    $message = "您的验证码是：{$code}\n\n此验证码将在10分钟后失效，请尽快使用。\n\n如果这不是您的操作，请忽略此邮件。";

    send_mail($email, $subject, $message);
    echo json_encode(['success' => true, 'message' => '验证码已发送']);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} 