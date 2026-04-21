import DetailsAnimator from "./utils/DetailsAnimator";
import {initFenceTabs} from "./utils/tabs";
import {initFenceGrid, initFenceMasonry} from "./utils/fenceGallery";

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
}

document.addEventListener("DOMContentLoaded", () => {
    initFrontFeatures(document);
});
