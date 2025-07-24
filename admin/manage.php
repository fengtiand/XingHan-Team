<?php
require_once '../db_connect.php';
$page_title = '网站展示管理';
$current_page = 'manage';
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
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $image_url = $_POST['image_url'];

                if (isset($_FILES['image_upload']) && $_FILES['image_upload']['name'] != '') {
                    $uploaded_path = handleFileUpload($_FILES['image_upload'], 'portfolio');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }
                
                $stmt = $conn->prepare("INSERT INTO portfolio (title, description, image_url, link, tag, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $_POST['title'], $_POST['description'], $image_url, $_POST['link'], $_POST['tag'], $_POST['status']);
                $stmt->execute();
                $stmt->close();
                break;
            case 'update':
                $image_url = $_POST['image_url'];

                if (isset($_FILES['image_upload']) && $_FILES['image_upload']['name'] != '') {
                    $uploaded_path = handleFileUpload($_FILES['image_upload'], 'portfolio');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }
                
                $stmt = $conn->prepare("UPDATE portfolio SET title = ?, description = ?, image_url = ?, link = ?, tag = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $_POST['title'], $_POST['description'], $image_url, $_POST['link'], $_POST['tag'], $_POST['status'], $_POST['id']);
                $stmt->execute();
                $stmt->close();
                break;
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM portfolio WHERE id = ?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $stmt->close();
                break;
            case 'update_status':
                $stmt = $conn->prepare("UPDATE team_members SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $_POST['status'], $_POST['id']);
                $stmt->execute();
                break;
            case 'update_member':
                $image_url = $_POST['image_url'];

                if (isset($_FILES['image_upload']) && $_FILES['image_upload']['name'] != '') {
                    $uploaded_path = handleFileUpload($_FILES['image_upload'], 'team');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }
                
                $stmt = $conn->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, qq = ?, wechat = ?, email = ?, image_url = ?, status = ?, review_status = ? WHERE id = ?");
                $stmt->bind_param("sssssssssi", $_POST['name'], $_POST['position'], $_POST['bio'], $_POST['qq'], $_POST['wechat'], $_POST['email'], $image_url, $_POST['status'], $_POST['review_status'], $_POST['id']);
                $stmt->execute();
                break;
        }
    }
}

$result = $conn->query("SELECT * FROM portfolio ORDER BY id");
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
    <h2 class="content-heading">网站展示管理</h2>
    
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">网站展示项目列表</h3>
            <div class="block-options">
                <button type="button" class="btn btn-primary" onclick="showAddProjectModal()">
                    <i class="fa fa-plus"></i> 添加新项目
                </button>
            </div>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>标题</th>
                        <th>描述</th>
                        <th>状态</th>
                        <th class="text-center" style="width: 200px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($project = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><?php echo htmlspecialchars(substr($project['description'], 0, 50)) . '...'; ?></td>
                        <td><?php echo $project['status'] == 'show' ? '展示' : '不展示'; ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editProject(<?php echo $project['id']; ?>)">编辑</button>
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这个项目吗？');">删除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProjectModalLabel">添加新项目</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProjectForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">项目图片</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image_url" id="addProjectImageUrl">
                            <input type="file" class="form-control" name="image_upload" accept="image/*">
                        </div>
                        <small class="text-muted">可以输入URL或上传新图片</small>
                        <div id="currentImage" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">链接</label>
                        <input type="text" class="form-control" name="link">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">标签</label>
                        <input type="text" class="form-control" name="tag" value="6年老品牌">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">状态</label>
                        <select class="form-select" name="status">
                            <option value="show">展示</option>
                            <option value="hide">不展示</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="submitAddForm()">添加项目</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">编辑项目</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProjectForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editProjectId">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" id="editProjectTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea class="form-control" name="description" id="editProjectDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">项目图片</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image_url" id="editProjectImageUrl">
                            <input type="file" class="form-control" name="image_upload" accept="image/*" onchange="previewImage(this, 'editImagePreview')">
                        </div>
                        <small class="text-muted">可以输入URL或上传新图片</small>
                        <div id="editImagePreview" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">链接</label>
                        <input type="text" class="form-control" name="link" id="editProjectLink">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">标签</label>
                        <input type="text" class="form-control" name="tag" id="editProjectTag">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">状态</label>
                        <select class="form-select" name="status" id="editProjectStatus">
                            <option value="show">展示</option>
                            <option value="hide">不展示</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="submitEditForm()">保存更改</button>
            </div>
        </div>
    </div>
</div>


<script>
function showAddProjectModal() {
    var myModal = new bootstrap.Modal(document.getElementById('addProjectModal'));
    myModal.show();
}

function submitAddForm() {
    document.getElementById('addProjectForm').submit();
}

function editProject(id) {
    fetch(`get_project.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editProjectId').value = data.id;
            document.getElementById('editProjectTitle').value = data.title;
            document.getElementById('editProjectDescription').value = data.description;
            document.getElementById('editProjectImageUrl').value = data.image_url;
            document.getElementById('editProjectLink').value = data.link;
            document.getElementById('editProjectTag').value = data.tag;
            document.getElementById('editProjectStatus').value = data.status;

            updateImagePreview(data.image_url, 'editImagePreview');

            var myModal = new bootstrap.Modal(document.getElementById('editProjectModal'));
            myModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('获取项目信息失败: ' + error.message);
        });
}

function submitEditForm() {
    document.getElementById('editProjectForm').submit();
}

function updateImagePreview(imageUrl, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (imageUrl) {
        const imgSrc = isValidUrl(imageUrl) ? imageUrl : '../' + imageUrl;
        container.innerHTML = `<img src="${imgSrc}" alt="预览" style="max-width: 200px; margin-top: 10px;" onerror="this.onerror=null; this.src='../assets/admin/img/preview-error.png';">`;
    } else {
        container.innerHTML = '';
    }
}

function previewImage(input, previewId) {
    const container = document.getElementById(previewId);
    if (!container) return;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            container.innerHTML = `<img src="${e.target.result}" alt="预览" style="max-width: 200px; margin-top: 10px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function isValidUrl(url) {
    try {
        return url.startsWith('http://') || url.startsWith('https://');
    } catch (e) {
        return false;
    }
}

document.getElementById('editProjectImageUrl').addEventListener('change', function() {
    updateImagePreview(this.value, 'editImagePreview');
});

document.addEventListener('DOMContentLoaded', function() {

    const addImageInput = document.querySelector('#addProjectModal input[name="image_upload"]');
    if (addImageInput) {
        addImageInput.addEventListener('change', function() {
            previewImage(this, 'addProjectPreview');
        });
    }

    const addImageUrlInput = document.querySelector('#addProjectModal input[name="image_url"]');
    if (addImageUrlInput) {
        addImageUrlInput.addEventListener('change', function() {
            updateImagePreview(this.value, 'addProjectPreview');
        });
    }

    const addModalBody = document.querySelector('#addProjectModal .modal-body form');
    if (addModalBody) {
        const previewDiv = document.createElement('div');
        previewDiv.id = 'addProjectPreview';
        previewDiv.className = 'mt-2';
        addModalBody.querySelector('.mb-3').appendChild(previewDiv);
    }
});
</script>

<?php
include 'footer.php';
?>