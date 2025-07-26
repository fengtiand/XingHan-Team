# 使用官方PHP Apache镜像作为基础镜像
FROM php:8.0-apache

# 设置工作目录
WORKDIR /var/www/html

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 安装PHP扩展
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip

# 启用Apache mod_rewrite
RUN a2enmod rewrite

# 复制项目文件到容器
COPY . /var/www/html/

# 设置正确的文件权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 创建必要的目录
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 777 /var/www/html/uploads

# 安装MySQL客户端工具（用于健康检查）
RUN apt-get update && apt-get install -y mysql-client && rm -rf /var/lib/apt/lists/*

# 复制Apache配置
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# 复制启动脚本
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 暴露端口
EXPOSE 80

# 健康检查
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# 使用自定义启动脚本
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]