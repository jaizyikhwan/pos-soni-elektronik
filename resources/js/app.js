import "./barcode.js";
import { initRupiahFormatter } from "./rupiah.js";

// FIRST LOAD
document.addEventListener("DOMContentLoaded", () => {
    initRupiahFormatter();
});

// LIVEWIRE SPA NAVIGATION
document.addEventListener("livewire:navigated", () => {
    initRupiahFormatter();
});
