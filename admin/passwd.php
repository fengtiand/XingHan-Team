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
require_once '../db_connect.php';
$page_title = '修改密码';
$current_page = 'passwd';
include 'header.php';
$alert = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $alert = '<div class="alert alert-danger">新密码和确认密码不匹配</div>';
    } else {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
        if ($stmt === false) {
            $alert = '<div class="alert alert-danger">准备语句失败: ' . $conn->error . '</div>';
        } else {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                if ($update_stmt === false) {
                    $alert = '<div class="alert alert-danger">准备更新语句失败: ' . $conn->error . '</div>';
                } else {
                    $update_stmt->bind_param("si", $hashed_password, $user_id);
                    if ($update_stmt->execute()) {
                        $alert = '<div class="alert alert-success">密码已成功更新</div>';
                    } else {
                        $alert = '<div class="alert alert-danger">更新密码失败: ' . $update_stmt->error . '</div>';
                    }
                    $update_stmt->close();
                }
            } else {
                $alert = '<div class="alert alert-danger">当前密码不正确</div>';
            }
            $stmt->close();
        }
    }
}
?>

<div class="content">
    <h2 class="content-heading">修改密码</h2>
    
    <?php echo $alert; ?>

    <div class="block block-rounded">
        <div class="block-content">
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="form-label" for="current_password">当前密码</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="new_password">新密码</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="confirm_password">确认新密码</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">更改密码</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>