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
require_once '../send_mail.php';
$page_title = '加入申请管理';
$current_page = 'join';
include 'header.php';
$site_name_result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_title'");
$site_name = $site_name_result->fetch_assoc()['setting_value'] ?? '星涵网络工作室';
$alert = ''; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $action = $_POST['action'];
        
        if ($action == 'approve' || $action == 'reject') {
            $review_status = ($action == 'approve') ? 'approved' : 'rejected';
            $stmt = $conn->prepare("UPDATE team_members SET review_status = ? WHERE id = ?");
            $stmt->bind_param("si", $review_status, $id);
            if ($stmt->execute()) {
                $email_stmt = $conn->prepare("SELECT email, name FROM team_members WHERE id = ?");
                $email_stmt->bind_param("i", $id);
                $email_stmt->execute();
                $email_result = $email_stmt->get_result();
                $applicant = $email_result->fetch_assoc();
                $applicant_email = $applicant['email'];
                $applicant_name = $applicant['name'];
                $email_stmt->close();

                if ($action == 'approve') {
                    $subject = "您的加入申请已通过 - {$site_name}";
                    $message = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background-color: #007bff; color: #ffffff; padding: 20px; text-align: center; }
                            .content { background-color: #f8f9fa; padding: 20px; border-radius: 5px; }
                            .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #6c757d; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h1>{$site_name}</h1>
                            </div>
                            <div class='content'>
                                <h2>恭喜您，{$applicant_name}！</h2>
                                <p>您的加入申请已经通过审核。欢迎成为我们团队的一员！</p>
                                <p>我们期待与您共同努力，创造更多精彩。</p>
                                <p>如果您有任何问题，请随时与我们联系。</p>
                            </div>
                            <div class='footer'>
                                <p>&copy; " . date('Y') . " {$site_name}. 保留所有权利。</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    if (send_mail($applicant_email, $subject, $message)) {
                        $alert = '<div class="alert alert-success">申请已通过，并已发送通知邮件。</div>';
                    } else {
                        $alert = '<div class="alert alert-warning">申请已通过，但发送通知邮件失败。</div>';
                    }
                } else {
                    $alert = '<div class="alert alert-info">申请已拒绝。</div>';
                }
            } else {
                $alert = '<div class="alert alert-danger">操作失败：' . $stmt->error . '</div>';
            }
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT * FROM team_members WHERE review_status = 'pending' ORDER BY id DESC");
?>

<div class="content">
    <h2 class="content-heading">加入申请管理</h2>
    
    <?php if (!empty($alert)): ?>
        <?php echo $alert; ?>
    <?php endif; ?>

    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">申请列表</h3>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>邮箱</th>
                        <th>简介</th>
                        <!-- <th>申请时间</th> -->
                        <th class="text-center" style="width: 200px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['bio']); ?></td>
                        <!-- <td><?php echo $member['created_at']; ?></td> -->
                        <td class="text-center">
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">通过</button>
                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">拒绝</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>