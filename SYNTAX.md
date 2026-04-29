# AAEditor 语法说明

AAEditor 使用 HyperDown 解析 Markdown，并在此基础上扩展了代码块、数学公式和 `:::` 围栏块。本文只说明当前实现支持的写法。

## Markdown 基础语法

### 标题

```markdown
# 一级标题
## 二级标题
### 三级标题

一级标题
==

二级标题
--
```

### 段落与换行

普通文本会解析为段落。段落内换行会输出 `<br>`，空行会分隔段落。

```markdown
第一行
第二行

新的段落
```

### 强调、删除线与行内代码

```markdown
**加粗**
*斜体*
***加粗斜体***
~~删除线~~
`行内代码`
```

### 列表

```markdown
- 无序列表
- 无序列表

1. 有序列表
2. 有序列表

3. 有序列表可从指定数字开始
4. 下一项
```

### 引用

```markdown
> 这是一段引用。
> 可以包含多行内容。
```

### 链接与图片

```markdown
[链接文字](https://example.com)
[带标题的链接](https://example.com "标题")

![图片描述](https://example.com/image.jpg)
![带标题的图片](https://example.com/image.jpg "标题")

<https://example.com>
<user@example.com>
```

也支持引用式链接和图片：

```markdown
[链接文字][site]
![图片描述][img]

[site]: https://example.com
[img]: https://example.com/image.jpg
```

### 表格

```markdown
| 名称 | 数量 | 备注 |
| :--- | ---: | :--: |
| 苹果 | 3 | 新鲜 |
| 橙子 | 5 | 推荐 |
```

分隔行中的 `:---`、`---:`、`:---:` 分别表示左对齐、右对齐、居中。

### 脚注

```markdown
这里引用一个脚注[^note]。

[^note]: 这里是脚注内容。
```

### 水平线

```markdown
---
***
```

## 代码块增强

AAEditor 支持反引号和波浪线代码块：

````markdown
```javascript
console.log("Hello AAEditor");
```

~~~php
echo "Hello AAEditor";
~~~
````

代码块信息字符串支持语言、行号、高亮行和标题：

````markdown
```javascript:line-numbers=10 {1,3-4} [示例标题]
const name = "AAEditor";
console.log(name);
console.log("line 3");
console.log("line 4");
```
````

格式说明：

- `language`：代码语言，会输出到 `language-*` class，并显示语言标签。
- `:line-numbers`：显示行号，从 1 开始。
- `:line-numbers=10`：显示行号，从 10 开始。
- `:no-line-numbers`：明确关闭行号。
- `:legacyRel`：不匹配行号选项时会作为旧版 `rel` 值保留。
- `{1,3-5}`：高亮第 1 行和第 3 到 5 行。
- `[标题]`：代码块标题，必须写在信息字符串最后。

常见语言别名会被规范化，例如 `js` 会显示为 `JavaScript`，`ts` 会显示为 `TypeScript`，`py` 会显示为 `Python`。

### 行内注释标记

在代码行末尾加入 `[!code ...]` 可以控制该行或后续多行样式。渲染时标记会从代码内容中移除。

````markdown
```javascript:line-numbers
const a = 1; // [!code highlight]
const b = 2; // [!code focus:2]
const c = 3;
const added = true; // [!code ++]
const removed = false; // [!code --]
const warn = "check"; // [!code warning]
throw new Error("failed"); // [!code error]
```
````

支持的动作：

- `highlight`：高亮行。
- `focus`：聚焦行；存在聚焦行时，其他行会变淡。
- `++`：新增行样式。
- `--`：删除行样式。
- `warning`：警告行样式。
- `error`：错误行样式。

动作后可追加 `:数量`，例如 `[!code focus:3]` 表示从当前行开始连续 3 行。标记可以直接写在行尾，也可以跟在 `//`、`#`、`<!-- -->`、`/* */`、`;`、`--` 等注释符后。

## 数学公式

行内公式：

```markdown
这是行内公式 $E = mc^2$。
```

块级公式需要用独占行的 `$$` 包裹：

```markdown
$$
\int_0^1 x^2 dx = \frac{1}{3}
$$
```

公式实际渲染依赖插件设置中的 MathJax 支持。

## 围栏块

围栏块使用 `:::` 包裹：

```markdown
::: info 标题
这里是内容。
:::
```

基本格式：

```markdown
::: 类型[:变体] {属性: 值, 其他属性: 值} 标题
内容
:::
```

属性会输出为 `data-*` 属性，例如 `{columns: 3, gap: 8px}` 会输出 `data-columns="3"` 和 `data-gap="8px"`。

### 信息块

支持以下类型：

- `info`、`note`：信息块。
- `tip`、`success`：成功或提示块。
- `important`：重要提示块。
- `warning`：警告块。
- `danger`、`caution`、`error`：错误或危险提示块。

```markdown
::: warning 注意
操作前请先备份数据。
:::
```

未写标题时，标题默认为类型名的大写。

### 折叠块 details

```markdown
::: details 更多信息
这里默认折叠。
:::

::: details:open 默认展开
这里默认展开。
:::
```

未写标题时，标题默认为 `Details`。

### 标签页 tabs

`tabs` 内部只接受多个 `tab` 子块；如果内容不符合结构，会回退为普通 `info` 块。

```markdown
::: tabs
::: tab 安装
npm install
:::
::: tab 使用
npm run dev
:::
:::
```

可用属性自定义 tabs 相关 class：

```markdown
::: tabs {tabs-wrapper-classes: tab-flat, panels-wrapper-classes: tab-panel-flat, tab-active-classes: tab-active, tab-inactive-classes: tab-inactive}
::: tab A
内容 A
:::
::: tab B
内容 B
:::
:::
```

支持的 tabs 属性：

- `tabs-wrapper-classes`
- `panels-wrapper-classes`
- `tab-classes`
- `panel-classes`
- `tab-active-classes`
- `tab-inactive-classes`

### 宫格 grid

适合图片或卡片列表。每行一个项目时，前端会整理为宫格项。

```markdown
::: grid {columns: 3, gap: 8px} 图片
![图片 1](https://example.com/1.jpg)
![图片 2](https://example.com/2.jpg)
![图片 3](https://example.com/3.jpg)
:::
```

自适应列数：

```markdown
::: grid {auto-fit: true, min-width: 220px, gap: 12px} 图片
![图片 1](https://example.com/1.jpg)
![图片 2](https://example.com/2.jpg)
:::
```

常用属性：

- `columns`：固定列数，例如 `3`。
- `gap`：间距，例如 `8px`。
- `auto-fit`：设为 `true` 时启用自适应列数。
- `min-width`：自适应列数时的最小宽度，例如 `220px`。

### 瀑布流 masonry

```markdown
::: masonry {columns: 3, gap: 16px} 瀑布流
![图片 1](https://example.com/1.jpg)

![图片 2](https://example.com/2.jpg)

![图片 3](https://example.com/3.jpg)
:::
```

常用属性：

- `columns`：列数，默认按前端逻辑使用 3。
- `gap`：间距，默认按前端逻辑使用 16。

### 原始内容 raw

`raw` 会直接输出内容，不再进行 Markdown 解析。

```markdown
::: raw
<div class="custom-html">自定义 HTML</div>
:::
```

## 任务列表

AAEditor 的预览和前台内容后处理支持 `[x]` 与 `[ ]`：

```markdown
[x] 已完成
[ ] 未完成
```

它们会被转换为禁用状态的复选框。
