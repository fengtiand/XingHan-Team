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
$page_title = '首页内容管理';
$current_page = 'home';
include 'header.php';

// 处理文件上传的函数
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

    if (isset($_POST['section'])) {
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
}


if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    $section = $_POST['delete_section'];
 
    $stmt = $conn->prepare("SELECT image_url FROM content WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['image_url'] && !isValidUrl($row['image_url'])) {
            $file_path = '../' . $row['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    $stmt = $conn->prepare("DELETE FROM content WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $section = $_POST['add_section'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $uploaded_path = handleFileUpload($_FILES['image'], $section);
        if ($uploaded_path) {
            $image_url = $uploaded_path;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO content (section, title, content, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $section, $title, $content, $image_url);
    if ($stmt->execute()) {
        $alert = '<div class="alert alert-success">新内容已添加</div>';
    } else {
        $alert = '<div class="alert alert-danger">添加失败: ' . $conn->error . '</div>';
    }
}

$result = $conn->query("SELECT * FROM content WHERE section IN ('hero', 'about', 'services', 'culture', 'environment') ORDER BY section, id");
$content = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (!isset($content[$row['section']])) {
            $content[$row['section']] = [];
        }
        $content[$row['section']][] = $row;
    }
}

if (!$result) {
    echo "查询错误: " . $conn->error;
}

?>

<div class="content">
    <h2 class="content-heading">首页内容管理</h2>
    <?php if (isset($alert)) echo $alert; ?>
    
    <div class="block block-rounded">
        <ul class="nav nav-tabs nav-tabs-block" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero-pane" role="tab" aria-controls="hero-pane" aria-selected="true">Hero 部分</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about-pane" role="tab" aria-controls="about-pane" aria-selected="false">关于我们</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-pane" role="tab" aria-controls="services-pane" aria-selected="false">服务</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" id="culture-tab" data-bs-toggle="tab" data-bs-target="#culture-pane" role="tab" aria-controls="culture-pane" aria-selected="false">工作室文化</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" id="environment-tab" data-bs-toggle="tab" data-bs-target="#environment-pane" role="tab" aria-controls="environment-pane" aria-selected="false">工作室环境</button>
            </li>
        </ul>
        <div class="block-content tab-content">
            <!-- Hero Tab Pane -->
            <div class="tab-pane active" id="hero-pane" role="tabpanel" aria-labelledby="hero-tab" tabindex="0">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="section" value="hero">
                    <?php if (isset($content['hero']) && is_array($content['hero'])): ?>
                        <?php foreach ($content['hero'] as $index => $item): ?>
                    <div class="mb-4">
                        <h4>Hero <?php echo $index + 1; ?> 
                            <button type="button" class="btn btn-danger btn-sm float-end delete-item" 
                                    data-id="<?php echo $item['id']; ?>" 
                                    data-section="hero">
                                删除
                            </button>
                        </h4>
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
                    <?php endif; ?>
                    <div class="mb-4">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addHeroModal">
                            添加新Hero
                        </button>
                        <button type="submit" class="btn btn-primary">更新 Hero 部分</button>
                    </div>
                </form>
            </div>
            <!-- About Tab Pane -->
            <div class="tab-pane" id="about-pane" role="tabpanel" aria-labelledby="about-tab" tabindex="0">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="section" value="about">
                    <?php $item = $content['about'][0] ?? null; ?>
                    <?php if ($item): ?>
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
                    <?php else: ?>
                    <div class="alert alert-info">此部分内容尚未创建。</div>
                    <?php endif; ?>
                </form>
            </div>
            <!-- Services Tab Pane -->
            <div class="tab-pane" id="services-pane" role="tabpanel" aria-labelledby="services-tab" tabindex="0">
                <form action="" method="POST">
                    <input type="hidden" name="section" value="services">
                    <?php if (isset($content['services'])): foreach ($content['services'] as $index => $item): ?>
                    <div class="mb-4">
                        <h4>服务 <?php echo $index + 1; ?> 
                            <button type="button" class="btn btn-danger btn-sm float-end delete-item" 
                                    data-id="<?php echo $item['id']; ?>" 
                                    data-section="services">
                                删除
                            </button>
                        </h4>
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
                    <?php endforeach; endif; ?>
                    <div class="mb-4">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                            添加新服务
                        </button>
                        <button type="submit" class="btn btn-primary">更新服务</button>
                    </div>
                </form>
            </div>
            <!-- Culture Tab Pane -->
            <div class="tab-pane" id="culture-pane" role="tabpanel" aria-labelledby="culture-tab" tabindex="0">
                <form action="" method="POST">
                    <input type="hidden" name="section" value="culture">
                    <?php if (isset($content['culture'])): foreach ($content['culture'] as $index => $item): ?>
                    <div class="mb-4">
                        <h4>文化 <?php echo $index + 1; ?> 
                            <button type="button" class="btn btn-danger btn-sm float-end delete-item" 
                                    data-id="<?php echo $item['id']; ?>" 
                                    data-section="culture">
                                删除
                            </button>
                        </h4>
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
                    <?php endforeach; endif; ?>
                    <div class="mb-4">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCultureModal">
                            添加新文化
                        </button>
                        <button type="submit" class="btn btn-primary">更新工作室文化</button>
                    </div>
                </form>
            </div>
            <!-- Environment Tab Pane -->
            <div class="tab-pane" id="environment-pane" role="tabpanel" aria-labelledby="environment-tab" tabindex="0">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="section" value="environment">
                    <?php if (isset($content['environment'])): foreach ($content['environment'] as $index => $item): ?>
                    <div class="mb-4">
                        <h4>环境 <?php echo $index + 1; ?> 
                            <button type="button" class="btn btn-danger btn-sm float-end delete-item" 
                                    data-id="<?php echo $item['id']; ?>" 
                                    data-section="environment">
                                删除
                            </button>
                        </h4>
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
                    <?php endforeach; endif; ?>
                    <div class="mb-4">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEnvironmentModal">
                            添加新环境
                        </button>
                        <button type="submit" class="btn btn-primary">更新工作室环境</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addHeroModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加新Hero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="add_section" value="hero">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">内容</label>
                        <textarea class="form-control" name="content" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">图片</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加新服务</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="add_section" value="services">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">内容</label>
                        <textarea class="form-control" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addCultureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加新文化</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="add_section" value="culture">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">内容</label>
                        <textarea class="form-control" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addEnvironmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加新环境</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="add_section" value="environment">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea class="form-control" name="content" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">图片</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.delete-item').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('确定要删除这个项目吗？')) {
                const id = this.dataset.id;
                const section = this.dataset.section;
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${id}&delete_section=${section}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('删除失败：' + data.error);
                    }
                });
            }
        });
    });
});
</script>

<?php
include 'footer.php';
?>