// ✅ PERBAIKAN 0: Validate payload
function validatePayload(payload) {
    const required = ["items", "printer", "pembeli", "total", "tanggal"];

    for (const field of required) {
        if (!payload[field]) {
            throw new Error(`❌ Payload missing required field: ${field}`);
        }
    }

    if (!Array.isArray(payload.items) || payload.items.length === 0) {
        throw new Error("❌ Items harus array yang tidak kosong");
    }

    if (!["thermal", "dotmatrix"].includes(payload.printer)) {
        throw new Error("❌ Printer type harus 'thermal' atau 'dotmatrix'");
    }

    // Validasi setiap item
    payload.items.forEach((item, idx) => {
        if (!item.nama_barang || !item.quantity || !item.total) {
            throw new Error(
                `❌ Item ${idx}: nama_barang, quantity, total wajib ada`
            );
        }
    });

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
        const nama = (item.nama_barang || "").substring(0, 15).padEnd(15);
        const qty = String(item.quantity || 0).padStart(3);
        const total = String(item.total || 0).padStart(8);
        t += `${nama}${qty}${total}\n`;
    });

    t += "───────────────────────────\n";
    t += `TOTAL: Rp ${String(payload.total || 0).padStart(15)}\n`;
    if (payload.titipan > 0) {
        t += `TITIPAN: Rp ${String(payload.titipan).padStart(13)}\n`;
        t += `SISA: Rp ${String(payload.sisa || 0).padStart(17)}\n`;
    }
    t += "═══════════════════════════\n";
    t += `STATUS: ${payload.status}\n`;
    t += "Terima Kasih!\n\n\n\n";

    t += esc + "i"; // Cut paper

    console.log("🖨️ [THERMAL] Generated data:", {
        length: t.length,
        preview: t.substring(0, 100),
        itemCount: payload.items.length,
    });

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
        const name = (item.nama_barang || "").padEnd(20).substring(0, 20);
        const qty = String(item.quantity || 0).padStart(3);
        const harga = String(item.harga_satuan || 0).padStart(7);
        const total = String(item.total || 0).padStart(9);
        t += `${name}${qty}${harga}${total}\n`;
    });

    t += "----------------------------------------\n";
    t += `TOTAL                      ${String(payload.total || 0).padStart(
        9
    )}\n`;
    if (payload.titipan > 0) {
        t += `TITIPAN                    ${String(payload.titipan).padStart(
            9
        )}\n`;
        t += `SISA                       ${String(payload.sisa || 0).padStart(
            9
        )}\n`;
    }
    t += "========================================\n";
    t += `STATUS: ${payload.status}\n`;
    t += "Terima Kasih!\n\n";

    console.log("🖨️ [DOTMATRIX] Generated data:", {
        length: t.length,
        preview: t.substring(0, 100),
        itemCount: payload.items.length,
    });

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
    const searchTerms = {
        thermal: ["thermal", "pos", "xprinter", "zjiang", "58mm"],
        dotmatrix: ["dotmatrix", "epson", "lx", "fx", "impact"],
    };

    const terms = searchTerms[type] || [];

    // Cari berdasarkan nama printer
    for (const term of terms) {
        const found = availablePrinters.find((p) =>
            p.toLowerCase().includes(term.toLowerCase())
        );
        if (found) {
            console.log(`✓ Found ${type} printer: ${found}`);
            return found;
        }
    }

    console.warn(`⚠️ ${type} printer not found, akan gunakan default`);
    return null;
}

// ✅ PERBAIKAN: Proper QZ Tray print configuration
export async function printWithQZTray(payload) {
    let connected = false;

    try {
        console.log("🖨️ Starting print process for:", payload.printer);

        // 1. Koneksi (hanya sekali!)
        connected = await connectQZTray();

        // 2. Dapatkan daftar printer
        const availablePrinters = await qz.printers.getAvailable();
        if (!availablePrinters?.length) {
            throw new Error("❌ Tidak ada printer yang tersedia di sistem.");
        }

        console.log("📋 Available printers:", availablePrinters);

        // 3. Cari printer sesuai tipe
        const printerType = payload.printer;
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

        // 5. ✅ PERBAIKAN: Proper QZ Tray config
        const config = [
            {
                type: "raw",
                format: "plain",
                data: printData,
                endOfDocument: true, // ← PENTING untuk raw printing
            },
        ];

        // 6. Set printer dan execute print
        await qz.printers.find(selectedPrinter);
        await qz.print(config);

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
            message: `❌ Error: ${errorMsg}`,
            error: error,
        };
    } finally {
        if (connected) {
            try {
                if (qz && qz.websocket && qz.websocket.isActive?.()) {
                    await qz.websocket.disconnect();
                    console.log("✓ QZ Tray disconnected");
                }
            } catch (e) {
                console.warn("⚠️ Disconnect warning:", e?.message);
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
