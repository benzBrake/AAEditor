function splitClasses(value = "") {
    return String(value)
        .split(/\s+/)
        .map((item) => item.trim())
        .filter(Boolean);
}

function getDirectChildByClass(element, className) {
    if (!(element instanceof Element)) {
        return null;
    }
    return Array.from(element.children).find((child) =>
        child.classList.contains(className)
    ) || null;
}

function resolveTabs(root = document) {
    const tabs = [];
    if (root instanceof Element && root.classList.contains("fence-tabs")) {
        tabs.push(root);
    }
    if (typeof root.querySelectorAll === "function") {
        tabs.push(...root.querySelectorAll(".fence-tabs"));
    }
    return Array.from(new Set(tabs));
}

function applyStateClasses(element, activeClasses = [], inactiveClasses = [], isActive = false) {
    if (!(element instanceof Element)) {
        return;
    }
    activeClasses.forEach((className) => {
        element.classList.toggle(className, isActive);
    });
    inactiveClasses.forEach((className) => {
        element.classList.toggle(className, !isActive);
    });
}

function initTabsInstance(tabs) {
    if (!(tabs instanceof Element)) {
        return;
    }

    const nav = getDirectChildByClass(tabs, "fence-tabs-nav");
    const panelsWrapper = getDirectChildByClass(tabs, "fence-tabs-panels");
    if (!nav || !panelsWrapper) {
        return;
    }

    const buttons = Array.from(nav.children).filter((child) =>
        child.classList.contains("fence-tabs-tab")
    );
    const panels = Array.from(panelsWrapper.children).filter((child) =>
        child.classList.contains("fence-tabs-panel")
    );
    if (!buttons.length || buttons.length !== panels.length) {
        return;
    }

    const activeClasses = splitClasses(tabs.dataset.tabActiveClasses);
    const inactiveClasses = splitClasses(tabs.dataset.tabInactiveClasses);
    const panelActiveClasses = ["fence-tabs-panel-active", "tab-panel-active"];
    const panelInactiveClasses = ["fence-tabs-panel-inactive", "tab-panel-inactive"];
    const reduceMotion = window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches;

    const activate = (index, focusButton = false, animatePanel = true) => {
        const previousIndex = Number.parseInt(tabs.dataset.tabsActiveIndex || "", 10);
        const shouldAnimate = animatePanel && !reduceMotion && previousIndex !== index && tabs.dataset.tabsReady === "true";

        buttons.forEach((button, buttonIndex) => {
            const isActive = buttonIndex === index;
            button.setAttribute("aria-selected", isActive ? "true" : "false");
            button.setAttribute("tabindex", isActive ? "0" : "-1");
            applyStateClasses(button, activeClasses, inactiveClasses, isActive);
            if (focusButton && isActive) {
                button.focus();
            }
        });

        panels.forEach((panel, panelIndex) => {
            const isActive = panelIndex === index;
            panel.classList.remove("fence-tabs-panel-no-animation");
            panel.hidden = !isActive;
            panel.setAttribute("aria-hidden", isActive ? "false" : "true");
            applyStateClasses(panel, panelActiveClasses, panelInactiveClasses, isActive);

            if (isActive && !shouldAnimate) {
                panel.classList.add("fence-tabs-panel-no-animation");
            }
        });

        tabs.dataset.tabsActiveIndex = String(index);
    };

    buttons.forEach((button, index) => {
        if (button.dataset.tabsBound === "true") {
            return;
        }
        button.dataset.tabsBound = "true";
        button.addEventListener("click", () => {
            activate(index, false);
        });
    });

    let activeIndex = Number.parseInt(tabs.dataset.tabsActiveIndex || "", 10);
    if (Number.isNaN(activeIndex) || activeIndex < 0 || activeIndex >= buttons.length) {
        activeIndex = buttons.findIndex(
            (button) => button.getAttribute("aria-selected") === "true"
        );
    }
    if (activeIndex < 0) {
        activeIndex = 0;
    }

    activate(activeIndex, false, false);
    tabs.dataset.tabsReady = "true";
    tabs.dataset.tabsInitialized = "true";
}

export function initFenceTabs(root = document) {
    resolveTabs(root).forEach((tabs) => {
        initTabsInstance(tabs);
    });
}
