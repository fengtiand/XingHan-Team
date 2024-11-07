<?php
require_once '../db_connect.php';
$page_title = '控制面板';
$current_page = 'index';
include 'header.php';
$team_members_count = 0;
$projects_count = 0;
$pending_requests_count = 0;
$total_portfolio_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM team_members WHERE status = 'normal' AND review_status = 'approved'");
if ($result && $result->num_rows > 0) {
    $team_members_count = $result->fetch_assoc()['count'];
}
$result = $conn->query("SELECT COUNT(*) as count FROM portfolio WHERE status = 'show'");
if ($result && $result->num_rows > 0) {
    $projects_count = $result->fetch_assoc()['count'];
}
$result = $conn->query("SELECT COUNT(*) as count FROM team_members WHERE review_status = 'pending'");
if ($result && $result->num_rows > 0) {
    $pending_requests_count = $result->fetch_assoc()['count'];
}
$result = $conn->query("SELECT COUNT(*) as count FROM portfolio");
if ($result && $result->num_rows > 0) {
    $total_portfolio_count = $result->fetch_assoc()['count'];
}
$result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_announcement'");
$site_announcement = $result && $result->num_rows > 0 ? $result->fetch_assoc()['setting_value'] : '';
$server_info = [
    'PHP版本' => PHP_VERSION,
    'MySQL版本' => $conn->server_info,
    '服务器软件' => $_SERVER['SERVER_SOFTWARE'],
    '服务器操作系统' => php_uname('s') . ' ' . php_uname('r'),
    '服务器时间' => date('Y-m-d H:i:s'),
    '标语' => '只有先做一个成功者,社会才有可能接受她的与众不同。',
    
];
?>
<div class="content">
    <div class="row">
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-dark"><?php echo $team_members_count; ?></div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="fw-medium fs-sm text-muted mb-0">
                        团队成员
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-dark"><?php echo $projects_count; ?></div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="fw-medium fs-sm text-muted mb-0">
                        展示项目
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-dark"><?php echo $pending_requests_count; ?></div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="fw-medium fs-sm text-muted mb-0">
                        待审核申请
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-dark"><?php echo $total_portfolio_count; ?></div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="fw-medium fs-sm text-muted mb-0">
                        网站展示总数
                    </p>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">系统公告</h3>
                </div>
                <div class="block-content">
                    <div id="announcements-container" class="admin-announcements"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">服务器信息</h3>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <?php foreach ($server_info as $key => $value): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($key); ?></td>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">广告联盟</h3>
            <div class="block-options">
                <a href="https://auth.xhus.cn/advert" target="_blank" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus opacity-50 me-1"></i> 投放广告
                </a>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                    <i class="si si-refresh"></i>
                </button>
            </div>
        </div>
        <div class="block-content">
            <div id="advertisements-container"></div>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>
