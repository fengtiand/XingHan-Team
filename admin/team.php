<?php
require_once '../db_connect.php';
$page_title = '团队成员管理';
$current_page = 'team';
include 'header.php';

function handleFileUpload($file, $section, $wechat = '') {
    if ($file['error'] == 0) {
        $upload_dir = '../uploads/' . $section . '/';
        if ($section === 'qrcodes') {
            $upload_dir = '../qrcodes/';
            $file_name = $wechat . '.jpg'; 
        } else {
            $file_name = time() . '_' . $file['name'];
        }
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return $section === 'qrcodes' ? $file_name : 'uploads/' . $section . '/' . $file_name;
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
                    $uploaded_path = handleFileUpload($_FILES['image_upload'], 'team');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }

                if (isset($_FILES['qrcode_upload']) && $_FILES['qrcode_upload']['name'] != '' && !empty($_POST['wechat'])) {
                    handleFileUpload($_FILES['qrcode_upload'], 'qrcodes', $_POST['wechat']);
                }
                
                $stmt = $conn->prepare("INSERT INTO team_members (name, position, bio, qq, wechat, email, image_url, status, review_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", 
                    $_POST['name'], 
                    $_POST['position'], 
                    $_POST['bio'], 
                    $_POST['qq'], 
                    $_POST['wechat'], 
                    $_POST['email'], 
                    $image_url,
                    $_POST['status'],
                    $_POST['review_status']
                );
                $stmt->execute();
                break;

            case 'edit':
                $image_url = $_POST['image_url'];

                if (isset($_FILES['image_upload']) && $_FILES['image_upload']['name'] != '') {
                    $uploaded_path = handleFileUpload($_FILES['image_upload'], 'team');
                    if ($uploaded_path) {
                        $image_url = $uploaded_path;
                    }
                }
                
                if (isset($_FILES['qrcode_upload']) && $_FILES['qrcode_upload']['name'] != '' && !empty($_POST['wechat'])) {
                    handleFileUpload($_FILES['qrcode_upload'], 'qrcodes', $_POST['wechat']);
                }
                
                $stmt = $conn->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, qq = ?, wechat = ?, email = ?, image_url = ?, status = ?, review_status = ? WHERE id = ?");
                $stmt->bind_param("sssssssssi", 
                    $_POST['name'], 
                    $_POST['position'], 
                    $_POST['bio'], 
                    $_POST['qq'], 
                    $_POST['wechat'], 
                    $_POST['email'], 
                    $image_url,
                    $_POST['status'],
                    $_POST['review_status'],
                    $_POST['id']
                );
                $stmt->execute();
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                break;
        }
        
        // 添加操作反馈
        $alert = '<div class="alert alert-success">操作成功完成</div>';
    }
}

$result = $conn->query("SELECT * FROM team_members ORDER BY id");
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
    <h2 class="content-heading">团队成员管理</h2>
    
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">团队成员列表</h3>
            <div class="block-options">
                <button type="button" class="btn btn-primary" onclick="showAddMemberModal()">
                    <i class="fa fa-plus"></i> 添加新成员
                </button>
            </div>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>职位</th>
                        <th>状态</th>
                        <th>审核状态</th>
                        <th class="text-center" style="width: 200px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['position']); ?></td>
                        <td><?php echo $member['status'] == 'normal' ? '正常' : '暂停'; ?></td>
                        <td><?php 
                            switch($member['review_status']) {
                                case 'approved':
                                    echo '通过';
                                    break;
                                case 'pending':
                                    echo '待审核';
                                    break;
                                case 'rejected':
                                    echo '不通过';
                                    break;
                            }
                        ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editMember(<?php echo $member['id']; ?>)">编辑</button>
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这个成员吗？');">删除</button>
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
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">添加新成员</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">姓名</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">职位</label>
                        <input type="text" class="form-control" name="position" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">简介</label>
                        <textarea class="form-control" name="bio" rows="4" placeholder="请输入成员简介"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">头像</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image_url" placeholder="输入图片URL或上传图片">
                            <input type="file" class="form-control" name="image_upload" accept="image/*">
                        </div>
                        <small class="text-muted">可以输入URL或上传图片</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">QQ</label>
                        <input type="text" class="form-control" name="qq">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">微信</label>
                        <input type="text" class="form-control" name="wechat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">邮箱</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">状态</label>
                        <select class="form-select" name="status">
                            <option value="normal">正常</option>
                            <option value="paused">暂停</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">审核状态</label>
                        <select class="form-select" name="review_status">
                            <option value="approved">通过</option>
                            <option value="pending">待审核</option>
                            <option value="rejected">不通过</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">微信二维码</label>
                        <div class="input-group">
                            <input type="file" class="form-control" name="qrcode_upload" accept="image/*">
                        </div>
                        <small class="text-muted">上传微信二维码图片</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="submitAddForm()">添加成员</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-labelledby="editMemberModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">编辑成员信息</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editMemberId">
                    <div class="mb-3">
                        <label class="form-label">姓名</label>
                        <input type="text" class="form-control" name="name" id="editMemberName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">职位</label>
                        <input type="text" class="form-control" name="position" id="editMemberPosition" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">简介 <small class="text-muted">(支持emoji表情 😊)</small></label>
                        <textarea class="form-control" name="bio" id="editMemberBio" rows="4" placeholder="请输入成员简介，支持emoji表情 😊🎉💪"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">头像</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image_url" id="editMemberImageUrl">
                            <input type="file" class="form-control" name="image_upload" accept="image/*">
                        </div>
                        <small class="text-muted">可以输入URL或上传新图片</small>
                        <div id="currentImage" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">QQ</label>
                        <input type="text" class="form-control" name="qq" id="editMemberQQ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">微信</label>
                        <input type="text" class="form-control" name="wechat" id="editMemberWechat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">邮箱</label>
                        <input type="email" class="form-control" name="email" id="editMemberEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">状态</label>
                        <select class="form-select" name="status" id="editMemberStatus">
                            <option value="normal">正常</option>
                            <option value="paused">暂停</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">审核状态</label>
                        <select class="form-select" name="review_status" id="editMemberReviewStatus">
                            <option value="approved">通过</option>
                            <option value="pending">待审核</option>
                            <option value="rejected">不通过</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">微信二维码</label>
                        <div class="input-group">
                            <input type="file" class="form-control" name="qrcode_upload" accept="image/*">
                        </div>
                        <small class="text-muted">上传新的微信二维码图片</small>
                        <div id="currentQrcode" class="mt-2"></div>
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

<style>
/* 支持emoji表情显示的样式 */
textarea[name="bio"] {
    font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", "Segoe UI", system-ui, sans-serif;
    line-height: 1.5;
}

.emoji-hint {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
</style>

<script>

function isValidUrl(url) {
    try {
        return url.startsWith('http://') || url.startsWith('https://');
    } catch (e) {
        return false;
    }
}

function showAddMemberModal() {
    var myModal = new bootstrap.Modal(document.getElementById('addMemberModal'));
    myModal.show();
}

function submitAddForm() {
    document.getElementById('addMemberForm').submit();
}

function editMember(id) {

    console.log('Fetching member data for ID:', id);
    
    fetch('get_member.php?id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data); 
            
            if (data.error) {
                throw new Error(data.message || '获取数据失败');
            }

            document.getElementById('editMemberId').value = data.id;
            document.getElementById('editMemberName').value = data.name;
            document.getElementById('editMemberPosition').value = data.position;
            document.getElementById('editMemberBio').value = data.bio;
            document.getElementById('editMemberImageUrl').value = data.image_url;
            document.getElementById('editMemberQQ').value = data.qq;
            document.getElementById('editMemberWechat').value = data.wechat;
            document.getElementById('editMemberEmail').value = data.email;
            document.getElementById('editMemberStatus').value = data.status;
            document.getElementById('editMemberReviewStatus').value = data.review_status;

            if (data.image_url) {
                updateImagePreview(data.image_url, 'currentImage');
            }
   
            if (data.wechat) {
                updateQrcodePreview(data.wechat);
            }

            var editModal = new bootstrap.Modal(document.getElementById('editMemberModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('获取成员信息失败: ' + error.message);
        });
}

function submitEditForm() {
    document.getElementById('editMemberForm').submit();
}

function updateImagePreview(imageUrl, containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('Preview container not found:', containerId);
        return;
    }
    
    if (imageUrl) {
        const imgSrc = isValidUrl(imageUrl) ? imageUrl : '../' + imageUrl;
        container.innerHTML = `<img src="${imgSrc}" alt="预览" style="max-width: 200px;" onerror="this.onerror=null; this.src='../assets/admin/img/preview-error.png';">`;
    } else {
        container.innerHTML = '';
    }
}

function updateQrcodePreview(wechat) {
    const container = document.getElementById('currentQrcode');
    if (container && wechat) {
        const qrcodePath = `../qrcodes/${wechat}.jpg`;
        container.innerHTML = `
            <img src="${qrcodePath}" alt="微信二维码" style="max-width: 150px; margin-top: 10px;" 
                 onerror="this.style.display='none'">
        `;
    }
}

document.getElementById('editMemberWechat').addEventListener('change', function() {
    updateQrcodePreview(this.value);
});

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editMemberModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const imageUrl = document.getElementById('editMemberImageUrl').value;
            updateImagePreview(imageUrl, 'currentImage');
        });
    }

    const imageUrlInput = document.getElementById('editMemberImageUrl');
    if (imageUrlInput) {
        imageUrlInput.addEventListener('change', function() {
            updateImagePreview(this.value, 'currentImage');
        });
    }
});
</script>

<?php
include 'footer.php';
?>