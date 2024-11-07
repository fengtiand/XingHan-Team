
<?php
require_once '../db_connect.php';
require_once '../send_mail.php';
$page_title = '邮箱发信设置';
$current_page = 'mailset';
include 'header.php';
$alert = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'save_settings') {
        $smtp_host = $_POST['smtp_host'];
        $smtp_port = $_POST['smtp_port'];
        $smtp_user = $_POST['smtp_user'];
        $smtp_pass = $_POST['smtp_pass'];
        $from_email = $_POST['from_email'];
        $from_name = $_POST['from_name'];
        $smtp_secure = $_POST['smtp_secure'];
        $stmt = $conn->prepare("REPLACE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
        $settings = [
            'smtp_host' => $smtp_host,
            'smtp_port' => $smtp_port,
            'smtp_user' => $smtp_user,
            'smtp_pass' => $smtp_pass,
            'from_email' => $from_email,
            'from_name' => $from_name,
            'smtp_secure' => $smtp_secure
        ];

        foreach ($settings as $key => $value) {
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }

        $stmt->close();
        $alert = '<div class="alert alert-success">设置已更新</div>';
    } elseif (isset($_POST['action']) && $_POST['action'] == 'test_email') {
        $to = $_POST['test_email'];
        $subject = "测试邮件";
        $message = "这是一封测试邮件，如果您收到了这封邮件，说明邮件发送功能正常工作。";

        $result = send_mail($to, $subject, $message);
        if ($result === true) {
            $alert = '<div class="alert alert-success">测试邮件发送成功</div>';
        } else {
            $alert = '<div class="alert alert-danger">测试邮件发送失败: ' . $result . '</div>';
        }
    }
}
$result = $conn->query("SELECT * FROM site_settings WHERE setting_key IN ('smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'from_email', 'from_name', 'smtp_secure')");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
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
<div class="content">
    <h2 class="content-heading">邮箱发信设置</h2>
    
    <?php if (!empty($alert)): ?>
    <div class="block block-rounded">
        <div class="block-content">
            <?php echo $alert; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">SMTP 设置</h3>
        </div>
        <div class="block-content">
            <form action="" method="POST">
                <input type="hidden" name="action" value="save_settings">
                <div class="mb-4">
                    <label class="form-label" for="smtp_host">SMTP 服务器</label>
                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="smtp_port">SMTP 端口</label>
                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? ''); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="smtp_secure">加密方式</label>
                    <select class="form-select" id="smtp_secure" name="smtp_secure">
                        <option value="tls" <?php echo ($settings['smtp_secure'] ?? '') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($settings['smtp_secure'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        <option value="none" <?php echo ($settings['smtp_secure'] ?? '') == 'none' ? 'selected' : ''; ?>>无加密</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="smtp_user">SMTP 用户名</label>
                    <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="smtp_pass">SMTP 密码</label>
                    <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="from_email">发件人邮箱</label>
                    <input type="email" class="form-control" id="from_email" name="from_email" value="<?php echo htmlspecialchars($settings['from_email'] ?? ''); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="from_name">发件人名称</label>
                    <input type="text" class="form-control" id="from_name" name="from_name" value="<?php echo htmlspecialchars($settings['from_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">保存设置</button>
                </div>
            </form>
        </div>
    </div>

    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">测试发信</h3>
        </div>
        <div class="block-content">
            <form action="" method="POST">
                <input type="hidden" name="action" value="test_email">
                <div class="mb-4">
                    <label class="form-label" for="test_email">测试邮箱地址</label>
                    <input type="email" class="form-control" id="test_email" name="test_email" required>
                </div>
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">发送测试邮件</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>