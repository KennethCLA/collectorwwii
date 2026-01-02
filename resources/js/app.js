import "./bootstrap";
import "./admin/books-attachments-preview";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

import "../css/app.css";
import "../css/components.css";
import "../css/home-bg.css";

document.addEventListener("DOMContentLoaded", () => {
    Fancybox.bind("[data-fancybox='gallery']", {
        Toolbar: true,
        infinite: true,
        wheel: "zoom",
    });
});
