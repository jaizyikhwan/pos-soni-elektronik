// --- barcode-create.js ---
let html5QrCode = null;
let isScanning = false;
let initialized = false;

function initBarcodeScanner() {
    // Hindari init dua kali
    if (initialized) return;
    initialized = true;

    const createContainer = document.querySelector("#barcode-create-container");
    const searchContainer = document.querySelector("#barcode-search-container");
    const editContainer = document.querySelector("#barcode-edit-container");

    if (!createContainer && !searchContainer && !editContainer) {
        console.debug("barcode scanner: bukan halaman barcode, skip");
        return;
    }

    const mode = createContainer ? "create" : editContainer ? "edit" : "search";
    console.debug("barcode-scanner: mode =", mode);

    const readerEl = document.getElementById("reader");
    const startBtn = document.getElementById("start-scan");
    const stopBtn = document.getElementById("stop-scan");

    if (!readerEl || !startBtn || !stopBtn) {
        console.warn("barcode-scanner: element wajib tidak lengkap");
        return;
    }

    // Optional input
    const inputEl = document.getElementById("barcodeInput");

    // ----------------------------
    // START CAMERA
    // ----------------------------
    window.startCamera = async function () {
        if (!window.Html5Qrcode) {
            console.error("Html5Qrcode belum dimuat (CDN bermasalah?)");
            return;
        }

        if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");

        const cameras = await Html5Qrcode.getCameras();
        if (!cameras || cameras.length === 0) {
            alert("Tidak ada kamera tersedia.");
            return;
        }

        isScanning = true;

        readerEl.classList.remove("hidden");
        startBtn.classList.add("hidden");
        stopBtn.classList.remove("hidden");

        await html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess,
            (err) => console.warn("Scan error:", err)
        );
    };

    // ----------------------------
    // STOP CAMERA
    // ----------------------------
    window.stopCamera = async function () {
        if (html5QrCode && isScanning) {
            await html5QrCode.stop();
        }

        readerEl.classList.add("hidden");
        startBtn.classList.remove("hidden");
        stopBtn.classList.add("hidden");

        isScanning = false;
    };

    // ----------------------------
    // ON SCAN SUCCESS
    // ----------------------------
    function onScanSuccess(decodedText) {
        if (!isScanning) return;

        console.debug("barcode-scanner: scanned =", decodedText);

        // CREATE & EDIT → isi input
        if ((mode === "create" || mode === "edit") && inputEl) {
            inputEl.value = decodedText;
            inputEl.dispatchEvent(new Event("input", { bubbles: true }));
        }

        // SEARCH → kirim ke Livewire
        if (mode === "search") {
            const componentEl = document.querySelector("[wire\\:id]");
            if (!componentEl) {
                console.error("Livewire component tidak ditemukan");
                stopCamera();
                return;
            }

            const component = Livewire.find(
                componentEl.getAttribute("wire:id")
            );

            component.set("barcode", decodedText);
            component.call("search");
        }

        stopCamera();
    }

    // ----------------------------
    // EVENTS
    // ----------------------------
    startBtn.addEventListener("click", window.startCamera);
    stopBtn.addEventListener("click", window.stopCamera);
}

// First load
document.addEventListener("DOMContentLoaded", initBarcodeScanner);

// SPA navigation (Livewire)
document.addEventListener("livewire:navigated", () => {
    initialized = false;
    initBarcodeScanner();
});
