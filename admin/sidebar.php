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
<nav id="sidebar" aria-label="Main Navigation">
    <div class="content-header">
        <a class="fw-semibold text-dual" href="index.php">
            <span class="smini-visible">
                <i class="fa fa-circle-notch text-primary"></i>
            </span>
            <span class="smini-hide fs-5 tracking-wider">星涵网络<span class="fw-normal">工作室</span></span>
        </a>
        <div>
            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="class-toggle"
                data-target="#page-container" data-class="dark-mode" onclick="toggleTheme();">
                <i class="fa fa-moon" id="theme-icon"></i>
            </button>
        </div>
    </div>
    <div class="js-sidebar-scroll">
        <div class="content-side">
            <ul class="nav-main">
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page === 'index') ? ' active' : ''; ?>"
                        href="index.php"> <i class="nav-main-link-icon fa fa-house-user"></i> <span
                            class="nav-main-link-name">首页</span> </a> </li>
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page === 'site') ? ' active' : ''; ?>" href="site.php">
                        <i class="nav-main-link-icon fa fa-cog"></i> <span class="nav-main-link-name">网站设置</span> </a>
                </li>
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page == 'home') ? ' active' : ''; ?>" href="home.php">
                        <i class="nav-main-link-icon fa fa-home"></i> <span class="nav-main-link-name">首页内容管理</span>
                    </a> </li>
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page == 'team') ? ' active' : ''; ?>" href="team.php">
                        <i class="nav-main-link-icon fa fa-users"></i> <span class="nav-main-link-name">团队成员管理</span>
                    </a> </li>
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page == 'manage') ? ' active' : ''; ?>"
                        href="manage.php"> <i class="nav-main-link-icon fa fa-briefcase"></i> <span
                            class="nav-main-link-name">网站展示管理</span> </a> </li>
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page == 'join') ? ' active' : ''; ?>" href="join.php">
                        <i class="nav-main-link-icon fa fa-user-plus"></i> <span class="nav-main-link-name">加入申请</span>
                    </a> </li>
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo ($current_page == 'mailset') ? ' active' : ''; ?>"
                        href="mailset.php"> <i class="nav-main-link-icon fa fa-envelope"></i> <span
                            class="nav-main-link-name">邮箱设置</span> </a> </li>
                
                <li class="nav-main-item"> <a
                        class="nav-main-link<?php echo $current_page === 'about' ? ' active' : ''; ?>" href="about.php">
                        <i class="nav-main-link-icon fa fa-info-circle"></i> <span
                            class="nav-main-link-name">关于程序</span> </a> </li>
            </ul>

        </div>
        <div class="content-side content-side-bottom">
            <ul class="nav-main">
                <li class="nav-main-item">
                    <a class="nav-main-link<?php echo ($current_page === 'passwd') ? ' active' : ''; ?>"
                        href="passwd.php">
                        <i class="nav-main-link-icon fa fa-lock"></i>
                        <span class="nav-main-link-name">修改密码</span>
                    </a>
                </li>
                <li class="nav-main-item">
                    <a class="nav-main-link" href="logout.php">
                        <i class="nav-main-link-icon fa fa-sign-out-alt"></i>
                        <span class="nav-main-link-name">退出登录</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    function toggleTheme() {
        const pageContainer = document.getElementById('page-container');
        const themeIcon = document.getElementById('theme-icon');
        const header = document.getElementById('page-header');

        if (pageContainer.classList.contains('dark-mode')) {
            pageContainer.classList.remove('dark-mode');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            header.classList.remove('bg-dark');
            header.classList.add('bg-header-light');
            localStorage.setItem('theme', 'light');
        } else {
            pageContainer.classList.add('dark-mode');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            header.classList.remove('bg-header-light');
            header.classList.add('bg-dark');
            localStorage.setItem('theme', 'dark');
        }
    }
    document.addEventListener('DOMContentLoaded', (event) => {
        const savedTheme = localStorage.getItem('theme');
        const pageContainer = document.getElementById('page-container');
        const themeIcon = document.getElementById('theme-icon');
        const header = document.getElementById('page-header');

        if (savedTheme === 'dark') {
            pageContainer.classList.add('dark-mode');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            header.classList.remove('bg-header-light');
            header.classList.add('bg-dark');
        } else {
            pageContainer.classList.remove('dark-mode');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            header.classList.remove('bg-dark');
            header.classList.add('bg-header-light');
        }
    });
</script>