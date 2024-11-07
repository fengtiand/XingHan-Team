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
 * 本软件受著作权法和国际公约的保护。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
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

    var teamQueryModal = new bootstrap.Modal(document.getElementById('teamQueryModal'), {
        backdrop: 'static',
        keyboard: false
    });
    var joinTeamModal = new bootstrap.Modal(document.getElementById('joinTeamModal'), {
        backdrop: 'static',
        keyboard: false
    });
    document.querySelector('.btn-primary.custom-btn').addEventListener('click', function(e) {
        e.preventDefault();
        teamQueryModal.show();
    });
    document.querySelector('.btn-secondary.custom-btn').addEventListener('click', function(e) {
        e.preventDefault();
        joinTeamModal.show();
    });
    document.getElementById('teamQueryForm').addEventListener('submit', function(e) {
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
    document.getElementById('joinTeamForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        fetch('joinreq.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var joinTeamModal = bootstrap.Modal.getInstance(document.getElementById('joinTeamModal'));
                joinTeamModal.hide();
                
                var successModal = new bootstrap.Modal(document.getElementById('applicationSuccessModal'));
                successModal.show();
                
                this.reset();
            } else {
                if (data.error === 'email_exists') {
                    alert(data.message || '该邮箱已经是团队成员，请使用其他邮箱。');
                } else if (data.error === 'invalid_code') {
                    alert('验证码错误，请重新输入。');
                } else {
                    alert('提交申请时出错：' + (data.error || '未知错误'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('提交申请时出错：' + error.message);
        });
    });
    window.addEventListener('scroll', function() {
        var header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]').forEach(function(element) {
        element.addEventListener('click', function() {
            teamQueryModal.hide();
            joinTeamModal.hide();
            document.body.classList.remove('modal-open');
            document.querySelector('.modal-backdrop').remove();
        });
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

function showTeamQueryModal() {
    var teamQueryModal = new bootstrap.Modal(document.getElementById('teamQueryModal'));
    teamQueryModal.show();
}

function showJoinTeamModal() {
    var joinTeamModal = new bootstrap.Modal(document.getElementById('joinTeamModal'));
    joinTeamModal.show();
}

document.getElementById('sendVerificationCode').addEventListener('click', function() {
    var email = document.getElementById('applicantEmail').value;
    if (email) {
        fetch('sendcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('验证码已发送到您的邮箱，请查收。');
                var button = document.getElementById('sendVerificationCode');
                button.disabled = true;
                var seconds = 60;
                var timer = setInterval(function() {
                    button.textContent = `重新发送(${seconds}s)`;
                    seconds--;
                    if (seconds < 0) {
                        clearInterval(timer);
                        button.disabled = false;
                        button.textContent = '发送验证码';
                    }
                }, 1000);
            } else {
                alert('发送验证码失败：' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('发送验证码时出错，请稍后再试。');
        });
    } else {
        alert('请先输入邮箱地址。');
    }
});
