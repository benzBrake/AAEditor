import DetailsAnimator from "./utils/DetailsAnimator";
import {initFenceTabs} from "./utils/tabs";
import {initFenceGrid, initFenceMasonry} from "./utils/fenceGallery";
import {enhanceCodeBlocks} from "./utils/codeBlocks";

function initFenceDetails(root = document) {
    root.querySelectorAll(".fence-details").forEach((el) => {
        if (el.dataset.detailsInitialized === "true") {
            return;
        }
        el.dataset.detailsInitialized = "true";
        new DetailsAnimator(el);
    });
}

function initFrontFeatures(root = document) {
    initFenceDetails(root);
    initFenceTabs(root);
    initFenceGrid(root);
    initFenceMasonry(root);
    enhanceCodeBlocks(root, window.AAEditorCodeBlocksConfig || {});
}

document.addEventListener("DOMContentLoaded", () => {
    initFrontFeatures(document);
});

document.addEventListener("pjax:complete", () => {
    initFrontFeatures(document);
});
