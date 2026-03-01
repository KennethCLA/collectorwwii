// resources/js/app.js

import "./books-attachments-preview";

import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

import Choices from "choices.js";
import "choices.js/public/assets/styles/choices.min.css";

import "../css/app.css";
import "../css/components.css";
import "../css/home-bg.css";

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

Fancybox.bind("[data-fancybox]", {
    Toolbar: true,
    infinite: true,
    wheel: "zoom",
    Hash: false,
});

// Init Choices only where we ask for it
window.__choicesInstances = window.__choicesInstances || [];

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("select.js-select").forEach((el) => {
        if (el.dataset.enhanced) return;
        el.dataset.enhanced = "1";
        const instance = new Choices(el, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: "",
            allowHTML: false,
            position: "bottom",
        });
        window.__choicesInstances.push(instance);
    });
});
