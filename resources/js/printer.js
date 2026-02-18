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
                `❌ Item ${idx}: nama_barang, quantity, total wajib ada`,
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
        9,
    )}\n`;
    if (payload.titipan > 0) {
        t += `TITIPAN                    ${String(payload.titipan).padStart(
            9,
        )}\n`;
        t += `SISA                       ${String(payload.sisa || 0).padStart(
            9,
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
            "QZ-Tray belum ter-load. Pastikan CDN sudah accessible.",
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
                e.message,
            );
            if (attempt < maxRetries - 1) {
                await new Promise((r) => setTimeout(r, 1500));
            }
        }
    }
    throw new Error(
        "QZ-Tray connection failed setelah 3 percobaan. Pastikan aplikasi QZ-Tray sudah running.",
    );
}

// ✅ PERBAIKAN 3: Better printer validation
// some clients use fixed device names; provide explicit aliases first
const PRINTER_ALIASES = {
    thermal: [
        "PRINTER THERMAL INFORCE P58C NB", // exact Windows name
        "inforce",
        "p58",
        "p58c",
        "thermal",
        "pos",
        "xprinter",
        "zjiang",
        "58mm",
    ],
    dotmatrix: [
        "EPSON LX-310 Series",
        "epson",
        "lx",
        "fx",
        "impact",
        "dotmatrix",
    ],
};

function findPrinterByType(type, availablePrinters) {
    // first check aliases / exact names
    const aliases = PRINTER_ALIASES[type] || [];
    for (const alias of aliases) {
        const found = availablePrinters.find((p) =>
            p.toLowerCase().includes(alias.toLowerCase()),
        );
        if (found) {
            console.log(
                `✓ Found ${type} printer via alias '${alias}': ${found}`,
            );
            return found;
        }
    }

    // fall back to generic search terms if alias lookup failed
    const searchTerms = {
        thermal: [
            "thermal",
            "pos",
            "xprinter",
            "zjiang",
            "58mm",
            "inforce",
            "p58",
            "p58c",
        ],
        dotmatrix: ["dotmatrix", "epson", "lx", "fx", "impact"],
    };

    const terms = searchTerms[type] || [];

    // Cari berdasarkan nama printer
    for (const term of terms) {
        const found = availablePrinters.find((p) =>
            p.toLowerCase().includes(term.toLowerCase()),
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

        // 2. Dapatkan printer list atau cari langsung sesuai API
        const printerType = payload.printer;
        let selectedPrinter = null;

        // helper to try the old getAvailable() method if present
        if (typeof qz.printers.getAvailable === "function") {
            const availablePrinters = await qz.printers.getAvailable();
            if (!availablePrinters?.length) {
                throw new Error(
                    "❌ Tidak ada printer yang tersedia di sistem.",
                );
            }
            console.log("📋 Available printers:", availablePrinters);
            selectedPrinter = findPrinterByType(printerType, availablePrinters);
            if (!selectedPrinter) {
                selectedPrinter = availablePrinters[0];
                console.warn(
                    `⚠️ Printer ${printerType} tidak ditemukan. Menggunakan: ${selectedPrinter}`,
                );
            }
            console.log(`✓ Printer dipilih: ${selectedPrinter}`);
        } else {
            // newer QZ versions (e.g. 2.2.5) don't expose getAvailable;
            // we must search using qz.printers.find() and/or default.
            console.warn(
                "⚠️ qz.printers.getAvailable() tidak tersedia, menggunakan fallback API",
            );
            const searchTerms = {
                thermal: [
                    "thermal",
                    "pos",
                    "xprinter",
                    "zjiang",
                    "58mm",
                    "inforce",
                    "p58",
                    "p58c",
                ],
                dotmatrix: ["dotmatrix", "epson", "lx", "fx", "impact"],
            };
            const terms = searchTerms[printerType] || [];
            for (const term of terms) {
                try {
                    const found = await qz.printers.find(term);
                    if (found) {
                        selectedPrinter = found;
                        console.log(
                            `✓ Found ${printerType} printer by term '${term}': ${found}`,
                        );
                        break;
                    }
                } catch (e) {
                    // ignore, printer name doesn't match
                }
            }

            if (!selectedPrinter) {
                try {
                    const def = await qz.printers.getDefault();
                    if (def) {
                        selectedPrinter = def;
                        console.warn(
                            `⚠️ Printer ${printerType} tidak ditemukan melalui pencarian; menggunakan default: ${def}`,
                        );
                    }
                } catch (e) {
                    // no default available
                }
            }

            if (!selectedPrinter) {
                throw new Error(
                    "❌ Tidak ada printer yang tersedia di sistem (fallback failed).",
                );
            }
        }

        // verify the chosen printer actually looks like the correct type
        const validationTerms = {
            thermal: [
                "thermal",
                "pos",
                "xprinter",
                "zjiang",
                "58mm",
                "inforce",
                "p58",
                "p58c",
            ],
            dotmatrix: ["dotmatrix", "epson", "lx", "fx", "impact"],
        };
        const vals = validationTerms[printerType] || [];
        if (selectedPrinter) {
            const lowerName = selectedPrinter.toLowerCase();
            const matched = vals.some((t) =>
                lowerName.includes(t.toLowerCase()),
            );
            if (!matched) {
                throw new Error(
                    `❌ Printer yang dipilih (${selectedPrinter}) bukan jenis '${printerType}'. ` +
                        `Pastikan printer ${printerType} terpasang dan dikenali oleh sistem.`,
                );
            }
        }

        // 4. Generate format nota
        const printData =
            printerType === "thermal"
                ? generateThermalFormat(payload)
                : generateDotMatrixFormat(payload);

        if (!printData) {
            throw new Error("Format nota gagal di-generate");
        }

        console.log(`✓ Print data generated (${printData.length} bytes)`);

        // 5. ✅ PERBAIKAN: create a proper QZ Tray configuration object
        //    qz.print expects two arguments (config, data), otherwise the
        //    internal code tries to access o[0] where o is undefined.
        const cfg = qz.configs.create(selectedPrinter, {
            // you may add other options here if needed later
            encoding: "UTF-8",
        });

        // wrap the payload in an array since qz.print handles arrays of data
        const dataArray = [
            {
                type: "raw",
                format: "plain",
                data: printData,
                endOfDocument: true, // ← PENTING untuk raw printing
            },
        ];

        // 6. Set printer (ensure it exists) and execute print job
        await qz.printers.find(selectedPrinter);
        console.log("🔧 qz.print config:", cfg, "data:", dataArray);
        await qz.print(cfg, dataArray);

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
            payloads.map((payload) => printWithQZTray(payload)),
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

// Make key functions available globally in the browser environment
if (typeof window !== "undefined") {
    window.printWithQZTray = printWithQZTray;
    window.printToMultiplePrinters = printToMultiplePrinters;
    window.generateThermalFormat = generateThermalFormat;
    window.generateDotMatrixFormat = generateDotMatrixFormat;
}
