<?php
$install_lock_file = 'install.lock';
$db_config_file = 'db_connect.php';

if (!file_exists($install_lock_file)) {
    header('Location: install/index.php');
    exit;
}

if (!file_exists($db_config_file)) {
    die('数据库配置文件丢失。请重新安装系统。');
}

require_once 'db_connect.php';

$query_settings = $conn->query("SELECT * FROM site_settings");
if ($query_settings === false) {
    die("数据库查询失败: " . $conn->error . "。请确认 'site_settings' 表是否存在，并且数据库配置正确。");
}
$site_settings = $query_settings->fetch_all(MYSQLI_ASSOC);
$settings = array_column($site_settings, 'setting_value', 'setting_key');

$result = $conn->query("SELECT * FROM content WHERE section IN ('hero', 'about', 'services', 'culture')");
if ($result === false) {
    die("查询失败: " . $conn->error);
}

if ($result->num_rows > 0) {
    $content = [];
    while ($row = $result->fetch_assoc()) {
        $content[$row['section']][] = $row;
    }
} else {
    echo "没有找到数据";
}
$team_members = $conn->query("SELECT * FROM team_members WHERE status = 'normal' AND review_status = 'approved' ORDER BY id");
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
 * 本软件受著作权法和国际公约的保护。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */ -->
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="keywords" content="<?php echo htmlspecialchars($settings['site_keywords'] ?? ''); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($settings['site_description'] ?? ''); ?>">
    <title><?php echo htmlspecialchars($settings['site_title'] ?? '默认网站标题'); ?> - <?php echo htmlspecialchars($settings['site_subtitle']); ?></title>
    <?php if (!empty($settings['site_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['site_favicon']); ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?php echo htmlspecialchars($settings['site_favicon']); ?>" type="image/x-icon">
    <?php else: ?>
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/index/style.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <?php if ($settings['logo_type'] == 'image'): ?>
                    <img src="<?php echo htmlspecialchars($settings['logo_image']); ?>" alt="<?php echo htmlspecialchars($settings['site_title']); ?>" class="logo-image">
                <?php else: ?>
                    <span class="logo-text"><?php echo htmlspecialchars($settings['logo_text'] ?? '星涵网络工作室'); ?></span>
                <?php endif; ?>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul>
                <li><a href="#home">首页</a></li>
                <li><a href="#about">关于我们</a></li>
                <li><a href="#services">服务内容</a></li>
                <li><a href="#portfolio">网站展示</a></li>
                <li><a href="#team-query">团队查询</a></li>
                <li><a href="#team-query">申请加入</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="swiper-container fullscreen-swiper">
                <div class="swiper-wrapper">
                    <?php if (isset($content['hero']) && is_array($content['hero'])): ?>
                        <?php foreach ($content['hero'] as $slide): ?>
                            <div class="swiper-slide" style="background-image: url('<?php echo htmlspecialchars($slide['image_url']); ?>');">
                                <div class="slide-content">
                                    <h1><?php echo htmlspecialchars($slide['title']); ?></h1>
                                    <p><?php echo htmlspecialchars($slide['content']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide" style="background-image: url('assets/images/default-hero.jpg');">
                            <div class="slide-content">
                                <h1>欢迎访问</h1>
                                <p>请在后台添加内容</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

        <section id="about">
            <div class="section-header">
                <h2><?php echo isset($content['about'][0]) ? htmlspecialchars($content['about'][0]['title']) : '关于我们'; ?></h2>
                <span class="section-subtitle">ABOUT US</span>
            </div>
            <div class="about-content">
                <div class="about-image">
                    <?php if (isset($content['about'][0])): ?>
                        <?php if (strpos($content['about'][0]['image_url'], '.mp4') !== false): ?>
                            <video class="about-video" autoplay muted loop playsinline>
                                <source src="<?php echo htmlspecialchars($content['about'][0]['image_url']); ?>" type="video/mp4">
                                您的浏览器不支持视频播放。
                            </video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($content['about'][0]['image_url']); ?>" alt="关于我们" class="about-img">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="about-text">
                    <p><?php echo isset($content['about'][0]) ? nl2br(htmlspecialchars($content['about'][0]['content'])) : '请在后台添加内容'; ?></p>
                </div>
            </div>
        </section>

        <section id="services">
            <div class="section-header">
                <h2>我们的服务</h2>
                <span class="section-subtitle">OUR SERVICES</span>
            </div>
            <div class="service-grid">
                <?php
                $services_result = $conn->query("SELECT * FROM content WHERE section = 'services' ORDER BY id");
                $icons = ['fa-code', 'fa-paint-brush', 'fa-chart-line', 'fa-mobile-alt', 'fa-search', 'fa-cogs', 'fa-server'];
                $index = 0;
                while ($service = $services_result->fetch_assoc()):
                    $icon = isset($icons[$index]) ? $icons[$index] : 'fa-star';
                    $index++;
                ?>
                <div class="service-item">
                    <div class="service-icon">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['content']); ?></p>
                    <div class="service-decoration"></div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section id="team">
            <div class="section-header">
                <h2>我们的团队</h2>
                <span class="section-subtitle">OUR TEAM</span>
            </div>
            <div class="team-grid">
                <?php 
                $member_count = 0;
                while ($member = $team_members->fetch_assoc()): 
                    $member_count++;
                ?>
                <div class="team-member">
                    <div class="member-image">
                        <img src="<?php echo htmlspecialchars($member['image_url']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-member-img">
                    </div>
                    <div class="member-info">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p class="position"><?php echo htmlspecialchars($member['position']); ?></p>
                        <p class="bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                        <div class="member-social">
                            <?php if (!empty($member['qq'])): ?>
                                <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo htmlspecialchars($member['qq']); ?>&site=qq&menu=yes" class="social-icon" title="QQ"><i class="fab fa-qq"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($member['wechat'])): ?>
                                <a href="javascript:void(0)" class="social-icon wechat-icon" 
                                   data-wechat="<?php echo htmlspecialchars($member['wechat']); ?>"
                                   data-bs-toggle="popover" 
                                   data-bs-trigger="hover" 
                                   data-bs-html="true" 
                                   data-bs-content="<div class='text-center'><img src='qrcodes/<?php echo htmlspecialchars($member['wechat']); ?>.jpg' alt='微信二维码' class='wechat-qr'><div class='mt-2'><?php echo htmlspecialchars($member['wechat']); ?></div></div>">
                                    <i class="fab fa-weixin"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($member['email'])): ?>
                                <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="social-icon" title="邮箱"><i class="fas fa-envelope"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="member-number"><?php echo $member_count; ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section id="portfolio">
            <div class="section-header">
                <h2>网站展示</h2>
                <span class="section-subtitle">PORTFOLIO</span>
            </div>
            <div class="portfolio-grid">
                <?php
                $portfolio_result = $conn->query("SELECT * FROM portfolio WHERE status = 'show' ORDER BY id");
                while ($project = $portfolio_result->fetch_assoc()):
                ?>
                <div class="portfolio-item">
                    <div class="portfolio-tag"><?php echo htmlspecialchars($project['tag']); ?></div>
                    <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                    <div class="portfolio-info">
                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p><?php echo htmlspecialchars($project['description']); ?></p>
                        <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" class="btn btn-primary">前往网站</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section id="team-query">
            <div class="section-header">
                <h2>团队查询</h2>
                <span class="section-subtitle">TEAM QUERY</span>
            </div>
            <div class="team-query-content">
                <button class="btn btn-primary custom-btn" id="teamQueryBtn">团队查询</button>
                <button class="btn btn-secondary custom-btn" id="joinTeamBtn">申请加入</button>
            </div>
        </section>

        <section id="studio-culture">
            <div class="section-header">
                <h2>工作室文化</h2>
                <span class="section-subtitle">STUDIO CULTURE</span>
            </div>
            <div class="culture-content">
                <?php
                $culture_result = $conn->query("SELECT * FROM content WHERE section = 'culture' ORDER BY id");
                while ($culture = $culture_result->fetch_assoc()):
                ?>
                <div class="culture-item">
                    <h3><?php echo htmlspecialchars($culture['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($culture['content'])); ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section id="studio-environment">
            <div class="section-header">
                <h2>工作室环境</h2>
                <span class="section-subtitle">STUDIO ENVIRONMENT</span>
            </div>
            <div class="environment-content">
                <?php
                $environment_result = $conn->query("SELECT * FROM content WHERE section = 'environment' ORDER BY id");
                while ($environment = $environment_result->fetch_assoc()):
                ?>
                <div class="environment-item">
                    <img src="<?php echo htmlspecialchars($environment['image_url']); ?>" alt="<?php echo htmlspecialchars($environment['title']); ?>">
                    <div class="environment-info">
                        <h3><?php echo htmlspecialchars($environment['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($environment['content'])); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-main">
                <div class="footer-section">
                    <h4>关于我们</h4>
                    <p><?php 
                        if (isset($content['about']) && !empty($content['about'][0]['content'])) {
                            echo htmlspecialchars(mb_substr($content['about'][0]['content'], 0, 100, 'UTF-8')) . '...';
                        } else {
                            echo '暂无内容';
                        }
                    ?></p>
                </div>
                <div class="footer-section">
                    <h4>联系方式</h4>
                    <ul>
                        <?php if (!empty($settings['contact_email'])): ?>
                            <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($settings['contact_email']); ?>"><?php echo htmlspecialchars($settings['contact_email']); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($settings['contact_qq'])): ?>
                            <li><i class="fab fa-qq"></i> <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo htmlspecialchars($settings['contact_qq']); ?>&site=qq&menu=yes" target="_blank"><?php echo htmlspecialchars($settings['contact_qq']); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($settings['contact_wechat'])): ?>
                            <li><i class="fab fa-weixin"></i> <?php echo htmlspecialchars($settings['contact_wechat']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>快速链接</h4>
                    <ul>
                        <?php
                        $footer_links = json_decode($settings['footer_links'] ?? '[]', true);
                        if (!empty($footer_links)):
                            foreach ($footer_links as $link):
                        ?>
                            <li><a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank"><?php echo htmlspecialchars($link['text']); ?></a></li>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <li><a href="#home">首页</a></li>
                            <li><a href="#about">关于我们</a></li>
                            <li><a href="#services">服务内容</a></li>
                            <li><a href="#portfolio">网站展示</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>工作室文化</h4>
                    <ul>
                        <?php
                        if (isset($content['culture']) && !empty($content['culture'])):
                            $count = 0;
                            foreach ($content['culture'] as $culture):
                                if ($count >= 3) break;
                        ?>
                            <li><?php echo htmlspecialchars($culture['title']); ?></li>
                        <?php
                                $count++;
                            endforeach;
                        else:
                        ?>
                            <li>暂无内容</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copyright">
                    <?php echo $settings['footer_text'] ?? '&copy; ' . date('Y') . ' 星涵网络工作室. 保留所有权利。'; ?>
                </div>
                <?php if (!empty($settings['icp_number'])): ?>
                    <div class="icp">
                        <a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow">
                            <img src="assets/img/zzdxxkz.png" alt="ICP备案图标" class="beian-icon">
                            <?php echo htmlspecialchars($settings['icp_number']); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (!empty($settings['police_number'])): ?>
                    <div class="police">
                        <a href="http://www.beian.gov.cn/portal/index.do" target="_blank" rel="nofollow">
                            <img src="assets/img/beian.png" alt="公安备案图标" class="beian-icon">
                            <?php echo htmlspecialchars($settings['police_number']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="teamQueryModal" tabindex="-1" role="dialog" aria-labelledby="teamQueryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="teamQueryModalLabel">团队成员查询</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="teamQueryForm">
                        <div class="mb-3">
                            <label for="queryInput" class="form-label">请输入姓名、QQ、微信或邮箱</label>
                            <input type="text" class="form-control" id="queryInput" required>
                        </div><center>
                        <button type="submit" class="btn btn-primary">查询</button></center>
                    </form>
                    <div id="queryResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="joinTeamModal" tabindex="-1" role="dialog" aria-labelledby="joinTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joinTeamModalLabel">申请加入团队</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="joinTeamForm" action="joinreq.php" method="POST">
                        <div class="mb-3">
                            <label for="applicantName" class="form-label">姓名</label>
                            <input type="text" class="form-control" id="applicantName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicantEmail" class="form-label">邮箱</label>
                            <div class="input-group d-flex">
                                <input type="email" class="form-control" id="applicantEmail" name="email" required>
                                <button class="btn btn-outline-secondary align-self-stretch" type="button" id="sendVerificationCode">
                                    发送验证码
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="verificationCode" class="form-label">验证码</label>
                            <input type="text" class="form-control" id="verificationCode" name="verificationCode" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicantBio" class="form-label">介绍自己</label>
                            <textarea class="form-control" id="applicantBio" name="bio" rows="5" placeholder="请简单介绍一下自己，包括你的技能、经验和为什么想加入我们的团队" required></textarea>
                        </div><center>
                        <button type="submit" class="btn btn-primary">提交申请</button></center>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="applicationSuccessModal" tabindex="-1" role="dialog" aria-labelledby="applicationSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applicationSuccessModalLabel">申请提交成功</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>您的申请已成功提交，我们会尽快处理并与您联系。感谢您的兴趣！</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="assets/index/index.js"></script>
</body>
</html>
