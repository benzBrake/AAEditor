# AAEditor 项目配置

## 项目概述

**AAEditor** 是一个用于 Typecho 的增强编辑器插件，提供 Markdown 编辑、公式支持、代码高亮等功能。

## 技术栈

### 后端
- **语言**: PHP 7.4+
- **框架**: Typecho Plugin API
- **数据库**: 依赖 Typecho（支持 SQLite/MySQL/PostgreSQL）

### 前端构建
- **构建工具**: Laravel Mix 6 + Webpack 5
- **语言**: 原生 JavaScript (ES6+)
- **样式**: SCSS
- **依赖管理**: npm
- **代码高亮**: Highlight.js 11+
- **Markdown 解析**: Marked 4+

## 项目结构

```
AAEditor/
├── Plugin.php              # 插件主文件
├── Util.php                # 工具类
├── HyperDown.php           # Markdown 解析器
├── assets/                 # 前端资源
│   ├── src/               # 源码
│   ├── dist/              # 构建产物（自动生成）
│   ├── json/              # 配置文件（表情/字符/符号）
│   └── mirror/            # CDN 镜像资源
├── tools/                  # 工具脚本
├── pack/                   # 打包产物（自动生成）
├── webpack.mix.js         # Webpack 配置
└── package.json           # npm 配置
```

## 开发工作流

### 安装依赖

```bash
npm install --legacy-peer-deps
```

### 开发模式（热更新）

```bash
npm run dev
# 或
npm run watch
```

### 生产构建

```bash
npm run prod
```

### 打包插件

```bash
npm run pack
# 或
cd tools && ./pack.sh
```

## 代码规范

### PHP
- 遵循 PSR-4 自动加载规范
- 命名空间：`TypechoPlugin\AAEditor`
- 类名使用 PascalCase
- 方法名使用 camelCase

### JavaScript
- 使用 ES6+ 语法
- 优先使用 const/let，避免 var
- 函数使用 camelCase
- 避免全局变量污染

### SCSS
- 使用 BEM 命名规范
- 变量使用 `$` 前缀
- 嵌套不超过 3 层

## 核心功能模块

### 编辑器功能
- Markdown 实时预览
- 代码块语法高亮
- 数学公式渲染（MathJax）
- 图片拖拽上传
- 表格编辑器
- 围栏区块（Fence Block）

### 短代码支持
- 自定义容器
- 提示框/警告框
- 折叠区块

### 前台渲染
- 正文样式加载（可选）
- Highlight.js 代码高亮
- FontAwesome 图标（可选）
- MathJax 公式渲染

## 重要配置项

### jsDelivr 镜像
- 默认：本地资源
- 可选：JSDMirror、渺软公益 CDN、晓白云公益 CDN 等

### 附件上传
- 支持禁止特定后缀自动插入
- 支持一键插入所有图片

## 版本发布流程

1. 更新 `Plugin.php` 中的 `@version` 标签
2. 运行 `npm run prod` 构建前端资源
3. 运行 `npm run pack` 打包插件
4. 在 `pack/` 目录获取发布包

## 注意事项

- 插件依赖 Typecho 1.2.0+ 版本
- 前端构建产物已包含在仓库中（`assets/dist/`）
- 修改前端资源后必须重新构建
- 禁用插件时可选择保留或清理数据
