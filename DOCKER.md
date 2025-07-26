# XingHan-Team Docker 部署指南

## 🚀 快速开始

### 方式一：使用 Docker Compose（推荐）

```bash
# 克隆项目
git clone <your-repo-url>
cd xinghanteam

# 启动所有服务
docker-compose up -d

# 查看服务状态
docker-compose ps
```

### 方式二：使用预构建镜像

```bash
# 拉取镜像
docker pull ghcr.io/fengtiand/xinghan-team:latest

# 启动MySQL数据库
docker run -d \
  --name xinghan-mysql \
  -e MYSQL_ROOT_PASSWORD=xinghan123 \
  -e MYSQL_DATABASE=xinghan_team \
  -p 3306:3306 \
  mysql:8.0

# 启动Web应用
docker run -d \
  --name xinghan-web \
  --link xinghan-mysql:mysql \
  -e DB_HOST=mysql \
  -e DB_NAME=xinghan_team \
  -e DB_USER=root \
  -e DB_PASS=xinghan123 \
  -p 8080:80 \
  ghcr.io/fengtiand/xinghan-team:latest
```

## 📋 服务说明

| 服务 | 端口 | 说明 |
|------|------|------|
| Web应用 | 8080 | 主网站入口 |
| MySQL | 3306 | 数据库服务 |
| phpMyAdmin | 8081 | 数据库管理界面 |

## 🔧 环境变量配置

| 变量名 | 默认值 | 说明 |
|--------|--------|------|
| DB_HOST | mysql | 数据库主机 |
| DB_NAME | xinghan_team | 数据库名 |
| DB_USER | root | 数据库用户 |
| DB_PASS | xinghan123 | 数据库密码 |

## 📁 数据持久化

重要目录已通过 Docker Volume 进行持久化：

- `mysql_data`: MySQL数据目录
- `./uploads`: 文件上传目录
- `./assets`: 静态资源目录

## 🛠️ 自定义配置

### 修改数据库密码

编辑 `docker-compose.yml` 文件中的环境变量：

```yaml
environment:
  MYSQL_ROOT_PASSWORD: your_new_password
  # 同时更新web服务的DB_PASS
```

### 自定义端口

```yaml
ports:
  - "your_port:80"  # 修改为你想要的端口
```

## 🔍 故障排除

### 查看日志

```bash
# 查看所有服务日志
docker-compose logs

# 查看特定服务日志
docker-compose logs web
docker-compose logs mysql
```

### 重启服务

```bash
# 重启所有服务
docker-compose restart

# 重启特定服务
docker-compose restart web
```

### 数据库连接问题

1. 确保MySQL服务已完全启动
2. 检查环境变量配置是否正确
3. 查看MySQL日志：`docker-compose logs mysql`

## 🚀 生产环境部署

### 1. 安全配置

- 修改默认密码
- 使用HTTPS（配置反向代理）
- 限制数据库访问权限

### 2. 性能优化

```yaml
# 在docker-compose.yml中添加资源限制
services:
  web:
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M
```

### 3. 备份策略

```bash
# 数据库备份
docker-compose exec mysql mysqldump -u root -p xinghan_team > backup.sql

# 恢复数据库
docker-compose exec -T mysql mysql -u root -p xinghan_team < backup.sql
```

## 📦 构建自定义镜像

```bash
# 构建镜像
docker build -t xinghan-team:custom .

# 推送到私有仓库
docker tag xinghan-team:custom fengtiand/xinghan-team:latest
docker push fengtiand/xinghan-team:latest
```

## 🆘 获取帮助

如果遇到问题，请：

1. 检查 [故障排除](#故障排除) 部分
2. 查看项目 Issues
3. 提交新的 Issue 并附上详细的错误信息和日志

---

**默认管理员账号**
- 用户名：admin
- 密码：admin123

**访问地址**
- 主站：http://localhost:8080
- 数据库管理：http://localhost:8081