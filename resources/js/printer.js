// ✅ PERBAIKAN 0: Validate payload
function validatePayload(payload) {
    if (!payload.items || !Array.isArray(payload.items)) {
        throw new Error("Invalid payload: items harus array");
    }

    if (payload.items.length === 0) {
        throw new Error("Tidak ada barang untuk dicetak");
    }

    if (
        !payload.printer ||
        !["thermal", "dotmatrix"].includes(payload.printer)
    ) {
        throw new Error("Invalid printer type");
    }

    return true;
}
// Generate format untuk Thermal (POS-58)
function generateThermalFormat(payload) {
    validatePayload(payload);

    const esc = "\x1B";
    let t = "";
    t += esc + "@"; // Init
    t += esc + "a" + String.fromCharCode(1); // Center
    t += "═══════════════════════════\n";
    t += "     SONI ELEKTRONIK\n";
    t += "  Jl. Demuk No. 123\n";
    t += "     Tulungagung\n";
    t += "═══════════════════════════\n";

    t += esc + "a" + String.fromCharCode(0); // Left align
    t += `Tanggal: ${payload.tanggal}\n`;
    t += `Pembeli: ${payload.pembeli}\n`;
    t += `HP: ${payload.no_hp}\n`;
    t += `Alamat: ${payload.alamat}\n`;
    t += "───────────────────────────\n";
    t += "BARANG              QTY TOTAL\n";
    t += "───────────────────────────\n";

    payload.items.forEach((item) => {
        const nama = (item.nama_barang || item.nama || "")
            .substring(0, 15)
            .padEnd(15);
        const qty = String(item.quantity).padStart(3);
        const total = String(item.total).padStart(8);
        t += `${nama}${qty}${total}\n`;
    });

    t += "───────────────────────────\n";
    t += `TOTAL: Rp ${String(payload.total).padStart(20)}\n`;
    t += `TITIPAN: Rp ${String(payload.titipan).padStart(16)}\n`;
    t += `SISA: Rp ${String(payload.sisa).padStart(20)}\n`;
    t += "═══════════════════════════\n";
    t += `STATUS: ${payload.status}\n`;
    t += "Terima Kasih!\n\n\n\n";

    t += esc + "i"; // Cut paper
    return t;
}

// Generate format untuk Dotmatrix (EPSON)
function generateDotMatrixFormat(payload) {
    validatePayload(payload);

    let t = "";
    t += "        SONI ELEKTRONIK\n";
    t += "      Jl. Demuk No. 123\n";
    t += "       Tulungagung\n";
    t += "========================================\n";
    t += `Tanggal : ${payload.tanggal}\n`;
    t += `Pembeli : ${payload.pembeli}\n`;
    t += `HP      : ${payload.no_hp}\n`;
    t += `Alamat  : ${payload.alamat}\n`;
    t += "----------------------------------------\n";
    t += "Barang               Qty   Harga   Total\n";
    t += "----------------------------------------\n";

    payload.items.forEach((item) => {
        const name = item.nama_barang.padEnd(20).substring(0, 20);
        const qty = String(item.quantity).padStart(3);
        const harga = String(item.harga_satuan).padStart(7);
        const total = String(item.total).padStart(9);
        t += `${name}${qty}${harga}${total}\n`;
    });

    t += "----------------------------------------\n";
    t += `TOTAL                      ${String(payload.total).padStart(9)}\n`;
    t += `TITIPAN                    ${String(payload.titipan).padStart(9)}\n`;
    t += `SISA                       ${String(payload.sisa).padStart(9)}\n`;
    t += "========================================\n";
    t += `STATUS: ${payload.status}\n`;
    t += "Terima Kasih!\n\n";

    return t;
}

// ✅ PERBAIKAN 1: Validasi QZ-Tray availability
async function validateQZTray() {
    if (typeof qz === "undefined") {
        throw new Error(
            "QZ-Tray belum ter-load. Pastikan CDN sudah accessible."
        );
    }
    return true;
}

// ✅ PERBAIKAN 2: Improved connection handling
async function connectQZTray(maxRetries = 3) {
    await validateQZTray();

    for (let attempt = 0; attempt < maxRetries; attempt++) {
        try {
            if (!qz.websocket.isActive()) {
                await qz.websocket.connect();
            }
            console.log("✓ QZ Tray connected");
            return true;
        } catch (e) {
            console.warn(
                `Connection attempt ${attempt + 1}/${maxRetries} failed:`,
                e.message
            );
            if (attempt < maxRetries - 1) {
                await new Promise((r) => setTimeout(r, 1500));
            }
        }
    }
    throw new Error(
        "QZ-Tray connection failed setelah 3 percobaan. Pastikan aplikasi QZ-Tray sudah running."
    );
}

// ✅ PERBAIKAN 3: Better printer validation
function findPrinterByType(type, availablePrinters) {
    const printerMapping = {
        thermal: ["POS", "58", "THERMAL", "INFORCE"],
        dotmatrix: ["EPSON", "DOT", "MATRIX", "L5290"],
    };

    const keywords = printerMapping[type] || [];
    const found = availablePrinters.find((p) =>
        keywords.some((kw) => p.toUpperCase().includes(kw))
    );

    if (!found) {
        console.warn(
            `Printer ${type} tidak ditemukan. Printer tersedia:`,
            availablePrinters
        );
    }

    return found;
}

// ✅ PERBAIKAN 4: Enhanced export function
export async function printWithQZTray(payload) {
    let connected = false;

    try {
        console.log("🖨️ Starting print process for:", payload.printer);
        connected = await connectQZTray();

        // 1. Koneksi dengan retry
        connected = await connectQZTray();

        // 2. Dapatkan daftar printer
        const availablePrinters = await qz.printers.getAvailable();
        if (!availablePrinters?.length) {
            throw new Error("❌ Tidak ada printer yang tersedia di sistem.");
        }

        console.log("📋 Available printers:", availablePrinters);

        // 3. Cari printer sesuai tipe
        const printerType = payload.printer; // 'thermal' atau 'dotmatrix'
        let selectedPrinter = findPrinterByType(printerType, availablePrinters);

        if (!selectedPrinter) {
            selectedPrinter = availablePrinters[0];
            console.warn(
                `⚠️ Printer ${printerType} tidak ditemukan. Menggunakan: ${selectedPrinter}`
            );
        }

        console.log(`✓ Printer dipilih: ${selectedPrinter}`);

        // 4. Generate format nota
        const printData =
            printerType === "thermal"
                ? generateThermalFormat(payload)
                : generateDotMatrixFormat(payload);

        if (!printData) {
            throw new Error("Format nota gagal di-generate");
        }

        console.log(`✓ Print data generated (${printData.length} bytes)`);

        // 5. Konfigurasi print
        const config = [
            {
                type: "raw",
                format: "plain",
                data: printData,
            },
        ];

        // 6. Execute print
        await qz.print({ printer: selectedPrinter }, config);

        console.log("✓ Print job sent successfully");
        return {
            success: true,
            message: "✓ Nota berhasil dicetak ke printer " + selectedPrinter,
            printer: selectedPrinter,
        };
    } catch (error) {
        const errorMsg = error?.message || error?.toString() || "Unknown error";
        console.error("❌ Print Error:", error);
        return {
            success: false,
            message: `❌ Error: ${error.message}`,
            error: error,
        };
    } finally {
        if (connected) {
            try {
                if (qz.websocket.isActive()) {
                    await qz.websocket.disconnect();
                }
            } catch (e) {
                console.warn("Disconnect warning:", e.message);
            }
        }
    }
}

// Untuk print ke multiple printers sekaligus
export async function printToMultiplePrinters(payloads) {
    try {
        const results = await Promise.all(
            payloads.map((payload) => printWithQZTray(payload))
        );
        return {
            success: results.every((r) => r.success),
            results: results,
        };
    } catch (error) {
        return {
            success: false,
            message: `Multiple print error: ${error.message}`,
            error: error,
        };
    }
}
