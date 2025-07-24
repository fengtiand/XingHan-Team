<?php
require_once '../db_connect.php';
$page_title = '网站设置';
$current_page = 'site';
include 'header.php';

function handleFileUpload($file, $section) {
    if ($file['error'] == 0) {
        $upload_dir = '../uploads/' . $section . '/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = 'favicon.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return 'uploads/' . $section . '/' . $file_name;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] == 0) {
        $uploaded_path = handleFileUpload($_FILES['site_favicon'], 'favicon');
        if ($uploaded_path) {
            $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('site_favicon', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->bind_param("ss", $uploaded_path, $uploaded_path);
            $stmt->execute();
        }
    }
    $settings = [
        'site_title' => $_POST['site_title'] ?? '',
        'site_subtitle' => $_POST['site_subtitle'] ?? '',
        'site_keywords' => $_POST['site_keywords'] ?? '',
        'site_description' => $_POST['site_description'] ?? '',
        'footer_text' => $_POST['footer_text'] ?? '',
        'logo_type' => $_POST['logo_type'] ?? 'text',
        'logo_text' => $_POST['logo_text'] ?? '',
        'captcha_enabled' => isset($_POST['captcha_enabled']) ? '1' : '0',
        'contact_email' => $_POST['contact_email'] ?? '',
        'contact_qq' => $_POST['contact_qq'] ?? '',
        'contact_wechat' => $_POST['contact_wechat'] ?? '',
        'icp_number' => $_POST['icp_number'] ?? '',
        'police_number' => $_POST['police_number'] ?? '',
    ];

    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] == 0) {
        $uploaded_path = handleFileUpload($_FILES['logo_image'], 'logo');
        if ($uploaded_path) {
            $settings['logo_image'] = $uploaded_path;
        }
    } else if (isset($_POST['logo_image'])) {
        $settings['logo_image'] = $_POST['logo_image'];
    }

    if (isset($_POST['footer_link_text']) && isset($_POST['footer_link_url'])) {
        $footer_links = [];
        foreach ($_POST['footer_link_text'] as $index => $text) {
            if (!empty($text) && !empty($_POST['footer_link_url'][$index])) {
                $footer_links[] = [
                    'text' => $text,
                    'url' => $_POST['footer_link_url'][$index]
                ];
            }
        }
        $settings['footer_links'] = json_encode($footer_links);
    }

    foreach ($settings as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
    }

    $alert = '<div class="alert alert-success">设置已更新</div>';
}

$result = $conn->query("SELECT * FROM site_settings");
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
    <h2 class="content-heading">网站设置</h2>
    <?php if (isset($alert)) echo $alert; ?>

    <form action="" method="POST" enctype="multipart/form-data">

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">基本设置</h3>
            </div>
            <div class="block-content">
                <div class="mb-4">
                    <label class="form-label">网站标题</label>
                    <input type="text" class="form-control" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">网站副标题</label>
                    <input type="text" class="form-control" name="site_subtitle" value="<?php echo htmlspecialchars($settings['site_subtitle'] ?? ''); ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">网站关键词</label>
                    <input type="text" class="form-control" name="site_keywords" value="<?php echo htmlspecialchars($settings['site_keywords'] ?? ''); ?>">
                    <small class="text-muted">多个关键词用英文逗号分隔</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">网站描述</label>
                    <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">网站图标</label>
                    <div class="input-group">
                        <input type="file" class="form-control" name="site_favicon" accept=".ico,.png,.jpg,.jpeg">
                    </div>
                    <small class="text-muted">支持 ICO, PNG, JPG 格式，建议使用 32x32 或 16x16 像素的图标</small>
                    <?php if (!empty($settings['site_favicon'])): ?>
                    <div class="mt-2">
                        <img src="../<?php echo htmlspecialchars($settings['site_favicon']); ?>" alt="当前图标" style="width: 32px; height: 32px;">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label">Logo 类型</label>
                    <select class="form-select" name="logo_type">
                        <option value="text" <?php echo ($settings['logo_type'] ?? '') == 'text' ? 'selected' : ''; ?>>文字</option>
                        <option value="image" <?php echo ($settings['logo_type'] ?? '') == 'image' ? 'selected' : ''; ?>>图片</option>
                    </select>
                </div>

                <div class="mb-4 logo-text-input" <?php echo ($settings['logo_type'] ?? '') != 'text' ? 'style="display:none;"' : ''; ?>>
                    <label class="form-label">Logo 文字</label>
                    <input type="text" class="form-control" name="logo_text" value="<?php echo htmlspecialchars($settings['logo_text'] ?? ''); ?>">
                </div>

                <div class="mb-4 logo-image-input" <?php echo ($settings['logo_type'] ?? '') != 'image' ? 'style="display:none;"' : ''; ?>>
                    <label class="form-label">Logo 图片</label>
                    <div class="input-group">
                        <input type="file" class="form-control" name="logo_image" accept="image/*">
                    </div>
                    <?php if (!empty($settings['logo_image'])): ?>
                    <div class="mt-2">
                        <img src="../<?php echo htmlspecialchars($settings['logo_image']); ?>" alt="当前Logo" style="max-height: 50px;">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label">后台登录验证码</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="captcha_enabled" value="1" 
                               <?php echo ($settings['captcha_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label">启用验证码</label>
                    </div>
                    <small class="text-muted">开启后，后台登录时需要输入验证码</small>
                </div>
            </div>
        </div>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">底部设置</h3>
            </div>
            <div class="block-content">
                <div class="mb-4">
                    <label class="form-label">联系方式</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">邮箱</label>
                                <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">QQ</label>
                                <input type="text" class="form-control" name="contact_qq" value="<?php echo htmlspecialchars($settings['contact_qq'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">微信</label>
                                <input type="text" class="form-control" name="contact_wechat" value="<?php echo htmlspecialchars($settings['contact_wechat'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">页脚文本</label>
                    <textarea class="form-control" name="footer_text" rows="3"><?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?></textarea>
                    <small class="text-muted">支持HTML，可以添加版权信息等</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">ICP备案号</label>
                    <input type="text" class="form-control" name="icp_number" value="<?php echo htmlspecialchars($settings['icp_number'] ?? ''); ?>">
                    <small class="text-muted">如：京ICP备XXXXXXXX号-1</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">公安备案号</label>
                    <input type="text" class="form-control" name="police_number" value="<?php echo htmlspecialchars($settings['police_number'] ?? ''); ?>">
                    <small class="text-muted">如：京公网安备 XXXXXXXXXXXX号</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">底部链接</label>
                    <div class="row" id="footerLinks">
                        <?php
                        $footer_links = json_decode($settings['footer_links'] ?? '[]', true);
                        foreach ($footer_links as $index => $link):
                        ?>
                        <div class="col-md-6 mb-3 footer-link-item">
                            <div class="input-group">
                                <input type="text" class="form-control" name="footer_link_text[]" placeholder="链接文本" value="<?php echo htmlspecialchars($link['text']); ?>">
                                <input type="text" class="form-control" name="footer_link_url[]" placeholder="链接地址" value="<?php echo htmlspecialchars($link['url']); ?>">
                                <button type="button" class="btn btn-danger" onclick="removeFooterLink(this)">删除</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addFooterLink()">添加链接</button>
                </div>
            </div>
        </div>

        <div class="block block-rounded">
            <div class="block-content">
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">保存所有设置</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelector('select[name="logo_type"]').addEventListener('change', function() {
    document.querySelector('.logo-text-input').style.display = this.value === 'text' ? 'block' : 'none';
    document.querySelector('.logo-image-input').style.display = this.value === 'image' ? 'block' : 'none';
});

function addFooterLink() {
    const linkHtml = `
        <div class="col-md-6 mb-3 footer-link-item">
            <div class="input-group">
                <input type="text" class="form-control" name="footer_link_text[]" placeholder="链接文本">
                <input type="text" class="form-control" name="footer_link_url[]" placeholder="链接地址">
                <button type="button" class="btn btn-danger" onclick="removeFooterLink(this)">删除</button>
            </div>
        </div>
    `;
    document.getElementById('footerLinks').insertAdjacentHTML('beforeend', linkHtml);
}

function removeFooterLink(button) {
    button.closest('.footer-link-item').remove();
}
</script>

<?php include 'footer.php'; ?>