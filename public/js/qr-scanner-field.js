// Expose global factory function for Alpine x-data.
// Assumes qr-scanner.umd.min.js is loaded first and sets window.QrScanner.

if (typeof QrScanner === "undefined") {
    console.error(
        "QrScanner library not found. Ensure qr-scanner.umd.min.js is loaded before qr-scanner-field.js"
    );
} else {
    QrScanner.WORKER_PATH = "/js/qr-scanner-worker.min.js";

    window.qrScannerField = function ({ state }) {
        return {
            scanner: null,
            isScanning: false,
            async startScanner() {
                const video = this.$refs.video;
                video.style.display = "block";

                if (!this.scanner) {
                    this.scanner = new QrScanner(
                        video,
                        (result) => {
                            state = result?.data ?? result;
                            this.stopScanner();
                        },
                        { returnDetailedScanResult: true }
                    );
                }

                await this.scanner.start();
                this.isScanning = true;
            },
            async stopScanner() {
                if (this.scanner) {
                    await this.scanner.stop();
                }
                this.isScanning = false;
                this.$refs.video.style.display = "none";
            },
            toggleScanner() {
                this.isScanning ? this.stopScanner() : this.startScanner();
            },
        };
    };
}
