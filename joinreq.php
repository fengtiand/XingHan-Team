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
 * @since       Version 1.5.0
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

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    $verificationCode = isset($_POST['verificationCode']) ? trim($_POST['verificationCode']) : '';

    if (empty($name) || empty($email) || empty($bio) || empty($verificationCode)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM verification_codes WHERE email = ? AND code = ? AND expires_at > NOW()");
    $stmt->bind_param("ss", $email, $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'invalid_code']);
        exit;
    }
    $stmt->close();

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
        $delete_stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ?");
        $delete_stmt->bind_param("s", $email);
        $delete_stmt->execute();
        $delete_stmt->close();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();