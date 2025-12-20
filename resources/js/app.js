import "./bootstrap";

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

import "../css/app.css";
import "../css/components.css";
import "../css/home-bg.css";
