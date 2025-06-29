@php
    /** @var \App\Filament\Forms\Components\QrScannerField $field */
@endphp

<!-- Add QR Scanner scripts from CDN -->
<script src="https://unpkg.com/qr-scanner@1.4.2/qr-scanner-worker.min.js"></script>
<script src="https://unpkg.com/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            scanner: null,
            isScanning: false,
            error: null,
            async init() {
                try {                    
                    const video = this.$refs.video;
                    // Create QrScanner instance with proper options
                    this.scanner = new QrScanner(
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
                } catch (err) {
                    console.error('Error initializing scanner:', err);
                    this.error = 'Failed to initialize camera: ' + err.message;
                }
            },
            async startScanner() {
                try {
                    const video = this.$refs.video;
                    video.style.display = 'block';
                    console.log('Starting scanner');
                    await this.scanner.start();
                    console.log('Scanner started');
                    this.isScanning = true;
                    this.error = null;
                } catch (err) {
                    console.error('Error starting scanner:', err);
                    this.error = 'Error accessing camera: ' + err.message;
                    this.isScanning = false;
                    
                    // Hide video on error
                    const video = this.$refs.video;
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
        x-on:beforeunload="stopScanner()"
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