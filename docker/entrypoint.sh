#!/bin/bash
set -e

# 等待MySQL服务启动
echo "等待MySQL服务启动..."
while ! mysqladmin ping -h "$DB_HOST" --silent; do
    sleep 1
done
echo "MySQL服务已启动"

# 检查是否需要使用Docker环境的数据库配置
if [ "$DB_HOST" = "mysql" ]; then
    echo "使用Docker环境数据库配置"
    cp /var/www/html/docker/db_connect.php /var/www/html/db_connect.php
fi

# 创建安装锁文件（跳过安装步骤）
if [ ! -f "/var/www/html/install.lock" ]; then
    echo "创建安装锁文件"
    touch /var/www/html/install.lock
fi

# 设置文件权限
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/uploads

echo "XingHan-Team 官网程序启动完成"
echo "访问地址: http://localhost:8080"
echo "数据库管理: http://localhost:8081"
echo "默认管理员账号: admin / admin123"

# 启动Apache
exec apache2-foreground