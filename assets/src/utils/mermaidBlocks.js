const DEFAULT_MERMAID_MESSAGES = {
    sourceTab: "源码",
    previewTab: "预览",
    copyButtonTitle: "复制源码",
    copySuccessTitle: "复制成功",
    copyErrorTitle: "复制失败，请手动复制",
    zoomInTitle: "放大",
    zoomOutTitle: "缩小",
    resetViewTitle: "重置视图",
    fullscreenTitle: "网页全屏",
    exitFullscreenTitle: "退出网页全屏",
    renderErrorTitle: "Mermaid 渲染失败",
    renderUnavailableTitle: "Mermaid 渲染器未加载",
};

let blockCounter = 0;
let renderCounter = 0;
let initialized = false;
let activeFullscreenWrapper = null;
let fullscreenKeyBound = false;

const ICONS = {
    copy: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path d="M6.9998 6V3C6.9998 2.44772 7.44752 2 7.9998 2H19.9998C20.5521 2 20.9998 2.44772 20.9998 3V17C20.9998 17.5523 20.5521 18 19.9998 18H16.9998V20.9991C16.9998 21.5519 16.5499 22 15.993 22H4.00666C3.45059 22 3 21.5554 3 20.9991L3.0026 7.00087C3.0027 6.44811 3.45264 6 4.00942 6H6.9998ZM5.00242 8L5.00019 20H14.9998V8H5.00242ZM8.9998 6H16.9998V16H18.9998V4H8.9998V6Z"></path></svg>',
    copySuccess: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26"><path d="M22.566406 4.730469L20.773438 3.511719C20.277344 3.175781 19.597656 3.304688 19.265625 3.796875L10.476563 16.757813L6.4375 12.71875C6.015625 12.296875 5.328125 12.296875 4.90625 12.71875L3.371094 14.253906C2.949219 14.675781 2.949219 15.363281 3.371094 15.789063L9.582031 22C9.929688 22.347656 10.476563 22.613281 10.96875 22.613281C11.460938 22.613281 11.957031 22.304688 12.277344 21.839844L22.855469 6.234375C23.191406 5.742188 23.0625 5.066406 22.566406 4.730469Z" fill="currentColor" /></svg>',
    zoomIn: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4a7 7 0 1 0 4.39 12.45l3.58 3.58 1.41-1.41-3.58-3.58A7 7 0 0 0 11 4Zm0 2a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm-1 2v2H8v2h2v2h2v-2h2v-2h-2V8h-2Z"/></svg>',
    zoomOut: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4a7 7 0 1 0 4.39 12.45l3.58 3.58 1.41-1.41-3.58-3.58A7 7 0 0 0 11 4Zm0 2a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm-3 4v2h6v-2H8Z"/></svg>',
    fullscreen: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h6v2H7v4H5V5Zm8 0h6v6h-2V7h-4V5ZM5 13h2v4h4v2H5v-6Zm12 0h2v6h-6v-2h4v-4Z"/></svg>',
};

function normalizeMermaidLanguage(language = "") {
    const normalized = String(language).trim().toLowerCase();
    return normalized === "mermaid" || normalized === "mmd" ? "mermaid" : "";
}

function mergeMessages(messages = {}) {
    return {
        ...DEFAULT_MERMAID_MESSAGES,
        ...(messages || {}),
    };
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
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
    window.clearTimeout(button._aaeditorMermaidCopyTimer);
    button.dataset.state = state;
    const label = state === "success"
        ? messages.copySuccessTitle
        : state === "error"
            ? messages.copyErrorTitle
            : messages.copyButtonTitle;

    button.setAttribute("title", label);
    button.dataset.tooltip = label;

    if (state === "idle") {
        button.removeAttribute("aria-busy");
        button.setAttribute("aria-label", messages.copyButtonTitle);
        return;
    }

    button.setAttribute(
        "aria-label",
        state === "success" ? messages.copySuccessTitle : messages.copyErrorTitle
    );
    button._aaeditorMermaidCopyTimer = window.setTimeout(() => {
        button.dataset.state = "idle";
        button.removeAttribute("aria-busy");
        button.setAttribute("title", messages.copyButtonTitle);
        button.dataset.tooltip = messages.copyButtonTitle;
        button.setAttribute("aria-label", messages.copyButtonTitle);
    }, state === "success" ? 1400 : 1800);
}

function isMermaidCodeBlock(preElement) {
    if (!(preElement instanceof Element) || preElement.tagName.toLowerCase() !== "pre") {
        return false;
    }

    const codeElement = preElement.querySelector(":scope > code");
    if (!codeElement) {
        return false;
    }

    const language = normalizeMermaidLanguage(preElement.dataset.codeLanguage || "");
    return language === "mermaid"
        || codeElement.classList.contains("language-mermaid")
        || codeElement.classList.contains("language-mmd");
}

function resolveMermaidBlocks(root = document) {
    const blocks = [];
    const scope = root instanceof Element || root instanceof Document ? root : document;

    if (scope instanceof Element) {
        const preElement = scope.tagName.toLowerCase() === "code" ? scope.parentElement : scope;
        if (isMermaidCodeBlock(preElement)) {
            blocks.push(preElement);
        }
    }

    if (typeof scope.querySelectorAll === "function") {
        scope
            .querySelectorAll("pre[data-code-language], pre > code.language-mermaid, pre > code.language-mmd")
            .forEach((element) => {
                const preElement = element.tagName.toLowerCase() === "pre" ? element : element.parentElement;
                if (isMermaidCodeBlock(preElement)) {
                    blocks.push(preElement);
                }
            });
    }

    return Array.from(new Set(blocks));
}

function getSourceText(preElement, codeElement) {
    const copyButton = preElement.querySelector(":scope .copy[data-text]");
    const text = copyButton?.dataset.text ?? codeElement.textContent ?? "";
    return text.replace(/\u200b/g, "");
}

function ensureMermaidInitialized(mermaid, options = {}) {
    if (initialized || !mermaid || typeof mermaid.initialize !== "function") {
        return;
    }

    mermaid.initialize({
        startOnLoad: false,
        securityLevel: "strict",
        ...(options.mermaid || {}),
    });
    initialized = true;
}

function activateTab(wrapper, targetName, focusButton = false) {
    const buttons = Array.from(wrapper.querySelectorAll(":scope > .aaeditor-mermaid-toolbar .aaeditor-mermaid-tabs [role='tab']"));
    const panels = Array.from(wrapper.querySelectorAll(":scope > .aaeditor-mermaid-panels > [role='tabpanel']"));

    buttons.forEach((button) => {
        const isActive = button.dataset.tabTarget === targetName;
        button.setAttribute("aria-selected", isActive ? "true" : "false");
        button.setAttribute("tabindex", isActive ? "0" : "-1");
        button.classList.toggle("is-active", isActive);
        if (isActive && focusButton) {
            button.focus();
        }
    });

    panels.forEach((panel) => {
        const isActive = panel.dataset.tabPanel === targetName;
        panel.hidden = !isActive;
        panel.setAttribute("aria-hidden", isActive ? "false" : "true");
        panel.classList.toggle("is-active", isActive);
    });
}

function bindTabEvents(wrapper) {
    const buttons = Array.from(wrapper.querySelectorAll(":scope > .aaeditor-mermaid-toolbar .aaeditor-mermaid-tabs [role='tab']"));
    buttons.forEach((button, index) => {
        if (button.dataset.mermaidTabBound === "true") {
            return;
        }
        button.dataset.mermaidTabBound = "true";
        button.addEventListener("click", () => activateTab(wrapper, button.dataset.tabTarget || "preview"));
        button.addEventListener("keydown", (event) => {
            const keys = ["ArrowLeft", "ArrowRight", "Home", "End"];
            if (!keys.includes(event.key)) {
                return;
            }
            event.preventDefault();

            let nextIndex = index;
            if (event.key === "ArrowLeft") {
                nextIndex = index <= 0 ? buttons.length - 1 : index - 1;
            } else if (event.key === "ArrowRight") {
                nextIndex = index >= buttons.length - 1 ? 0 : index + 1;
            } else if (event.key === "Home") {
                nextIndex = 0;
            } else if (event.key === "End") {
                nextIndex = buttons.length - 1;
            }

            activateTab(wrapper, buttons[nextIndex].dataset.tabTarget || "preview", true);
        });
    });
}

function getViewState(wrapper) {
    if (!wrapper._aaeditorMermaidViewState) {
        wrapper._aaeditorMermaidViewState = {
            scale: 1,
            x: 0,
            y: 0,
            baseWidth: 0,
            baseHeight: 0,
        };
    }
    return wrapper._aaeditorMermaidViewState;
}

function readSvgSize(svg) {
    if (!svg) {
        return {
            width: 0,
            height: 0,
        };
    }

    const viewBox = svg.viewBox?.baseVal;
    if (viewBox && viewBox.width > 0 && viewBox.height > 0) {
        return {
            width: viewBox.width,
            height: viewBox.height,
        };
    }

    return {
        width: Number.parseFloat(svg.getAttribute("width") || "") || svg.clientWidth || 0,
        height: Number.parseFloat(svg.getAttribute("height") || "") || svg.clientHeight || 0,
    };
}

function rememberSvgSize(wrapper) {
    const state = getViewState(wrapper);
    const svg = wrapper.querySelector(":scope .aaeditor-mermaid-canvas > svg");
    const size = readSvgSize(svg);
    state.baseWidth = size.width || state.baseWidth;
    state.baseHeight = size.height || state.baseHeight;
}

function updateView(wrapper) {
    const state = getViewState(wrapper);
    const canvas = wrapper.querySelector(":scope .aaeditor-mermaid-canvas");
    const svg = canvas?.querySelector(":scope > svg");
    const zoomValue = wrapper.querySelector(":scope .aaeditor-mermaid-zoom-value");
    if (canvas) {
        canvas.style.left = `calc(50% + ${state.x}px)`;
        canvas.style.top = `calc(50% + ${state.y}px)`;
    }
    if (svg && state.baseWidth > 0 && state.baseHeight > 0) {
        svg.style.maxWidth = "none";
        svg.style.width = `${state.baseWidth * state.scale}px`;
        svg.style.height = `${state.baseHeight * state.scale}px`;
    }
    if (zoomValue) {
        zoomValue.textContent = `${Math.round(state.scale * 100)}%`;
    }
}

function resetView(wrapper) {
    const state = getViewState(wrapper);
    state.scale = 1;
    state.x = 0;
    state.y = 0;
    updateView(wrapper);
}

function zoomView(wrapper, nextScale, origin = null) {
    const state = getViewState(wrapper);
    const canvas = wrapper.querySelector(":scope .aaeditor-mermaid-canvas");
    const previousScale = state.scale;
    const scale = clamp(nextScale, 0.25, 4);
    if (Math.abs(scale - previousScale) < 0.001) {
        return;
    }

    if (canvas && origin) {
        const beforeRect = canvas.getBoundingClientRect();
        const relativeX = (origin.clientX - beforeRect.left) / previousScale;
        const relativeY = (origin.clientY - beforeRect.top) / previousScale;
        state.scale = scale;
        updateView(wrapper);
        const afterRect = canvas.getBoundingClientRect();
        state.x += origin.clientX - (afterRect.left + relativeX * scale);
        state.y += origin.clientY - (afterRect.top + relativeY * scale);
        updateView(wrapper);
        return;
    }

    state.scale = scale;
    updateView(wrapper);
}

function panView(wrapper, dx, dy) {
    const state = getViewState(wrapper);
    state.x += dx;
    state.y += dy;
    updateView(wrapper);
}

function ensureFullscreenKeyHandler() {
    if (fullscreenKeyBound) {
        return;
    }
    fullscreenKeyBound = true;
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && activeFullscreenWrapper) {
            event.preventDefault();
            setWebFullscreen(activeFullscreenWrapper, false);
        }
    });
}

function setWebFullscreen(wrapper, enabled) {
    if (enabled && activeFullscreenWrapper && activeFullscreenWrapper !== wrapper) {
        setWebFullscreen(activeFullscreenWrapper, false);
    }

    const messages = mergeMessages(wrapper._aaeditorMermaidMessages);
    const fullscreenButton = wrapper.querySelector(":scope .aaeditor-mermaid-action[data-action='fullscreen']");
    wrapper.classList.toggle("is-web-fullscreen", enabled);
    document.body.classList.toggle("aaeditor-mermaid-fullscreen-open", enabled);

    if (fullscreenButton) {
        fullscreenButton.classList.toggle("is-active", enabled);
        fullscreenButton.setAttribute("aria-pressed", enabled ? "true" : "false");
        fullscreenButton.setAttribute("title", enabled ? messages.exitFullscreenTitle : messages.fullscreenTitle);
        fullscreenButton.setAttribute("aria-label", enabled ? messages.exitFullscreenTitle : messages.fullscreenTitle);
    }

    activeFullscreenWrapper = enabled ? wrapper : null;
    if (enabled) {
        ensureFullscreenKeyHandler();
        activateTab(wrapper, "preview");
        window.setTimeout(() => {
            wrapper.querySelector(":scope .aaeditor-mermaid-viewport")?.focus({preventScroll: true});
        }, 0);
    }
}

function createPreviewViewport() {
    const viewport = document.createElement("div");
    viewport.className = "aaeditor-mermaid-viewport";
    viewport.tabIndex = 0;

    const canvas = document.createElement("div");
    canvas.className = "aaeditor-mermaid-canvas";
    viewport.appendChild(canvas);
    return {
        viewport,
        canvas,
    };
}

function bindPreviewViewport(wrapper, viewport) {
    if (viewport.dataset.mermaidViewportBound === "true") {
        return;
    }
    viewport.dataset.mermaidViewportBound = "true";

    let dragging = false;
    let lastX = 0;
    let lastY = 0;

    viewport.addEventListener("pointerdown", (event) => {
        if (event.button !== 0) {
            return;
        }
        event.preventDefault();
        dragging = true;
        lastX = event.clientX;
        lastY = event.clientY;
        viewport.classList.add("is-dragging");
        viewport.setPointerCapture?.(event.pointerId);
    });

    viewport.addEventListener("pointermove", (event) => {
        if (!dragging) {
            return;
        }
        panView(wrapper, event.clientX - lastX, event.clientY - lastY);
        lastX = event.clientX;
        lastY = event.clientY;
    });

    const stopDragging = (event) => {
        if (!dragging) {
            return;
        }
        dragging = false;
        viewport.classList.remove("is-dragging");
        viewport.releasePointerCapture?.(event.pointerId);
    };
    viewport.addEventListener("pointerup", stopDragging);
    viewport.addEventListener("pointercancel", stopDragging);

    viewport.addEventListener("wheel", (event) => {
        event.preventDefault();
        const direction = event.deltaY < 0 ? 1 : -1;
        const factor = direction > 0 ? 1.12 : 1 / 1.12;
        zoomView(wrapper, getViewState(wrapper).scale * factor, event);
    }, {passive: false});

    viewport.addEventListener("keydown", (event) => {
        if (event.key === "+" || event.key === "=") {
            event.preventDefault();
            zoomView(wrapper, getViewState(wrapper).scale * 1.12);
        } else if (event.key === "-" || event.key === "_") {
            event.preventDefault();
            zoomView(wrapper, getViewState(wrapper).scale / 1.12);
        } else if (event.key === "0") {
            event.preventDefault();
            resetView(wrapper);
        } else if (event.key === "ArrowLeft") {
            event.preventDefault();
            panView(wrapper, 24, 0);
        } else if (event.key === "ArrowRight") {
            event.preventDefault();
            panView(wrapper, -24, 0);
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            panView(wrapper, 0, 24);
        } else if (event.key === "ArrowDown") {
            event.preventDefault();
            panView(wrapper, 0, -24);
        }
    });
}

function renderError(previewElement, title, detail = "") {
    previewElement.classList.add("has-error");
    previewElement.innerHTML = "";

    const error = document.createElement("div");
    error.className = "aaeditor-mermaid-error";

    const heading = document.createElement("strong");
    heading.textContent = title;
    error.appendChild(heading);

    if (detail) {
        const message = document.createElement("pre");
        message.textContent = detail;
        error.appendChild(message);
    }

    previewElement.appendChild(error);
}

function createActionButton(action, title, icon) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "aaeditor-mermaid-action aaeditor-mermaid-tooltip";
    button.dataset.action = action;
    button.dataset.tooltip = title;
    button.setAttribute("title", title);
    button.setAttribute("aria-label", title);
    button.innerHTML = icon;
    return button;
}

function createCopyButton(messages) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "copy aaeditor-mermaid-copy aaeditor-mermaid-tooltip";
    button.dataset.action = "copy";
    button.dataset.state = "idle";
    button.dataset.tooltip = messages.copyButtonTitle;
    button.setAttribute("title", messages.copyButtonTitle);
    button.setAttribute("aria-label", messages.copyButtonTitle);
    button.innerHTML = `
        <span class="copy-icon copy-icon-default" aria-hidden="true">${ICONS.copy}</span>
        <span class="copy-icon copy-icon-success" aria-hidden="true">${ICONS.copySuccess}</span>
    `;
    return button;
}

function bindToolbarActions(wrapper, source, messages) {
    const copyButton = wrapper.querySelector(":scope .aaeditor-mermaid-copy[data-action='copy']");
    if (copyButton && copyButton.dataset.mermaidActionBound !== "true") {
        copyButton.dataset.mermaidActionBound = "true";
        copyButton.addEventListener("click", async () => {
            if (copyButton.getAttribute("aria-busy") === "true") {
                return;
            }
            copyButton.setAttribute("aria-busy", "true");
            const copied = await copyText(source);
            setCopyState(copyButton, copied ? "success" : "error", messages);
        });
    }

    const actionMap = {
        zoomOut: () => zoomView(wrapper, getViewState(wrapper).scale / 1.16),
        zoomIn: () => zoomView(wrapper, getViewState(wrapper).scale * 1.16),
        reset: () => resetView(wrapper),
        fullscreen: () => setWebFullscreen(wrapper, !wrapper.classList.contains("is-web-fullscreen")),
    };

    Object.keys(actionMap).forEach((action) => {
        const button = wrapper.querySelector(`:scope [data-action='${action}']`);
        if (!button || button.dataset.mermaidActionBound === "true") {
            return;
        }
        button.dataset.mermaidActionBound = "true";
        button.addEventListener("click", () => {
            if (action !== "copy") {
                activateTab(wrapper, "preview");
            }
            actionMap[action]();
        });
    });
}

async function renderPreview(wrapper, source, options, messages) {
    const previewElement = wrapper.querySelector(":scope .aaeditor-mermaid-render");
    const mermaid = window.mermaid;
    if (!previewElement) {
        return;
    }

    previewElement.classList.remove("has-error");
    if (!mermaid || typeof mermaid.render !== "function") {
        renderError(previewElement, messages.renderUnavailableTitle);
        return;
    }

    ensureMermaidInitialized(mermaid, options);
    const renderId = `aaeditor-mermaid-${Date.now()}-${++renderCounter}`;
    previewElement.dataset.renderState = "loading";

    try {
        const result = await mermaid.render(renderId, source);
        previewElement.innerHTML = "";
        const {viewport, canvas} = createPreviewViewport();
        canvas.innerHTML = result.svg || "";
        previewElement.appendChild(viewport);
        rememberSvgSize(wrapper);
        bindPreviewViewport(wrapper, viewport);
        resetView(wrapper);
        if (typeof result.bindFunctions === "function") {
            result.bindFunctions(canvas);
        }
        previewElement.dataset.renderState = "complete";
    } catch (error) {
        previewElement.dataset.renderState = "error";
        renderError(previewElement, messages.renderErrorTitle, error?.message || String(error || ""));
    }
}

function createButton(id, panelId, target, text, active = false) {
    const button = document.createElement("button");
    button.type = "button";
    button.id = id;
    button.className = "aaeditor-mermaid-tab";
    button.dataset.tabTarget = target;
    button.setAttribute("role", "tab");
    button.setAttribute("aria-controls", panelId);
    button.setAttribute("aria-selected", active ? "true" : "false");
    button.setAttribute("tabindex", active ? "0" : "-1");
    button.textContent = text;
    return button;
}

function createPanel(id, tabId, target, active = false) {
    const panel = document.createElement("div");
    panel.id = id;
    panel.className = "aaeditor-mermaid-panel";
    panel.dataset.tabPanel = target;
    panel.setAttribute("role", "tabpanel");
    panel.setAttribute("aria-labelledby", tabId);
    panel.setAttribute("aria-hidden", active ? "false" : "true");
    panel.hidden = !active;
    panel.classList.toggle("is-active", active);
    return panel;
}

function enhanceMermaidBlock(preElement, options = {}) {
    const codeElement = preElement.querySelector(":scope > code");
    if (!codeElement || preElement.dataset.aaeditorMermaidEnhanced === "true") {
        return null;
    }

    const parent = preElement.parentNode;
    if (!parent) {
        return null;
    }

    const messages = mergeMessages(options.messages);
    const source = getSourceText(preElement, codeElement);
    const id = `aaeditor-mermaid-block-${++blockCounter}`;
    const sourceTabId = `${id}-source-tab`;
    const previewTabId = `${id}-preview-tab`;
    const sourcePanelId = `${id}-source-panel`;
    const previewPanelId = `${id}-preview-panel`;

    const wrapper = document.createElement("div");
    wrapper.className = "aaeditor-mermaid-block";
    wrapper.dataset.aaeditorMermaidBlock = "true";
    wrapper._aaeditorMermaidMessages = messages;

    const toolbar = document.createElement("div");
    toolbar.className = "aaeditor-mermaid-toolbar";

    const tabs = document.createElement("div");
    tabs.className = "aaeditor-mermaid-tabs";
    tabs.setAttribute("role", "tablist");
    tabs.setAttribute("aria-label", "Mermaid");
    tabs.appendChild(createButton(sourceTabId, sourcePanelId, "source", messages.sourceTab, false));
    tabs.appendChild(createButton(previewTabId, previewPanelId, "preview", messages.previewTab, true));

    const actions = document.createElement("div");
    actions.className = "aaeditor-mermaid-actions";
    actions.appendChild(createActionButton("zoomOut", messages.zoomOutTitle, ICONS.zoomOut));

    const zoomValue = document.createElement("button");
    zoomValue.type = "button";
    zoomValue.className = "aaeditor-mermaid-zoom-value aaeditor-mermaid-tooltip";
    zoomValue.dataset.action = "reset";
    zoomValue.dataset.tooltip = messages.resetViewTitle;
    zoomValue.setAttribute("title", messages.resetViewTitle);
    zoomValue.setAttribute("aria-label", messages.resetViewTitle);
    zoomValue.setAttribute("aria-live", "polite");
    zoomValue.textContent = "100%";
    actions.appendChild(zoomValue);

    actions.appendChild(createActionButton("zoomIn", messages.zoomInTitle, ICONS.zoomIn));
    const fullscreenButton = createActionButton("fullscreen", messages.fullscreenTitle, ICONS.fullscreen);
    fullscreenButton.setAttribute("aria-pressed", "false");
    actions.appendChild(fullscreenButton);
    actions.appendChild(createCopyButton(messages));

    toolbar.appendChild(tabs);
    toolbar.appendChild(actions);

    const panels = document.createElement("div");
    panels.className = "aaeditor-mermaid-panels";

    const sourcePanel = createPanel(sourcePanelId, sourceTabId, "source", false);
    const previewPanel = createPanel(previewPanelId, previewTabId, "preview", true);
    const previewRender = document.createElement("div");
    previewRender.className = "aaeditor-mermaid-render";
    previewRender.dataset.renderState = "idle";
    previewPanel.appendChild(previewRender);

    parent.insertBefore(wrapper, preElement);
    preElement.querySelectorAll(":scope .copy").forEach((button) => button.remove());
    sourcePanel.appendChild(preElement);
    panels.appendChild(sourcePanel);
    panels.appendChild(previewPanel);
    wrapper.appendChild(toolbar);
    wrapper.appendChild(panels);

    preElement.dataset.aaeditorMermaidEnhanced = "true";
    bindTabEvents(wrapper);
    bindToolbarActions(wrapper, source, messages);
    activateTab(wrapper, "preview");

    return renderPreview(wrapper, source, options, messages);
}

function enhanceMermaidBlocks(root = document, options = {}) {
    const tasks = resolveMermaidBlocks(root)
        .map((preElement) => enhanceMermaidBlock(preElement, options))
        .filter(Boolean);
    return Promise.all(tasks);
}

if (typeof window !== "undefined") {
    window.AAEditorMermaidBlocks = {
        ...(window.AAEditorMermaidBlocks || {}),
        DEFAULT_MERMAID_MESSAGES,
        enhanceMermaidBlocks,
    };
}

export {
    DEFAULT_MERMAID_MESSAGES,
    enhanceMermaidBlocks,
};
