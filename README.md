FastAdmin是一款基于ThinkPHP+Bootstrap的极速后台开发框架。

## Docker 快速启动（已适配本项目数据库备份）

### 1) 启动

在 `daikuan-admins/` 目录执行：

```bash
docker compose up -d --build
```

- 访问后台/站点：`http://localhost:${NGINX_PORT}`（默认 `8081`）
- 数据库端口映射：宿主机 `${MYSQL_PORT}` -> 容器 `3306`（默认 `33060`）

### 2) 数据库自动导入说明

本项目已配置在 MySQL **首次初始化数据卷** 时自动导入：

- `docker/databse/daikuan_2025-12-24_14-13-19_mysql_data_SPiAM.sql`

注意：MySQL 官方镜像只会在数据卷为空时执行 `docker-entrypoint-initdb.d` 下的脚本/SQL。

### 3) 需要重新导入数据库（重置）

```bash
docker compose down -v
docker compose up -d --build
```

### 4) 容器内使用的数据库配置

本项目以 **根目录 `.env` 为唯一配置源**（同时给 ThinkPHP 和 Docker Compose 使用）：

- `docker-compose.yml` 会通过 `env_file: ./.env` 注入容器环境变量，并用 `${...}` 做端口映射等
- ThinkPHP 启动时会读取根目录 `.env`（`thinkphp/base.php`），并转换成 `PHP_` 前缀环境变量供 `Env::get()` 使用

关键配置如下：

- 网站域名：`NGINX_SERVER_NAME`（默认 `localhost`）
- 站点端口：`NGINX_PORT`（默认 `8081`）
- MySQL 宿主机端口：`MYSQL_PORT`（默认 `33060`）
- 业务库连接（ThinkPHP）：`DATABASE_HOSTNAME=mysql` / `DATABASE_DATABASE=daikuan` / `DATABASE_USERNAME` / `DATABASE_PASSWORD`


## 主要特性

* 基于`Auth`验证的权限管理系统
    * 支持无限级父子级权限继承，父级的管理员可任意增删改子级管理员及权限设置
    * 支持单管理员多角色
    * 支持管理子级数据或个人数据
* 强大的一键生成功能
    * 一键生成CRUD,包括控制器、模型、视图、JS、语言包、菜单、回收站等
    * 一键压缩打包JS和CSS文件，一键CDN静态资源部署
    * 一键生成控制器菜单和规则
    * 一键生成API接口文档
* 完善的前端功能组件开发
    * 基于`AdminLTE`二次开发
    * 基于`Bootstrap`开发，自适应手机、平板、PC
    * 基于`RequireJS`进行JS模块管理，按需加载
    * 基于`Less`进行样式开发
* 强大的插件扩展功能，在线安装卸载升级插件
* 通用的会员模块和API模块
* 共用同一账号体系的Web端会员中心权限验证和API接口会员权限验证
* 二级域名部署支持，同时域名支持绑定到应用插件
* 多语言支持，服务端及客户端支持
* 支持大文件分片上传、剪切板粘贴上传、拖拽上传，进度条显示，图片上传前压缩
* 支持表格固定列、固定表头、跨页选择、Excel导出、模板渲染等功能
* 强大的第三方应用模块支持([CMS](https://www.fastadmin.net/store/cms.html)、[CRM](https://www.fastadmin.net/store/facrm.html)、[企业网站管理系统](https://www.fastadmin.net/store/ldcms.html)、[知识库文档系统](https://www.fastadmin.net/store/knowbase.html)、[在线投票系统](https://www.fastadmin.net/store/vote.html)、[B2C商城](https://www.fastadmin.net/store/shopro.html)、[B2B2C商城](https://www.fastadmin.net/store/wanlshop.html))
* 整合第三方短信接口(阿里云、腾讯云短信)
* 无缝整合第三方云存储(七牛云、阿里云OSS、腾讯云存储、又拍云)功能，支持云储存分片上传
* 第三方富文本编辑器支持(Summernote、百度编辑器)
* 第三方登录(QQ、微信、微博)整合
* 第三方支付(微信、支付宝)无缝整合，微信支持PC端扫码支付
* 丰富的插件应用市场

## 安装使用

https://doc.fastadmin.net

## 在线演示

https://demo.fastadmin.net

用户名：admin

密　码：123456

提　示：演示站数据无法进行修改，请下载源码安装体验全部功能

## 界面截图
![控制台](https://images.gitee.com/uploads/images/2020/0929/202947_8db2d281_10933.gif "控制台")

## 问题反馈

在使用中有任何问题，请使用以下联系方式联系我们

问答社区: https://ask.fastadmin.net

Github: https://github.com/fastadminnet/fastadmin

Gitee: https://gitee.com/fastadminnet/fastadmin

## 特别鸣谢

感谢以下的项目,排名不分先后

ThinkPHP：http://www.thinkphp.cn

AdminLTE：https://adminlte.io

Bootstrap：http://getbootstrap.com

jQuery：http://jquery.com

Bootstrap-table：https://github.com/wenzhixin/bootstrap-table

Nice-validator: https://validator.niceue.com

SelectPage: https://github.com/TerryZ/SelectPage

Layer: https://layuion.com/layer/

DropzoneJS: https://www.dropzonejs.com


## 版权信息

FastAdmin遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2017-2024 by FastAdmin (https://www.fastadmin.net)

All rights reserved。
