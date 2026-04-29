const DEFAULT_CODE_BLOCK_MESSAGES = {
    copyButtonTitle: "点击复制",
    copySuccessTitle: "复制成功",
    copyErrorTitle: "复制失败，请手动复制",
};

const LANGUAGE_ALIASES = {
    csharp: "cs",
    cs: "cs",
    fsharp: "fs",
    fs: "fs",
    golang: "go",
    go: "go",
    javascript: "javascript",
    js: "javascript",
    jsx: "jsx",
    markdown: "markdown",
    md: "markdown",
    powershell: "powershell",
    ps1: "powershell",
    py: "python",
    python: "python",
    rb: "ruby",
    ruby: "ruby",
    rs: "rust",
    rust: "rust",
    shell: "bash",
    shellscript: "bash",
    sh: "bash",
    bash: "bash",
    ts: "typescript",
    typescript: "typescript",
    tsx: "tsx",
    yml: "yaml",
    yaml: "yaml",
};

const LANGUAGE_LABELS = {
    bash: "Bash",
    css: "CSS",
    cs: "C#",
    fs: "F#",
    go: "Go",
    html: "HTML",
    javascript: "JavaScript",
    json: "JSON",
    jsx: "JSX",
    markdown: "Markdown",
    php: "PHP",
    powershell: "PowerShell",
    python: "Python",
    ruby: "Ruby",
    rust: "Rust",
    sql: "SQL",
    text: "Text",
    tsx: "TSX",
    typescript: "TypeScript",
    vue: "Vue",
    xml: "XML",
    yaml: "YAML",
};

const LINE_MARKER_SELECTOR = "span.line[data-start][data-end]";
const LINE_ANNOTATION_PATTERN = /(?:\s*)(?:(?:\/\/+|#|<!--|\/\*+|;|--)\s*)?\[\!code\s+([a-z+-]+)(?::(\d+))?\](?:\s*\*\/|\s*-->)?\s*$/i;
const copyIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path d="M6.9998 6V3C6.9998 2.44772 7.44752 2 7.9998 2H19.9998C20.5521 2 20.9998 2.44772 20.9998 3V17C20.9998 17.5523 20.5521 18 19.9998 18H16.9998V20.9991C16.9998 21.5519 16.5499 22 15.993 22H4.00666C3.45059 22 3 21.5554 3 20.9991L3.0026 7.00087C3.0027 6.44811 3.45264 6 4.00942 6H6.9998ZM5.00242 8L5.00019 20H14.9998V8H5.00242ZM8.9998 6H16.9998V16H18.9998V4H8.9998V6Z"></path></svg>';
const okIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26"><path d="M22.566406 4.730469L20.773438 3.511719C20.277344 3.175781 19.597656 3.304688 19.265625 3.796875L10.476563 16.757813L6.4375 12.71875C6.015625 12.296875 5.328125 12.296875 4.90625 12.71875L3.371094 14.253906C2.949219 14.675781 2.949219 15.363281 3.371094 15.789063L9.582031 22C9.929688 22.347656 10.476563 22.613281 10.96875 22.613281C11.460938 22.613281 11.957031 22.304688 12.277344 21.839844L22.855469 6.234375C23.191406 5.742188 23.0625 5.066406 22.566406 4.730469Z" fill="currentColor" /></svg>';

function escapeHtml(value = "") {
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function escapeAttribute(value = "") {
    return escapeHtml(value).replace(/`/g, "&#96;");
}

function defaultLanguageLabel(language = "") {
    return language
        .split(/[-_.]/)
        .filter(Boolean)
        .map((part) => {
            if (!part.length) {
                return "";
            }
            return part.charAt(0).toUpperCase() + part.slice(1);
        })
        .join(" ");
}

function normalizeLanguage(language = "") {
    const cleaned = String(language).trim().toLowerCase();
    if (!cleaned || !/^[_a-z0-9\-+#.]+$/i.test(cleaned)) {
        return "";
    }
    return LANGUAGE_ALIASES[cleaned] || cleaned;
}

function getLanguageLabel(language = "") {
    const normalized = normalizeLanguage(language);
    if (!normalized) {
        return "";
    }
    return LANGUAGE_LABELS[normalized] || defaultLanguageLabel(normalized);
}

function mergeMessages(messages = {}) {
    return {
        ...DEFAULT_CODE_BLOCK_MESSAGES,
        ...(messages || {}),
    };
}

function parseLineSpec(spec = "") {
    const lines = new Set();
    String(spec)
        .split(",")
        .map((segment) => segment.trim())
        .filter(Boolean)
        .forEach((segment) => {
            const rangeMatch = segment.match(/^(\d+)\s*-\s*(\d+)$/);
            if (rangeMatch) {
                let start = Number.parseInt(rangeMatch[1], 10);
                let end = Number.parseInt(rangeMatch[2], 10);
                if (Number.isNaN(start) || Number.isNaN(end)) {
                    return;
                }
                if (start > end) {
                    [start, end] = [end, start];
                }
                for (let line = start; line <= end; line += 1) {
                    lines.add(line);
                }
                return;
            }

            const lineNumber = Number.parseInt(segment, 10);
            if (!Number.isNaN(lineNumber) && lineNumber > 0) {
                lines.add(lineNumber);
            }
        });

    return lines;
}

function extractCodeLines(codeElement) {
    const lines = [];
    let anchorHtml = "";
    let buffer = "";

    const pushLine = () => {
        lines.push({
            anchorHtml,
            rawText: buffer,
        });
        anchorHtml = "";
        buffer = "";
    };

    codeElement.childNodes.forEach((node) => {
        if (node.nodeType === Node.ELEMENT_NODE && node.matches?.(LINE_MARKER_SELECTOR)) {
            if (anchorHtml || buffer.length > 0) {
                pushLine();
            }
            anchorHtml = node.outerHTML;
            return;
        }

        const text = node.textContent ?? "";
        const parts = text.split("\n");
        parts.forEach((part, index) => {
            buffer += part;
            if (index < parts.length - 1) {
                pushLine();
            }
        });
    });

    if (anchorHtml || buffer.length > 0 || lines.length > 0) {
        pushLine();
    }

    if (!lines.length) {
        lines.push({
            anchorHtml: "",
            rawText: codeElement.textContent ?? "",
        });
    }

    while (lines.length > 1) {
        const lastLine = lines[lines.length - 1];
        if (lastLine.anchorHtml !== "" || lastLine.rawText !== "") {
            break;
        }
        lines.pop();
    }

    return lines;
}

function stripLineAnnotation(rawLine = "") {
    const match = LINE_ANNOTATION_PATTERN.exec(rawLine);
    if (!match || typeof match.index !== "number") {
        return {
            text: rawLine,
            annotation: null,
        };
    }

    const action = String(match[1] || "").toLowerCase();
    const count = Math.max(Number.parseInt(match[2] || "1", 10) || 1, 1);

    return {
        text: rawLine.slice(0, match.index).replace(/\s+$/, ""),
        annotation: {
            action,
            count,
        },
    };
}

function splitHighlightedHtmlIntoLines(html = "") {
    if (html === "") {
        return [""];
    }

    const lines = [];
    const openTags = [];
    let current = "";
    let cursor = 0;

    while (cursor < html.length) {
        if (html[cursor] === "<") {
            const end = html.indexOf(">", cursor);
            if (end === -1) {
                current += html.slice(cursor);
                break;
            }

            const tag = html.slice(cursor, end + 1);
            const isClosing = /^<\//.test(tag);
            const isSelfClosing = /\/>$/.test(tag) || /^<br\s*\/?>$/i.test(tag);
            current += tag;

            if (!isClosing && !isSelfClosing) {
                const match = /^<([a-z0-9-]+)/i.exec(tag);
                if (match) {
                    openTags.push({
                        name: match[1].toLowerCase(),
                        open: tag,
                    });
                }
            } else if (isClosing) {
                const match = /^<\/([a-z0-9-]+)/i.exec(tag);
                if (match) {
                    const closingName = match[1].toLowerCase();
                    for (let index = openTags.length - 1; index >= 0; index -= 1) {
                        if (openTags[index].name === closingName) {
                            openTags.splice(index, 1);
                            break;
                        }
                    }
                }
            }

            cursor = end + 1;
            continue;
        }

        if (html[cursor] === "\n") {
            const closingHtml = openTags
                .slice()
                .reverse()
                .map((tag) => `</${tag.name}>`)
                .join("");
            lines.push(current + closingHtml);
            current = openTags.map((tag) => tag.open).join("");
            cursor += 1;
            continue;
        }

        current += html[cursor];
        cursor += 1;
    }

    lines.push(current);
    return lines;
}

function buildLineDescriptors(sourceLines, meta) {
    const highlightedLines = parseLineSpec(meta.highlightSpec);
    const descriptors = sourceLines.map((line, index) => {
        const stripped = stripLineAnnotation(line.rawText);
        return {
            anchorHtml: line.anchorHtml,
            text: stripped.text,
            annotations: stripped.annotation ? [stripped.annotation] : [],
            classes: new Set(),
            diffMarker: "",
            lineNumber: meta.lineNumbers ? meta.lineNumbersStart + index : null,
        };
    });

    highlightedLines.forEach((lineNumber) => {
        const target = descriptors[lineNumber - 1];
        if (target) {
            target.classes.add("is-highlighted");
        }
    });

    const applyRangeAction = (startIndex, count, callback) => {
        for (let offset = 0; offset < count; offset += 1) {
            const line = descriptors[startIndex + offset];
            if (!line) {
                break;
            }
            callback(line);
        }
    };

    descriptors.forEach((line, index) => {
        line.annotations.forEach(({action, count}) => {
            switch (action) {
                case "highlight":
                    applyRangeAction(index, count, (target) => target.classes.add("is-highlighted"));
                    break;
                case "focus":
                    applyRangeAction(index, count, (target) => target.classes.add("is-focused"));
                    break;
                case "++":
                    applyRangeAction(index, count, (target) => {
                        target.classes.add("is-diff-plus");
                        target.diffMarker = "+";
                    });
                    break;
                case "--":
                    applyRangeAction(index, count, (target) => {
                        target.classes.add("is-diff-minus");
                        target.diffMarker = "-";
                    });
                    break;
                case "warning":
                    applyRangeAction(index, count, (target) => target.classes.add("is-warning"));
                    break;
                case "error":
                    applyRangeAction(index, count, (target) => target.classes.add("is-error"));
                    break;
                default:
                    break;
            }
        });
    });

    const hasFocusedLines = descriptors.some((line) => line.classes.has("is-focused"));
    if (hasFocusedLines) {
        descriptors.forEach((line) => {
            if (!line.classes.has("is-focused")) {
                line.classes.add("is-muted");
            }
        });
    }

    return {
        descriptors,
        hasFocusedLines,
        text: descriptors.map((line) => line.text).join("\n"),
    };
}

function setHeader(preElement, meta) {
    const existingHeader = preElement.querySelector(":scope > .aaeditor-code-header");
    if (existingHeader) {
        existingHeader.remove();
    }

    if (!meta.title) {
        preElement.style.setProperty("--aaeditor-pre-title-height", "0px");
        return;
    }

    const header = document.createElement("div");
    header.className = "aaeditor-code-header";

    const main = document.createElement("div");
    main.className = "aaeditor-code-header-main";

    const title = document.createElement("span");
    title.className = "aaeditor-code-title";
    title.textContent = meta.title;
    main.appendChild(title);

    if (meta.languageLabel) {
        const badge = document.createElement("span");
        badge.className = "aaeditor-code-language";
        badge.textContent = meta.languageLabel;
        main.appendChild(badge);
    }

    const actions = document.createElement("div");
    actions.className = "aaeditor-code-header-actions";

    header.appendChild(main);
    header.appendChild(actions);

    preElement.insertBefore(header, preElement.firstChild);
    preElement.style.setProperty("--aaeditor-pre-title-height", `${header.offsetHeight}px`);
}

function renderCopyButtonIcon() {
    return `
        <span class="copy-icon copy-icon-default" aria-hidden="true">${copyIcon}</span>
        <span class="copy-icon copy-icon-success" aria-hidden="true">${okIcon}</span>
    `;
}

function fallbackCopyText(text) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.setAttribute("aria-hidden", "true");
    Object.assign(textarea.style, {
        position: "fixed",
        top: "0",
        left: "-9999px",
        opacity: "0",
        pointerEvents: "none",
    });
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    textarea.setSelectionRange(0, textarea.value.length);

    let copied = false;
    try {
        copied = document.execCommand("copy");
    } catch (error) {
    }

    textarea.remove();
    return copied;
}

async function copyText(text) {
    if (window.isSecureContext && navigator.clipboard && typeof navigator.clipboard.writeText === "function") {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (error) {
        }
    }

    return fallbackCopyText(text);
}

function setCopyState(button, state, messages) {
    window.clearTimeout(button._aaeditorCopyTimer);
    button.dataset.state = state;
    button.setAttribute(
        "title",
        state === "success"
            ? messages.copySuccessTitle
            : state === "error"
                ? messages.copyErrorTitle
                : messages.copyButtonTitle
    );

    if (state === "idle") {
        button.removeAttribute("aria-busy");
        return;
    }

    button._aaeditorCopyTimer = window.setTimeout(() => {
        button.dataset.state = "idle";
        button.removeAttribute("aria-busy");
        button.setAttribute("title", messages.copyButtonTitle);
    }, state === "success" ? 1400 : 1800);
}

function ensureCopyButton(preElement, text, messages) {
    let button = preElement.querySelector(":scope > .copy");
    const headerActions = preElement.querySelector(":scope > .aaeditor-code-header .aaeditor-code-header-actions");
    if (!button) {
        button = document.createElement("button");
        button.type = "button";
        button.className = "copy";
        button.dataset.state = "idle";
        button.innerHTML = renderCopyButtonIcon();
        button.setAttribute("aria-label", messages.copyButtonTitle);
        button.addEventListener("click", async () => {
            if (button.getAttribute("aria-busy") === "true") {
                return;
            }

            button.setAttribute("aria-busy", "true");
            const copied = await copyText(button.dataset.text || "");
            setCopyState(button, copied ? "success" : "error", messages);
        });
        button.addEventListener("keydown", (event) => {
            if (event.key !== "Enter" && event.key !== " ") {
                return;
            }
            event.preventDefault();
            button.click();
        });
    }

    const target = headerActions || preElement;
    if (button.parentElement !== target) {
        target.appendChild(button);
    }

    button.dataset.text = text;
    button.setAttribute("title", messages.copyButtonTitle);
    button.setAttribute("aria-label", messages.copyButtonTitle);
    setCopyState(button, "idle", messages);
}

function readMeta(preElement, codeElement) {
    const rawLanguage = preElement.dataset.codeLanguage
        || Array.from(codeElement.classList)
            .find((className) => className.startsWith("language-"))
            ?.replace(/^language-/, "")
        || "";
    const language = normalizeLanguage(rawLanguage);
    const lineNumbers = preElement.dataset.codeLineNumbers === "true";
    const lineNumbersStart = Math.max(
        Number.parseInt(preElement.dataset.codeLineNumbersStart || "1", 10) || 1,
        1
    );

    return {
        language,
        languageLabel: preElement.dataset.codeLanguageLabel || getLanguageLabel(language),
        title: preElement.dataset.codeTitle || "",
        highlightSpec: preElement.dataset.codeHighlight || "",
        lineNumbers,
        lineNumbersStart,
    };
}

function renderCodeLines(codeElement, descriptors, highlightedLines) {
    const finalLines = [];
    for (let index = 0; index < descriptors.length; index += 1) {
        const descriptor = descriptors[index];
        const lineHtml = index < highlightedLines.length ? highlightedLines[index] : "";
        const classes = ["aaeditor-code-line", ...descriptor.classes];
        const attrs = [];

        if (descriptor.lineNumber !== null) {
            attrs.push(`data-line-number="${descriptor.lineNumber}"`);
        }
        if (descriptor.diffMarker) {
            attrs.push(`data-diff-marker="${escapeAttribute(descriptor.diffMarker)}"`);
        }
        finalLines.push(
            `<span class="${classes.join(" ")}" ${attrs.join(" ")}>${descriptor.anchorHtml || ""}${lineHtml || "&#8203;"}</span>`
        );
    }
    codeElement.innerHTML = finalLines.join("");
}

function enhanceCodeBlock(codeElement, options = {}) {
    const preElement = codeElement.parentElement;
    if (!preElement || preElement.dataset.aaeditorEnhanced === "true") {
        return;
    }

    const hljs = window.hljs;
    if (!hljs || typeof hljs.highlightElement !== "function") {
        return;
    }

    const meta = readMeta(preElement, codeElement);
    const sourceLines = extractCodeLines(codeElement);
    const processed = buildLineDescriptors(sourceLines, meta);
    const messages = mergeMessages(options.messages);

    codeElement.textContent = processed.text;
    hljs.highlightElement(codeElement);

    const highlightedLines = splitHighlightedHtmlIntoLines(codeElement.innerHTML);
    renderCodeLines(codeElement, processed.descriptors, highlightedLines);

    preElement.classList.add("styled", "aaeditor-code-block");
    if (processed.hasFocusedLines) {
        preElement.classList.add("has-focused-lines");
    }
    if (meta.lineNumbers) {
        preElement.classList.add("has-line-numbers");
    }

    setHeader(preElement, meta);
    ensureCopyButton(preElement, processed.text, messages);
    preElement.dataset.aaeditorEnhanced = "true";
}

function enhanceCodeBlocks(root = document, options = {}) {
    const scope = root instanceof Element || root instanceof Document ? root : document;
    scope.querySelectorAll("pre > code").forEach((codeElement) => {
        enhanceCodeBlock(codeElement, options);
    });
}

if (typeof window !== "undefined") {
    window.AAEditorCodeBlocks = {
        DEFAULT_CODE_BLOCK_MESSAGES,
        enhanceCodeBlocks,
        getLanguageLabel,
        normalizeLanguage,
    };
}

export {
    DEFAULT_CODE_BLOCK_MESSAGES,
    enhanceCodeBlocks,
    getLanguageLabel,
    normalizeLanguage,
};
