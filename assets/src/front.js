import DetailsAnimator from "./utils/DetailsAnimator";

document.addEventListener("DOMContentLoaded", _ => {
    document.querySelectorAll('.fence-details').forEach((el) => {
        new DetailsAnimator(el);
    });
});