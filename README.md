# AAEditor

## 项目概述

AAEditor 是一个功能强大的 Typecho CMS 增强编辑器插件，提供现代化的 Markdown 编辑体验。插件集成了实时预览、代码高亮、数学公式支持、图片拖拽上传等丰富功能，大幅提升 Typecho 的内容创作效率。

### 核心功能

- **增强编辑器**: 现代化的 Markdown 编辑界面，支持实时预览
- **代码高亮**: 集成 Highlight.js，支持多种编程语言语法高亮
- **数学公式**: 内置 MathJax SVG 输出支持，渲染 LaTeX 数学公式且不依赖额外字体文件
- **图片处理**: 支持拖拽上传、一键插入所有图片
- **表格编辑器**: 可视化表格编辑工具
- **围栏区块**: 支持自定义容器、提示框、警告框、折叠区块等短代码
- **前台渲染**: 可选的前台正文样式和代码高亮加载
- **CDN 镜像**: 支持 jsDelivr 国内镜像加速

<!-- AUTO:tech-stack -->
## 技术栈

### 后端

| 层级 | 技术 | 版本 |
|------|------|------|
| 框架 | Typecho Plugin API | - |
| 语言 | PHP | 7.4+ |
| 数据库 | SQLite/MySQL/PostgreSQL | 依赖 Typecho |

### 前端

| 层级 | 技术 | 版本 |
|------|------|------|
| 构建工具 | Laravel Mix | 6.0+ |
| 打包工具 | Webpack | 5.40+ |
| 语言 | JavaScript (ES6+) | - |
| 样式 | SCSS | - |
| 代码高亮 | Highlight.js | 11.10+ |
| Markdown 解析 | Marked | 4.0+ |
<!-- /AUTO:tech-stack -->

<!-- AUTO:directory -->
## 项目结构

```
AAEditor/
├── Plugin.php              # 插件主文件，实现插件接口
├── Util.php                # 工具类，提供通用方法
├── HyperDown.php           # Markdown 解析器
├── assets/                 # 前端资源目录
│   ├── src/               # 源码目录
│   │   ├── main.js        # 编辑器主入口
│   │   ├── front.js       # 前台渲染入口
│   │   ├── editor/        # 编辑器组件
│   │   └── config/        # 配置页面资源
│   ├── dist/              # 构建产物（自动生成）
│   ├── json/              # 配置文件（表情/字符/符号）
│   └── mirror/            # CDN 镜像资源
├── tools/                  # 工具脚本目录
│   ├── pack.sh            # Linux 打包脚本
│   └── pack.cmd           # Windows 打包脚本
├── pack/                   # 打包产物（自动生成）
├── webpack.mix.js         # Webpack 配置文件
├── package.json           # npm 依赖配置
└── restart.sh             # 快速构建脚本
```
<!-- /AUTO:directory -->

<!-- AUTO:quick-start -->
## 快速开始

### 环境要求

- PHP 7.4 或更高版本
- Typecho 1.3.0 或更高版本
- Node.js 14+ （仅开发构建需要）

### 开发构建

```bash
# 克隆项目
git clone https://github.com/benzBrake/AAEditor.git
cd AAEditor

# 安装依赖
npm install --legacy-peer-deps

# 开发模式（支持热更新）
npm run dev

# 生产构建
npm run prod

# 打包插件（生成发布包）
npm run pack
```

### 快速构建

```bash
# 使用快速构建脚本
./restart.sh              # 生产构建
./restart.sh dev          # 开发构建
```

### Typecho 安装

1. 将整个 `AAEditor` 目录上传到 Typecho 的 `/usr/plugins/` 目录
2. 确保 目录名为 `AAEditor`
3. 登录 Typecho 后台，进入 **控制台 → 插件**
4. 启用 **AAEditor** 插件
5. 进入插件设置页面进行配置
<!-- /AUTO:quick-start -->

## 配置说明

### 基础设置

| 配置项 | 说明 | 默认值 |
|--------|------|--------|
| 是否开启编辑器 | 如仅需短代码功能可关闭 | 开启 |
| 前台加载正文样式 | 默认主题已适配，其他主题需手动添加类名 | 开启 |
| 开启公式支持 | 前台引入 MathJax SVG 输出脚本解析公式，不依赖额外字体文件 | 开启 |
| 前台载入 FontAwesome | 关闭后需自行加载图标 | 自动 |
| 前台载入 Highlight.js | 关闭后需自行渲染代码块 | 关闭 |
| 图片一键插入全部 | 附件列表中一键插入所有图片 | 开启 |
| jsDelivr 镜像 | 选择国内镜像 CDN 提高访问速度 | 本地 |

### 慎重修改

| 配置项 | 说明 |
|--------|------|
| 禁用插件后是否保留数据 | 开启后禁用插件时会清理所有插件数据 |

## 版本发布

1. 更新 `Plugin.php` 中的 `@version` 标签
2. 运行 `npm run prod` 构建前端资源
3. 运行 `npm run pack` 生成发布包
4. 在 `pack/` 目录获取 `AAEditor-版本号-日期.zip`

## 相关链接

- [语法说明](SYNTAX.md)
- [使用说明](https://malleable-flare-263.notion.site/AAEditor-0a003324bf3a4a608d6d563ef3a9779e)
- [更新日志](https://xiamp.net/archives/aaeditor-update-log.html)
- [问题反馈](https://github.com/benzBrake/AAEditor/issues)

## 许可证

[ Mozilla Public License Version 2.0](LICENSE)

**学习可以，禁止直接改名商用！！！**
