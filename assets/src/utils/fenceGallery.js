function parseNumber(value, fallback) {
    const parsed = Number.parseInt(String(value ?? ""), 10);
    return Number.isFinite(parsed) ? parsed : fallback;
}

function isIgnorableWhitespace(node) {
    return node?.nodeType === Node.TEXT_NODE && node.textContent.trim() === "";
}

function isLineMarker(node) {
    return (
        node?.nodeType === Node.ELEMENT_NODE &&
        node.classList.contains("line") &&
        node.hasAttribute("data-start") &&
        node.hasAttribute("data-end")
    );
}

function gridItemHasContent(nodes) {
    return nodes.some((node) => {
        if (isLineMarker(node)) {
            return false;
        }

        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent.trim() !== "";
        }

        return node.nodeType === Node.ELEMENT_NODE;
    });
}

function createGridItems(nodes) {
    const items = [];
    let pendingMarkers = [];
    let currentNodes = [];

    const flush = () => {
        if (!gridItemHasContent(currentNodes)) {
            pendingMarkers = [];
            currentNodes = [];
            return;
        }

        const item = document.createElement("div");
        item.className = "grid-item";

        pendingMarkers.forEach((marker) => item.appendChild(marker));
        currentNodes.forEach((node) => item.appendChild(node));

        items.push(item);
        pendingMarkers = [];
        currentNodes = [];
    };

    nodes.forEach((node) => {
        if (isIgnorableWhitespace(node)) {
            return;
        }

        if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "BR") {
            flush();
            return;
        }

        if (isLineMarker(node)) {
            if (gridItemHasContent(currentNodes)) {
                flush();
            }
            pendingMarkers.push(node.cloneNode(true));
            return;
        }

        currentNodes.push(node.cloneNode(true));
    });

    flush();

    return items;
}

function normalizeGridFence(root = document) {
    if (!root || typeof root.querySelectorAll !== "function") {
        return;
    }

    root.querySelectorAll(".fence-grid .fence-content").forEach((content) => {
        const children = Array.from(content.childNodes);
        const hasWrappedParagraph = children.some(
            (child) => child.nodeType === Node.ELEMENT_NODE && child.tagName === "P"
        );
        const hasDirectLineMarker = children.some((child) => isLineMarker(child));

        if (!hasWrappedParagraph && !hasDirectLineMarker) {
            return;
        }

        if (!hasWrappedParagraph && hasDirectLineMarker) {
            const items = createGridItems(children);
            if (items.length) {
                content.replaceChildren(...items);
            }
            return;
        }

        const fragment = document.createDocumentFragment();
        let changed = false;

        children.forEach((child) => {
            if (isIgnorableWhitespace(child)) {
                return;
            }

            if (child.nodeType === Node.ELEMENT_NODE && child.tagName === "P") {
                const items = createGridItems(Array.from(child.childNodes));
                items.forEach((item) => fragment.appendChild(item));
                changed = true;
                return;
            }

            if (child.nodeType === Node.ELEMENT_NODE && child.tagName === "BR") {
                changed = true;
                return;
            }

            fragment.appendChild(child.cloneNode(true));
        });

        if (!changed) {
            return;
        }

        content.replaceChildren(fragment);
    });
}

function normalizeMasonryFence(root = document) {
    if (!root || typeof root.querySelectorAll !== "function") {
        return;
    }

    root.querySelectorAll(".fence-masonry .fence-content").forEach((content) => {
        if (content.querySelector(":scope > .masonry-wrapper")) {
            return;
        }

        const items = [];
        let buffer = [];

        const flushBuffer = () => {
            const hasContent = buffer.some((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    return true;
                }
                return node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== "";
            });

            if (hasContent) {
                items.push(buffer);
            }
            buffer = [];
        };

        Array.from(content.childNodes).forEach((child) => {
            if (child.nodeType === Node.TEXT_NODE) {
                if (child.textContent.trim() === "") {
                    flushBuffer();
                    return;
                }

                buffer.push(child.cloneNode(true));
                return;
            }

            if (child.nodeType !== Node.ELEMENT_NODE) {
                return;
            }

            if (child.tagName === "BR") {
                flushBuffer();
                return;
            }

            if (child.tagName === "P") {
                flushBuffer();
                const paragraphNodes = Array.from(child.childNodes)
                    .filter((node) => !isIgnorableWhitespace(node))
                    .map((node) => node.cloneNode(true));
                if (paragraphNodes.length) {
                    items.push(paragraphNodes);
                }
                return;
            }

            flushBuffer();
            items.push([child.cloneNode(true)]);
        });

        flushBuffer();

        if (!items.length) {
            return;
        }

        const wrapper = document.createElement("div");
        wrapper.className = "masonry-wrapper";

        items.forEach((nodes) => {
            const item = document.createElement("div");
            item.className = "masonry-item";

            const inner = document.createElement("div");
            inner.className = "masonry-item-inner";

            nodes.forEach((node) => inner.appendChild(node));
            item.appendChild(inner);
            wrapper.appendChild(item);
        });

        content.replaceChildren(wrapper);
    });
}

export function initFenceGrid(root = document) {
    normalizeGridFence(root);

    if (!root || typeof root.querySelectorAll !== "function") {
        return;
    }

    root.querySelectorAll(".fence-grid").forEach((el) => {
        if (el.dataset.columns) {
            el.style.setProperty("--columns", `repeat(${el.dataset.columns}, 1fr)`);
        }
        if (el.dataset.gap) {
            el.style.setProperty("--gap", el.dataset.gap);
        }
        if (el.dataset.minWidth) {
            el.style.setProperty("--min-width", el.dataset.minWidth);
        }
    });
}

export function initFenceMasonry(root = document) {
    normalizeMasonryFence(root);

    const MasonryCtor = window.Masonry;
    if (typeof MasonryCtor !== "function" || !root || typeof root.querySelectorAll !== "function") {
        return;
    }

    root.querySelectorAll(".fence-masonry .masonry-wrapper").forEach((wrapper) => {
        if (!(wrapper instanceof Element)) {
            return;
        }

        const items = Array.from(wrapper.children).filter((child) =>
            child.classList.contains("masonry-item")
        );
        if (!items.length) {
            return;
        }

        const fence = wrapper.closest(".fence-masonry");
        const gap = Math.max(parseNumber(fence?.dataset.gap, 16), 0);
        const columns = Math.max(parseNumber(fence?.dataset.columns, 3), 1);

        const layout = () => {
            const containerWidth = wrapper.clientWidth;
            if (!containerWidth) {
                return;
            }

            const columnWidth = Math.max(
                (containerWidth - gap * (columns - 1)) / columns,
                0
            );

            wrapper.style.setProperty("--masonry-gap", `${gap}px`);
            wrapper.style.setProperty("--masonry-columns", String(columns));

            items.forEach((item) => {
                item.style.width = `${columnWidth}px`;
                item.style.marginBottom = `${gap}px`;
            });

            if (!wrapper._aaMasonry) {
                wrapper._aaMasonry = new MasonryCtor(wrapper, {
                    itemSelector: ".masonry-item",
                    columnWidth,
                    gutter: gap,
                    percentPosition: false,
                    transitionDuration: 0
                });
                return;
            }

            wrapper._aaMasonry.options.columnWidth = columnWidth;
            wrapper._aaMasonry.options.gutter = gap;
            wrapper._aaMasonry.reloadItems();
            wrapper._aaMasonry.layout();
        };

        if (wrapper.dataset.masonryInitialized !== "true") {
            wrapper.dataset.masonryInitialized = "true";

            if (typeof ResizeObserver === "function") {
                const observer = new ResizeObserver(() => {
                    layout();
                });
                observer.observe(wrapper);
                wrapper._aaMasonryResizeObserver = observer;
            } else {
                window.addEventListener("resize", layout);
            }

            wrapper.querySelectorAll("img").forEach((img) => {
                if (img.complete) {
                    return;
                }

                const refresh = () => layout();
                img.addEventListener("load", refresh, {once: true});
                img.addEventListener("error", refresh, {once: true});
            });
        }

        layout();
    });
}
