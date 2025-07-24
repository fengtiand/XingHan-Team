<?php
require_once '../db_connect.php';
$page_title = '关于程序';
$current_page = 'about';
include 'header.php';
?>

<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">关于 XingHan-Team 管理系统</h3>
        </div>
        <div class="block-content">
            <div class="py-4 text-center">
                <h2 class="mb-2">XingHan-Team 管理系统</h2>
                <h3 class="fs-base fw-medium text-muted mb-4">Version 1.7.1</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="mb-5">
                        <h4 class="mb-3">版本说明</h4>
                        <div class="alert alert-primary">
                            <p class="mb-0">当前版本：V1.7.1 (发布日期：2025-01-03)</p>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td style="width: 200px;"><strong>程序名称</strong></td>
                                    <td>XingHan-Team 管理系统</td>
                                </tr>
                                <tr>
                                    <td><strong>开发团队</strong></td>
                                    <td>星涵网络工作室</td>
                                </tr>
                                <tr>
                                    <td><strong>官方网站</strong></td>
                                    <td><a href="https://www.ococn.cn" target="_blank">www.ococn.cn</a></td>
                                </tr>
                                <tr>
                                    <td><strong>开源协议</strong></td>
                                    <td>MIT License</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-5">
                        <h4 class="mb-3">致谢</h4>
                        <div class="alert alert-info mb-4">
                            <p class="mb-0">特别感谢 OneUI 提供的优秀后台模板支持。</p>
                        </div>
                        <p>本系统使用了以下开源组件：</p>
                        <ul class="list-group push">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                OneUI Admin Template
                                <span class="badge bg-primary">5.6</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Bootstrap
                                <span class="badge bg-primary">5.1.3</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Font Awesome
                                <span class="badge bg-primary">5.15.4</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <h4 class="mb-3">版权声明</h4>
                        <div class="alert alert-warning mb-4">
                            <p class="mb-0">本版本开源发布，供大家使用和学习，未经授权禁止商业用途。</p>
                        </div>
                        <p>版权所有 &copy; 2024 XingHan-Team。保留所有权利。</p>
                        <p>本软件是一个开源项目，旨在为开发者提供学习和参考。在使用本软件时，请遵守以下规则：</p>
                        <ul class="list-group push">
                            <li class="list-group-item">允许个人和教育用途的使用</li>
                            <li class="list-group-item">需要保留原始版权信息</li>
                            <li class="list-group-item">禁止未经授权的商业用途</li>
                            <li class="list-group-item">欢迎贡献代码和反馈问题</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
