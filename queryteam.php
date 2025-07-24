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

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']); 
    
    $sql = "SELECT * FROM team_members WHERE 
            name = ? OR 
            qq = ? OR 
            wechat = ? OR 
            email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $query, $query, $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['isMember' => true]);
    } else {
        echo json_encode(['isMember' => false]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();