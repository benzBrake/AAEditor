# Git 提交约定

## 提交标题

使用带 emoji 的 Conventional Commits 格式：

```text
<emoji> <type>(<scope>): <subject>
```

- 每个提交聚焦于一项改动；`scope` 可选。
- 标题不超过 72 个字符，使用祈使语气；中英文按改动上下文和近期提交保持一致。
- 使用与提交类型匹配的 emoji：

| Emoji | 类型 | 用途 |
| --- | --- | --- |
| ✨ | `feat` | 新增功能 |
| 🐛 | `fix` | 修复缺陷 |
| 📝 | `docs` | 文档或注释 |
| 🎨 | `style` | 不改变语义的格式或风格调整 |
| ♻️ | `refactor` | 不新增功能、不修复缺陷的重构 |
| ⚡️ | `perf` | 性能优化 |
| ✅ | `test` | 新增或修复测试 |
| 🔧 | `chore` | 构建、工具或其他维护工作 |
| 👷 | `ci` | CI/CD 配置与脚本 |
| ⏪️ | `revert` | 回滚提交 |

破坏性变更使用 `!` 标记类型，并在正文中添加 `BREAKING CHANGE: <说明>`。

## 提交正文

标题与正文之间空一行。需要补充上下文时，用不超过三条以祈使动词开头的列表说明动机、实现或影响；不要使用冒号式小标题。关联问题等信息使用 Git trailer 格式。

```text
🐛 fix(compat): restore PHP 7.2 support

- replace syntax unsupported by PHP 7.2
- preserve automatic font loading behavior
```
