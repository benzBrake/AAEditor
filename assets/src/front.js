import DetailsAnimator from "./utils/DetailsAnimator";
import {initFenceTabs} from "./utils/tabs";

document.addEventListener("DOMContentLoaded", _ => {
    document.querySelectorAll('.fence-details').forEach((el) => {
        new DetailsAnimator(el);
    });
    initFenceTabs(document);
});
