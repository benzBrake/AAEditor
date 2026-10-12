# AAEditor 开发约定

本文档承接原 `.claude/CLAUDE.md` 中的项目开发说明。根目录 `AGENTS.md` 只保留必须跨任务遵守的规则和文档入口；本文件是详细开发约定的唯一正文来源。

## 项目概述

AAEditor 是一个用于 Typecho 的增强编辑器插件，提供 Markdown 编辑、实时预览、公式支持、代码高亮和图片处理等功能。

## 技术栈

### 后端

- 语言：PHP。项目协作约定要求兼容 PHP 7.2 及以上版本。
- 框架：Typecho Plugin API。
- 数据库：由 Typecho 提供，支持范围随 Typecho 运行环境确定（常见为 SQLite/MySQL/PostgreSQL）。
- 命名空间：`TypechoPlugin\AAEditor`。

源码包含针对 Typecho 1.3.0 及以上版本的分支判断；原开发说明记录的最低版本为 1.2.0，而当前 `README.md` 写的是 1.3.0+。在调整兼容范围前，应同时核对 `Plugin::isTypechoGe13()`、测试和用户文档。

### 前端

- 构建工具：Laravel Mix 6。
- 打包工具：Webpack 5。
- 语言：原生 JavaScript（ES6+）。
- 样式：SCSS。
- 依赖管理：npm。
- 代码高亮：Highlight.js 11。
- Markdown 解析：Marked 4。
- 公式渲染：MathJax SVG 输出（按插件配置加载）。

具体依赖版本以 `package.json` 和 `package-lock.json` 为准，不要只根据本文档升级依赖。

## 项目结构

```text
AAEditor/
├── Plugin.php              # 插件主文件，实现插件接口和配置页面
├── Util.php                # 编辑器、前台钩子和通用工具
├── HyperDown.php           # Markdown 解析器
├── assets/
│   ├── src/                # 前端源码
│   ├── dist/               # 构建产物，已纳入版本控制
│   ├── json/               # 表情、字符和符号配置
│   └── mirror/             # CDN 镜像资源
├── tools/                  # 打包脚本和排除列表
├── pack/                   # 打包产物，自动生成
├── tests/                  # PHP/JavaScript 测试和测试数据
├── webpack.mix.js          # Laravel Mix/Webpack 配置
└── package.json            # npm 脚本和依赖配置
```

## 开发工作流

### 安装依赖

```bash
npm install --legacy-peer-deps
```

### 开发模式

```bash
npm run dev
# 或持续监听
npm run watch
```

`npm run dev` 和 `npm run watch` 使用 `webpack.mix.js` 中的开发配置；开发配置会启用 BrowserSync，代理目标由该配置决定。

### 生产构建

```bash
npm run prod
```

构建入口是 `assets/src/`，输出到 `assets/dist/`。生产构建会清理目标目录并生成版本化资源；提交前检查构建产物是否符合预期。

### 打包插件

Windows 使用：

```bash
npm run pack
```

该命令调用 `tools/pack.cmd` 和 `tools/pack-original.ps1`。Linux 或 macOS 可按环境直接运行：

```bash
cd tools
./pack.sh
```

打包脚本读取 `Plugin.php` 中的 `@package` 和 `@version`，按日期生成压缩包并写入 `pack/`。排除项维护在 `tools/pack.exclude`。

## 代码风格

### PHP

- 使用现有命名空间 `TypechoPlugin\AAEditor`。
- 类名使用 PascalCase，方法名使用 camelCase。
- 遵循现有 Typecho 插件 API 调用方式和错误处理方式。
- 修改 PHP 兼容性相关代码时，至少按 PHP 7.2 语法和类型特性检查。

### JavaScript

- 使用 ES6+ 语法。
- 优先使用 `const`/`let`，避免新增 `var`。
- 函数使用 camelCase。
- 避免污染全局变量；需要挂载到 Typecho 或窗口对象时，应沿用现有接口。

### SCSS

- 使用 BEM 命名习惯。
- 变量使用 `$` 前缀。
- 嵌套层级不超过 3 层。

## 核心功能模块

- 编辑器：Markdown 实时预览、代码块语法高亮、数学公式、图片拖拽上传、表格编辑和围栏区块。
- 短代码/围栏区块：自定义容器、提示框、警告框和折叠区块。
- 前台渲染：可选正文样式、Highlight.js、FontAwesome 和 MathJax 支持。
- 资源镜像：可在插件设置中选择本地资源或配置的 CDN 镜像。

修改上述功能时，同时检查 `Plugin.php`、`Util.php`、对应的 `assets/src/` 入口及其构建产物。

## 重要配置项

- jsDelivr 镜像：默认使用本地资源，也可选择项目支持的镜像。
- 附件上传：支持禁止特定后缀自动插入，以及一键插入全部图片。
- 插件停用：可选择停用时保留或清理插件数据；涉及数据清理的修改必须审慎检查 `activate`/`deactivate` 逻辑。

## 版本发布流程

1. 更新 `Plugin.php` 中的 `@version` 标签。
2. 运行 `npm run prod` 构建前端资源。
3. 运行 `npm run pack` 生成发布包。
4. 从 `pack/` 目录取得发布压缩包，并检查包内是否排除了开发文件和敏感文件。

用户安装、配置和功能说明维护在根目录 `README.md`；语法行为维护在 `SYNTAX.md`。当实现发生变化时，按影响范围同步更新这些用户文档。
