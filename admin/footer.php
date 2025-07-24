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

        </main>
        <footer id="page-footer" class="bg-body-light">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                        Crafted with <i class="fa fa-heart text-danger"></i> by <a class="fw-semibold" href="https://blog.ococn.cn" target="_blank">奉天</a>
                    </div>
                    <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">Copyright &copy;<span data-toggle="year-copy"></span>
                        <a class="fw-semibold" href="https://www.ococn.cn" target="_blank">XingHan-Team 星涵网络工作室</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="/assets/admin/js/oneui.app.min-5.6.js"></script>
    <?php
    if (isset($js_content)) {
        echo $js_content;
    }
    ?>
</body>
</html>