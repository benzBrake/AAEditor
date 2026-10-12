# AAEditor 项目协作约定

AAEditor 是用于 Typecho 的增强编辑器插件，提供 Markdown 编辑、实时预览、代码高亮、公式支持和图片处理等能力。

## 必须遵守

- 如果 `AGENTS.local.md` 存在，先遵循其中约定。该文件只适用于本机环境，不能提交或复制其中的凭证、地址和其他敏感信息。
- PHP 兼容性：7.2（包括）及以上。
- 提交信息遵循 [docs/commit-convention.md](docs/commit-convention.md)。
- 修改 `assets/src/` 前端源码后，按需运行生产构建并检查已纳入版本控制的 `assets/dist/` 产物。
- 发布前按开发文档中的流程更新版本、构建并打包；不要把 `pack/` 生成物当作源码修改。

## 详细文档

- [开发约定](docs/development.md)：项目结构、技术栈、依赖安装、构建/打包命令、代码风格、功能模块和发布注意事项。修改源码、构建配置或发布流程前阅读。
- [提交信息约定](docs/commit-convention.md)：创建或检查提交信息时阅读。

用户安装、配置和功能说明见 [README.md](README.md)；语法行为见 [SYNTAX.md](SYNTAX.md)。
