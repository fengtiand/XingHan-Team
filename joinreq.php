<?php
/**
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
 * 本软件受著作权法和国际公约的保护。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */
require_once 'db_connect.php';
require_once 'send_mail.php';

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['email_code']) || 
    $_SESSION['email_code']['email'] !== $_POST['email'] ||
    $_SESSION['email_code']['code'] !== $_POST['verificationCode'] ||
    $_SESSION['email_code']['expire'] < time()) {

    error_log("Session email_code: " . print_r($_SESSION['email_code'] ?? 'not set', true));
    error_log("POST email: " . ($_POST['email'] ?? 'not set'));
    error_log("POST code: " . ($_POST['verificationCode'] ?? 'not set'));
    
    echo json_encode([
        'success' => false, 
        'error' => 'invalid_code',
        'message' => '验证码无效或已过期'
    ]);
    exit;
}

unset($_SESSION['email_code']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';

    if (empty($name) || empty($email) || empty($bio)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    $check_stmt = $conn->prepare("SELECT id FROM team_members WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'email_exists', 'message' => '该邮箱已经是团队成员，请使用其他邮箱申请。']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    $default_position = "待定";

    $stmt = $conn->prepare("INSERT INTO team_members (name, email, bio, position, status, review_status) VALUES (?, ?, ?, ?, 'normal', 'pending')");
    $stmt->bind_param("ssss", $name, $email, $bio, $default_position);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();