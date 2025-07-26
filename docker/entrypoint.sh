#!/bin/bash
set -e

# 设置文件权限
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/uploads

echo "XingHan-Team 官网程序启动完成"
echo "访问地址: http://localhost:8080"
echo "请通过Web界面进行一键安装配置"

# 启动Apache
exec apache2-foreground
