// =============================
// barcode.js FINAL VERY STABLE
// =============================

let html5QrCode = null;
let isScanning = false;
let initialized = false;

// =========================
// RESET SCANNER
// =========================
async function resetScanner() {
    if (html5QrCode) {
        try {
            await html5QrCode.stop();
        } catch (e) {}

        try {
            await html5QrCode.clear();
        } catch (e) {}
    }

    html5QrCode = null;
    isScanning = false;
    initialized = false;
}

// =========================
// INIT SCANNER
// =========================
function initBarcodeScanner() {
    if (initialized) return;

    const createContainer = document.querySelector("#barcode-create-container");
    const editContainer = document.querySelector("#barcode-edit-container");
    const searchContainer = document.querySelector("#barcode-search-container");

    if (!createContainer && !editContainer && !searchContainer) {
        return;
    }

    initialized = true;

    const mode = createContainer ? "create" : editContainer ? "edit" : "search";

    const readerEl = document.getElementById("reader");
    const startBtn = document.getElementById("start-scan");
    const stopBtn = document.getElementById("stop-scan");

    if (!readerEl || !startBtn || !stopBtn) {
        return;
    }

    const inputEl = document.getElementById("barcodeInput");

    // =========================
    // START CAMERA
    // =========================
    window.startCamera = async function () {
        if (isScanning) return;

        if (!window.Html5Qrcode) {
            alert("Library camera tidak tersedia");
            return;
        }

        try {
            await resetScanner();

            const reader = document.getElementById("reader");

            if (!reader) {
                alert("Reader tidak ditemukan");
                return;
            }

            html5QrCode = new Html5Qrcode("reader");

            await new Promise((r) => setTimeout(r, 300));

            const cameras = await Html5Qrcode.getCameras();

            if (!cameras || cameras.length === 0) {
                alert("Tidak ada kamera");
                return;
            }

            isScanning = true;

            readerEl.classList.remove("hidden");
            startBtn.classList.add("hidden");
            stopBtn.classList.remove("hidden");

            await html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: 250,
                    aspectRatio: 1.0,
                    disableFlip: true,
                },
                onScanSuccess,
                () => {},
            );
        } catch (error) {
            isScanning = false;

            readerEl.classList.add("hidden");
            startBtn.classList.remove("hidden");
            stopBtn.classList.add("hidden");

            const name = error?.name || "";
            const msg = error?.message || "";

            if (name === "NotAllowedError") {
                alert("Izin kamera ditolak");
            } else if (name === "NotReadableError") {
                alert("Kamera sedang dipakai aplikasi lain");
            } else if (name === "NotFoundError") {
                alert("Kamera tidak ditemukan");
            } else if (name === "SecurityError") {
                alert("Harus HTTPS");
            } else {
                alert("Camera error: " + msg);
            }
        }
    };

    // =========================
    // STOP CAMERA
    // =========================
    window.stopCamera = async function () {
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
            } catch (e) {}

            try {
                await html5QrCode.clear();
            } catch (e) {}

            html5QrCode = null;
        }

        readerEl.classList.add("hidden");
        startBtn.classList.remove("hidden");
        stopBtn.classList.add("hidden");

        isScanning = false;
    };

    // =========================
    // SUCCESS SCAN
    // =========================
    function onScanSuccess(decodedText) {
        if (!isScanning) return;

        if ((mode === "create" || mode === "edit") && inputEl) {
            inputEl.value = decodedText;

            inputEl.dispatchEvent(new Event("input", { bubbles: true }));
        }

        if (mode === "search") {
            const componentEl = document.querySelector("[wire\\:id]");

            if (!componentEl) {
                stopCamera();
                return;
            }

            const component = Livewire.find(
                componentEl.getAttribute("wire:id"),
            );

            component.set("barcode", decodedText);

            component.call("search");
        }

        stopCamera();
    }

    // =========================
    // EVENTS
    // =========================
    startBtn.onclick = window.startCamera;
    stopBtn.onclick = window.stopCamera;
}

// =========================
// FIRST LOAD
// =========================
document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        initBarcodeScanner();
    }, 300);
});

// =========================
// LIVEWIRE NAVIGATED
// =========================
document.addEventListener("livewire:navigated", async () => {
    await resetScanner();

    setTimeout(() => {
        initBarcodeScanner();
    }, 500);
});

// =========================
// RESET FROM LIVEWIRE EVENT
// =========================
window.addEventListener("reset-camera", async () => {
    await resetScanner();
});
