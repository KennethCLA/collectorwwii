// resources/js/app.js

import "./bootstrap";
import "./books-attachments-preview";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import "../css/app.css";
import "../css/components.css";
import "../css/home-bg.css";

Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

Fancybox.bind("[data-fancybox='gallery']", {
    Toolbar: true,
    infinite: true,
    wheel: "zoom",

    // 🔥 DIT IS DE FIX
    Hash: false,
});
