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
 * 本软件受著作权法和国际公约的保护。未经授权，不得以任何形式或方式复制、分发、
 * 传播、展示、执行、复制、发行、或以其他方式使用本软件。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */

// 添加错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db_connect.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('无效的ID参数');
    }

    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT id, name, position, bio, qq, wechat, email, image_url, status, review_status FROM team_members WHERE id = ?");
    if (!$stmt) {
        throw new Exception('预处理语句创建失败: ' . $conn->error);
    }

    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception('执行查询失败: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('获取结果失败: ' . $stmt->error);
    }

    $member = $result->fetch_assoc();
    if (!$member) {
        throw new Exception('未找到指定ID的成员');
    }

    $stmt->close();
    
    $member = array_merge([
        'id' => '',
        'name' => '',
        'position' => '',
        'bio' => '',
        'qq' => '',
        'wechat' => '',
        'email' => '',
        'image_url' => '',
        'status' => 'normal',
        'review_status' => 'pending'
    ], $member);

    echo json_encode($member);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}