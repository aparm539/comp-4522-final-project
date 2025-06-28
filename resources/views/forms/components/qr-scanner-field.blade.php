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
                    console.log('BarcodeDetector is available');
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
                            try {
                                // iOS Safari requires specific constraints
                                const constraints = {
                                    video: {
                                        facingMode: this.options.preferredCamera || 'environment',
                                        width: { ideal: 1280 },
                                        height: { ideal: 720 }
                                    }
                                };
                                
                                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                                this.video.srcObject = stream;
                                this.video.setAttribute('playsinline', 'true');
                                this.video.muted = true;
                                await this.video.play();
                                this.isScanning = true;
                                this.scan();
                            } catch (error) {
                                // Try with less restrictive constraints on iOS
                                try {
                                    const fallbackConstraints = {
                                        video: true
                                    };
                                    const stream = await navigator.mediaDevices.getUserMedia(fallbackConstraints);
                                    this.video.srcObject = stream;
                                    this.video.setAttribute('playsinline', 'true');
                                    this.video.muted = true;
                                    await this.video.play();
                                    this.isScanning = true;
                                    this.scan();
                                } catch (fallbackError) {
                                    throw new Error(`Camera access failed: ${fallbackError.name || fallbackError.message || 'Unknown error'}`);
                                }
                            }
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
                                // Check if mediaDevices is available
                                if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
                                    return false;
                                }
                                
                                const devices = await navigator.mediaDevices.enumerateDevices();
                                const hasVideoInput = devices.some(device => device.kind === 'videoinput');
                                
                                // On iOS, sometimes enumerateDevices returns empty labels
                                // Try to request camera access to get proper device info
                                if (!hasVideoInput || devices.every(d => d.kind === 'videoinput' && !d.label)) {
                                    try {
                                        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                                        // If we got a stream, camera exists
                                        stream.getTracks().forEach(track => track.stop());
                                        return true;
                                    } catch {
                                        return false;
                                    }
                                }
                                
                                return hasVideoInput;
                            } catch {
                                // Final fallback: assume camera exists if getUserMedia is available
                                return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
                            }
                        }
                    };
                    return;
                }
                console.log('BarcodeDetector is not available');
                
                // Fallback: Load QrScanner from CDN but handle worker path
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/qr-scanner@1.4.2/qr-scanner.umd.min.js';
                    script.onload = () => resolve();
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
                console.log('QrScanner loaded from CDN');
            },
            async init() {
                // Check HTTPS requirement for iOS
                if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                    this.error = 'Camera access requires HTTPS. Please use a secure connection.';
                    return;
                }
                
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
                console.log('Starting scanner');
                if (!this.scanner) {
                    console.log('Scanner is not initialized');
                    await this.init();
                }
                
                if (!this.hasCamera) {
                    this.error = 'No camera available on this device';
                    return;
                }
                
                try {
                    
                    const video = this.$refs.video;
                    console.log('Video element:', video);
                    video.setAttribute('autoplay', '');
                    video.setAttribute('muted', '');
                    video.setAttribute('playsinline', '');
                    video.style.display = 'block';
                    console.log('Starting scanner');
                    await this.scanner.start();
                    console.log('Scanner started');
                    this.isScanning = true;
                    this.error = null;
                } catch (err) {
                    console.error('Error starting scanner:', err);
                    
                    // Provide specific error messages for common iOS issues
                    let errorMessage = 'Failed to start camera';
                    
                    if (err.name === 'NotAllowedError') {
                        errorMessage = 'Camera permission denied. Please allow camera access in your browser settings.';
                    } else if (err.name === 'NotFoundError') {
                        errorMessage = 'No camera found on this device.';
                    } else if (err.name === 'NotSupportedError') {
                        errorMessage = 'Camera not supported in this browser.';
                    } else if (err.name === 'NotReadableError') {
                        errorMessage = 'Camera is already in use by another application.';
                    } else if (err.name === 'OverconstrainedError') {
                        errorMessage = 'Camera constraints not supported. Try refreshing the page.';
                    } else if (err.message) {
                        errorMessage = err.message;
                    }
                    
                    this.error = errorMessage;
                    this.isScanning = false;
                    
                    // Hide video on error
                    video.style.display = 'none';
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
            autoPlay="true"
            playsInline="true"
            muted="true"
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