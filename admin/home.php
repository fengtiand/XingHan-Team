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
 * 本软件受著作权法和国际公约的保护。未经授权，不得以任何形式或方式复制、分发、
 * 传播、展示、执行、复制、发行、或以其他方式使用本软件。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */
require_once '../db_connect.php';
$page_title = '首页内容管理';
$current_page = 'home';
include 'header.php';
function handleFileUpload($file, $section) {
    if ($file['error'] == 0) {
        $upload_dir = '../uploads/' . $section . '/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $file['name'];
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return 'uploads/' . $section . '/' . $file_name;
        }
    }
    return false;
}
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section = $_POST['section'];
    switch ($section) {
        case 'hero':
            for ($i = 0; $i < count($_POST['hero_id']); $i++) {
                $image_url = $_POST['hero_image_url'][$i];
                if (isset($_FILES['hero_image_upload']['name'][$i]) && $_FILES['hero_image_upload']['name'][$i] != '') {
                    $file = array(
                        'name' => $_FILES['hero_image_upload']['name'][$i],
                        'type' => $_FILES['hero_image_upload']['type'][$i],
                        'tmp_name' => $_FILES['hero_image_upload']['tmp_name'][$i],
                        'error' => $_FILES['hero_image_upload']['error'][$i],
                        'size' => $_FILES['hero_image_upload']['size'][$i]
                    );
                    $uploaded_path = handleFileUpload($file, 'hero');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }
                
                $stmt = $conn->prepare("UPDATE content SET title = ?, content = ?, image_url = ? WHERE id = ? AND section = 'hero'");
                $stmt->bind_param("sssi", $_POST['hero_title'][$i], $_POST['hero_content'][$i], $image_url, $_POST['hero_id'][$i]);
                $stmt->execute();
            }
            break;

        case 'about':
            $image_url = $_POST['image_url'];
            if (isset($_FILES['about_file_upload']) && $_FILES['about_file_upload']['name'] != '') {
                $uploaded_path = handleFileUpload($_FILES['about_file_upload'], 'about');
                if ($uploaded_path) {
                    $image_url = $uploaded_path;
                }
            }
            
            $stmt = $conn->prepare("UPDATE content SET title = ?, content = ?, image_url = ? WHERE section = ?");
            $stmt->bind_param("ssss", $_POST['title'], $_POST['content'], $image_url, $section);
            $stmt->execute();
            break;

        case 'services':
            $stmt = $conn->prepare("UPDATE content SET title = ?, content = ? WHERE id = ? AND section = 'services'");
            for ($i = 0; $i < count($_POST['service_id']); $i++) {
                $stmt->bind_param("ssi", $_POST['service_title'][$i], $_POST['service_content'][$i], $_POST['service_id'][$i]);
                $stmt->execute();
            }
            $stmt->close();
            break;
        case 'culture':
            $stmt = $conn->prepare("UPDATE content SET title = ?, content = ? WHERE id = ? AND section = 'culture'");
            for ($i = 0; $i < count($_POST['culture_id']); $i++) {
                $stmt->bind_param("ssi", $_POST['culture_title'][$i], $_POST['culture_content'][$i], $_POST['culture_id'][$i]);
                $stmt->execute();
            }
            $stmt->close();
            break;
        case 'environment':
            for ($i = 0; $i < count($_POST['environment_id']); $i++) {
                $image_url = $_POST['environment_image_url'][$i];
                
                if (isset($_FILES['environment_image_upload']['name'][$i]) && $_FILES['environment_image_upload']['name'][$i] != '') {
                    $file = array(
                        'name' => $_FILES['environment_image_upload']['name'][$i],
                        'type' => $_FILES['environment_image_upload']['type'][$i],
                        'tmp_name' => $_FILES['environment_image_upload']['tmp_name'][$i],
                        'error' => $_FILES['environment_image_upload']['error'][$i],
                        'size' => $_FILES['environment_image_upload']['size'][$i]
                    );
                    $uploaded_path = handleFileUpload($file, 'environment');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }
                
                $stmt = $conn->prepare("UPDATE content SET title = ?, content = ?, image_url = ? WHERE id = ? AND section = 'environment'");
                $stmt->bind_param("sssi", $_POST['environment_title'][$i], $_POST['environment_content'][$i], $image_url, $_POST['environment_id'][$i]);
                $stmt->execute();
            }
            break;
    }
    $alert = '<div class="alert alert-success">内容已更新</div>';
}

$result = $conn->query("SELECT * FROM content WHERE section IN ('hero', 'about', 'services', 'culture', 'environment') ORDER BY section, id");
$content = [];
while ($row = $result->fetch_assoc()) {
    $content[$row['section']][] = $row;
}
?>

<div class="content">
    <h2 class="content-heading">首页内容管理</h2>
    <?php if (isset($alert)) echo $alert; ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Hero 部分</h3>
                </div>
                <div class="block-content">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="section" value="hero">
                        <?php foreach ($content['hero'] as $index => $item): ?>
                        <div class="mb-4">
                            <h4>Hero <?php echo $index + 1; ?></h4>
                            <input type="hidden" name="hero_id[]" value="<?php echo $item['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">标题</label>
                                <input type="text" class="form-control" name="hero_title[]" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">内容</label>
                                <textarea class="form-control" name="hero_content[]" rows="4"><?php echo htmlspecialchars($item['content']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">图片</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="hero_image_url[]" value="<?php echo htmlspecialchars($item['image_url']); ?>">
                                    <input type="file" class="form-control" name="hero_image_upload[]" accept="image/*">
                                </div>
                                <small class="text-muted">可以输入URL或上传图片</small>
                            </div>
                            <?php if ($item['image_url']): ?>
                            <div class="mb-3">
                                <img src="<?php echo isValidUrl($item['image_url']) ? htmlspecialchars($item['image_url']) : '../' . htmlspecialchars($item['image_url']); ?>" 
                                     alt="预览" 
                                     style="max-width: 200px;"
                                     onerror="this.onerror=null; this.src='../assets/admin/img/preview-error.png';">
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">更新 Hero 部分</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">关于我们</h3>
                </div>
                <div class="block-content">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="section" value="about">
                        <?php $item = $content['about'][0]; ?>
                        <div class="mb-3">
                            <label class="form-label">标题</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">内容</label>
                            <textarea class="form-control" name="content" rows="4"><?php echo htmlspecialchars($item['content']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">媒体文件</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="image_url" value="<?php echo htmlspecialchars($item['image_url']); ?>">
                                <input type="file" class="form-control" name="about_file_upload" accept="image/*,video/mp4">
                            </div>
                            <small class="text-muted">可以输入URL或上传图片/视频(MP4)</small>
                        </div>
                        <?php if ($item['image_url']): ?>
                        <div class="mb-3">
                            <label class="form-label">当前预览</label>
                            <?php if (strpos($item['image_url'], '.mp4') !== false): ?>
                                <video width="320" height="180" controls>
                                    <source src="<?php echo isValidUrl($item['image_url']) ? htmlspecialchars($item['image_url']) : '../' . htmlspecialchars($item['image_url']); ?>" type="video/mp4">
                                    您的浏览器不支持视频播放。
                                </video>
                            <?php else: ?>
                                <img src="<?php echo isValidUrl($item['image_url']) ? htmlspecialchars($item['image_url']) : '../' . htmlspecialchars($item['image_url']); ?>" 
                                     alt="预览" 
                                     style="max-width: 200px;"
                                     onerror="this.onerror=null; this.src='../assets/admin/img/preview-error.png';">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">更新关于我们</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">服务</h3>
                </div>
                <div class="block-content">
                    <form action="" method="POST">
                        <input type="hidden" name="section" value="services">
                        <?php foreach ($content['services'] as $index => $item): ?>
                        <div class="mb-4">
                            <h4>服务 <?php echo $index + 1; ?></h4>
                            <input type="hidden" name="service_id[]" value="<?php echo $item['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">标题</label>
                                <input type="text" class="form-control" name="service_title[]" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">内容</label>
                                <textarea class="form-control" name="service_content[]" rows="4"><?php echo htmlspecialchars($item['content']); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">更新服务</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">工作室文化</h3>
                </div>
                <div class="block-content">
                    <form action="" method="POST">
                        <input type="hidden" name="section" value="culture">
                        <?php foreach ($content['culture'] as $index => $item): ?>
                        <div class="mb-4">
                            <h4>文化 <?php echo $index + 1; ?></h4>
                            <input type="hidden" name="culture_id[]" value="<?php echo $item['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">标题</label>
                                <input type="text" class="form-control" name="culture_title[]" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">内容</label>
                                <textarea class="form-control" name="culture_content[]" rows="4"><?php echo htmlspecialchars($item['content']); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">更新工作室文化</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">工作室环境</h3>
                </div>
                <div class="block-content">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="section" value="environment">
                        <?php foreach ($content['environment'] as $index => $item): ?>
                        <div class="mb-4">
                            <h4>环境 <?php echo $index + 1; ?></h4>
                            <input type="hidden" name="environment_id[]" value="<?php echo $item['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">标题</label>
                                <input type="text" class="form-control" name="environment_title[]" value="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">描述</label>
                                <textarea class="form-control" name="environment_content[]" rows="3"><?php echo htmlspecialchars($item['content']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">图片</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="environment_image_url[]" value="<?php echo htmlspecialchars($item['image_url']); ?>">
                                    <input type="file" class="form-control" name="environment_image_upload[]" accept="image/*">
                                </div>
                                <small class="text-muted">可以输入URL或上传图片</small>
                            </div>
                            <?php if ($item['image_url']): ?>
                            <div class="mb-3">
                                <img src="<?php echo isValidUrl($item['image_url']) ? htmlspecialchars($item['image_url']) : '../' . htmlspecialchars($item['image_url']); ?>" 
                                     alt="预览" 
                                     style="max-width: 200px;"
                                     onerror="this.onerror=null; this.src='../assets/admin/img/preview-error.png';">
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">更新工作室环境</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>