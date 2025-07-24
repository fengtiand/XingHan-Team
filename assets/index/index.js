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
 */ 
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navList = document.querySelector('nav ul');
    const menuLinks = document.querySelectorAll('nav ul li a');

    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            navList.classList.toggle('show');
        });
    }

    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            navList.classList.remove('show');
        });
    });

    document.addEventListener('click', function(event) {
        if (navList.classList.contains('show')) {
            const isClickInside = menuToggle.contains(event.target) || navList.contains(event.target);
            if (!isClickInside) {
                menuToggle.classList.remove('active');
                navList.classList.remove('show');
            }
        }
    });

    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            container: 'body',
            placement: 'top',
            html: true,
            trigger: 'click',
            template: '<div class="popover wechat-popover" role="tooltip"><div class="popover-arrow"></div><div class="popover-body"></div></div>'
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.wechat-icon') && !e.target.closest('.popover')) {
            popoverList.forEach(function(popover) {
                popover.hide();
            });
        }
    });

    const teamQueryBtn = document.getElementById('teamQueryBtn');
    if (teamQueryBtn) {
        teamQueryBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('teamQueryModal'));
            modal.show();
        });
    }

    const joinTeamBtn = document.getElementById('joinTeamBtn');
    if (joinTeamBtn) {
        joinTeamBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('joinTeamModal'));
            modal.show();
        });
    }

    const teamQueryForm = document.getElementById('teamQueryForm');
    if (teamQueryForm) {
        teamQueryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var query = document.getElementById('queryInput').value.trim();
            
            if (query.length === 0) {
                alert('请输入搜索内容');
                return;
            }

            fetch('queryteam.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'query=' + encodeURIComponent(query)
            })
            .then(response => response.json())
            .then(data => {
                var resultDiv = document.getElementById('queryResult');
                if (data.isMember) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> 该成员是我们团队的一员。</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> 该成员不是我们团队的一员。请谨防被骗！</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('queryResult').innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> 查询出错，请稍后再试。</div>';
            });
        });
    }

    // 发送验证码功能
    function initVerificationCode() {
        const sendVerificationButton = document.getElementById('sendVerificationCode');
        const applicantEmail = document.getElementById('applicantEmail');
        
        if (!sendVerificationButton || !applicantEmail) {
            console.log('Verification elements not found');
            return;
        }

        sendVerificationButton.addEventListener('click', function() {
            const email = applicantEmail.value;
            if (!email) {
                alert('请输入邮箱地址');
                return;
            }

            const button = this;
            button.disabled = true;
            let countdown = 60;
            const originalText = button.textContent;
 
            fetch('sendcode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    const timer = setInterval(() => {
                        button.textContent = `${countdown}秒后重试`;
                        countdown--;
                        if (countdown < 0) {
                            clearInterval(timer);
                            button.disabled = false;
                            button.textContent = originalText;
                        }
                    }, 1000);
                } else {
                    button.disabled = false;
                    button.textContent = originalText;
                    alert(data.message || '发送失败，请稍后重试');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.textContent = originalText;
                console.error('Error:', error);
                alert('发送失败，请稍后重试');
            });
        });
    }

    initVerificationCode();

    const joinTeamForm = document.getElementById('joinTeamForm');
    const joinTeamModal = document.getElementById('joinTeamModal');
    const applicationSuccessModal = document.getElementById('applicationSuccessModal');

    if (joinTeamForm && joinTeamModal && applicationSuccessModal) {
        joinTeamForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('joinreq.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    const modal = bootstrap.Modal.getInstance(joinTeamModal);
                    if (modal) modal.hide();

                    const successModal = new bootstrap.Modal(applicationSuccessModal);
                    successModal.show();

                    this.reset();
                } else {
                    if (data.error === 'email_exists') {
                        alert('该邮箱已经是团队成员，请使用其他邮箱。');
                    } else if (data.error === 'invalid_code') {
                        alert('验证码错误，请重新输入。');
                    } else {
                        alert('提交申请时出错：' + (data.message || '未知错误'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('提交申请时出错，请稍后重试');
            });
        });
    }

    window.addEventListener('scroll', function() {
        var header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    var swiper = new Swiper('.fullscreen-swiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
});
