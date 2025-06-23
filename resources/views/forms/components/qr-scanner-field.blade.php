@php
    /** @var \App\Filament\Forms\Components\QrScannerField $field */
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            scanner: null,
            isScanning: false,
            hasCamera: false,
            error: null,
            async ensureQrScanner() {
                if (window.QrScanner) return;
                
                // First check if we can use the native BarcodeDetector
                if ('BarcodeDetector' in window) {
                    // Create a simple QrScanner-like interface using BarcodeDetector
                    window.QrScanner = class {
                        constructor(video, onResult, options = {}) {
                            this.video = video;
                            this.onResult = onResult;
                            this.options = options;
                            this.isScanning = false;
                            this.detector = new BarcodeDetector({ formats: ['qr_code'] });
                        }
                        
                        async start() {
                            const stream = await navigator.mediaDevices.getUserMedia({
                                video: { facingMode: this.options.preferredCamera || 'environment' }
                            });
                            this.video.srcObject = stream;
                            await this.video.play();
                            this.isScanning = true;
                            this.scan();
                        }
                        
                        async stop() {
                            this.isScanning = false;
                            if (this.video.srcObject) {
                                this.video.srcObject.getTracks().forEach(track => track.stop());
                                this.video.srcObject = null;
                            }
                        }
                        
                        async scan() {
                            if (!this.isScanning) return;
                            
                            try {
                                const barcodes = await this.detector.detect(this.video);
                                if (barcodes.length > 0) {
                                    const result = this.options.returnDetailedScanResult 
                                        ? { data: barcodes[0].rawValue }
                                        : barcodes[0].rawValue;
                                    this.onResult(result);
                                    return;
                                }
                            } catch (err) {
                                console.log('Scan error:', err);
                            }
                            
                            // Continue scanning
                            if (this.isScanning) {
                                setTimeout(() => this.scan(), 100);
                            }
                        }
                        
                        static async hasCamera() {
                            try {
                                const devices = await navigator.mediaDevices.enumerateDevices();
                                return devices.some(device => device.kind === 'videoinput');
                            } catch {
                                return false;
                            }
                        }
                    };
                    return;
                }
                
                // Fallback: Load QrScanner from CDN but handle worker path
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/qr-scanner@1.4.2/qr-scanner.umd.min.js';
                    script.onload = () => resolve();
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            },
            async init() {
                await this.ensureQrScanner();
                
                // Check if device has camera
                this.hasCamera = await window.QrScanner.hasCamera();
                
                if (!this.hasCamera) {
                    this.error = 'No camera found on this device';
                    return;
                }
                
                // Create QrScanner instance with proper options
                const video = this.$refs.video;
                this.scanner = new window.QrScanner(
                    video,
                    result => {
                        console.log('decoded qr code:', result);
                        // Set the scanned value to the form field
                        $wire.set('{{ $field->getStatePath() }}', result.data || result);
                        this.stopScanner();
                    },
                    {
                        returnDetailedScanResult: true,
                        preferredCamera: 'environment', // Use back camera on mobile
                        highlightScanRegion: true,
                        highlightCodeOutline: true,
                        maxScansPerSecond: 5
                    }
                );
            },
            async startScanner() {
                if (!this.scanner) {
                    await this.init();
                }
                
                if (!this.hasCamera) {
                    return;
                }
                
                try {
                    const video = this.$refs.video;
                    video.style.display = 'block';
                    await this.scanner.start();
                    this.isScanning = true;
                    this.error = null;
                } catch (err) {
                    console.error('Error starting scanner:', err);
                    this.error = 'Failed to start camera: ' + err.message;
                    this.isScanning = false;
                }
            },
            async stopScanner() {
                if (this.scanner && this.isScanning) {
                    try {
                        await this.scanner.stop();
                    } catch (err) {
                        console.error('Error stopping scanner:', err);
                    }
                }
                this.isScanning = false;
                const video = this.$refs.video;
                video.style.display = 'none';
            },
            async toggleScanner() {
                if (this.isScanning) {
                    await this.stopScanner();
                } else {
                    await this.startScanner();
                }
            }
        }"
        x-init="init()"
        class="space-y-2"
    >
        <!-- Input field for barcode/QR code -->
        <div class="space-y-1">
            <input 
                type="text" 
                wire:model.live="{{ $field->getStatePath() }}"
                placeholder="Scan QR code or enter barcode manually..."
                class="filament-input block w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-70 disabled:cursor-not-allowed"
                x-bind:disabled="isScanning"
            />
            <p class="text-xs text-gray-500">
                Use the scanner below or type the barcode manually
            </p>
        </div>

        <!-- Video element for webcam stream -->
        <video 
            x-ref="video" 
            class="w-full max-w-md mx-auto rounded-md shadow bg-black" 
            style="display: none;"
            playsinline
        ></video>

        <!-- Error message -->
        <div x-show="error" x-text="error" class="text-red-600 text-sm"></div>

        <!-- Scanner controls -->
        <div class="flex gap-2">
            <button 
                type="button" 
                class="filament-button filament-button--size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700" 
                @click="toggleScanner"
                :disabled="!hasCamera"
            >
                <span x-text="isScanning ? 'Stop Scanning' : 'Start QR Scanner'"></span>
            </button>
            
            <span 
                x-show="!hasCamera && !error" 
                class="text-gray-500 text-sm py-2"
            >
                No camera available
            </span>
        </div>
    </div>
</x-dynamic-component> 