export function initRupiahFormatter() {
    document.querySelectorAll(".rupiah-input").forEach((input) => {
        // Format hanya ketika user selesai (blur)
        input.addEventListener("blur", (e) => {
            let value = e.target.value.replace(/[^\d]/g, "");

            if (!value) {
                e.target.value = "";
                return;
            }

            let formatted = new Intl.NumberFormat("id-ID").format(value);
            e.target.value = "Rp " + formatted;
        });

        // Hapus Rp dan titik saat fokus supaya bisa diketik normal
        input.addEventListener("focus", (e) => {
            e.target.value = e.target.value.replace(/[^\d]/g, "");
        });
    });
}
