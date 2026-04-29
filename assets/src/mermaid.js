import {DEFAULT_MERMAID_MESSAGES, enhanceMermaidBlocks} from "./utils/mermaidBlocks";

window.AAEditorMermaidBlocks = {
    ...(window.AAEditorMermaidBlocks || {}),
    DEFAULT_MERMAID_MESSAGES,
    enhanceMermaidBlocks,
};

function initMermaidBlocks(root = document) {
    return enhanceMermaidBlocks(root, window.AAEditorMermaidBlocksConfig || {});
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        initMermaidBlocks(document);
    });
} else {
    initMermaidBlocks(document);
}

document.addEventListener("pjax:complete", () => {
    initMermaidBlocks(document);
});
