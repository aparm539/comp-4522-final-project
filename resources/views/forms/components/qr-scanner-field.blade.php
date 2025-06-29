@php
    /** @var \App\Filament\Forms\Components\QrScannerField $field */
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            isScanning: false,
            error: null,
            hasCamera: false,
            stream: null,
            videoTrack: null,
            
            async init() {
                try {
                    // Check if camera is available
                    this.hasCamera = 'mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices;
                    console.log('Camera API available:', this.hasCamera);
                } catch (err) {
                    console.warn('Could not check for cameras:', err);
                    this.error = 'Could not check for camera availability';
                }
            },
            
            async startCamera() {
                try {
                    const video = this.$refs.video;
                    
                    // Get camera stream with preferred environment (back) camera
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment' },
                        audio: false
                    });
                    
                    // Attach stream to video element
                    video.srcObject = this.stream;
                    video.style.display = 'block';
                    
                    // Store video track for stopping later
                    this.videoTrack = this.stream.getVideoTracks()[0];
                    
                    console.log('Camera started successfully');
                    this.isScanning = true;
                    this.error = null;
                    
                    // Handle manual QR code recognition if needed
                    // This is a placeholder - you would need to implement QR detection
                    // or instruct users to position the code and manually submit
                } catch (err) {
                    console.error('Error accessing camera:', err);
                    this.error = 'Error accessing camera: ' + err.message;
                }
            },
            
            stopCamera() {
                if (this.stream) {
                    // Stop all tracks in the stream
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                    this.videoTrack = null;
                }
                
                // Hide video element
                const video = this.$refs.video;
                video.srcObject = null;
                video.style.display = 'none';
                
                this.isScanning = false;
            },
            
            toggleCamera() {
                if (this.isScanning) {
                    this.stopCamera();
                } else {
                    this.startCamera();
                }
            },
            
            // Manually capture value from camera view
            captureValue() {
                // Get value from input field
                const value = document.getElementById('{{ $field->getId() }}_manual').value;
                if (value) {
                    $wire.set('{{ $field->getStatePath() }}', value);
                    this.stopCamera();
                }
            }
        }"
        x-init="init()"
        x-on:beforeunload="stopCamera()"
        class="space-y-2"
    >
        <!-- Input field for barcode/QR code -->
        <div class="space-y-1">
            <input 
                type="text" 
                id="{{ $field->getId() }}"
                wire:model.live="{{ $field->getStatePath() }}"
                placeholder="Scan QR code or enter barcode manually..."
                class="filament-input block w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-70 disabled:cursor-not-allowed"
            />
            <p class="text-xs text-gray-500">
                Use the camera below or type the barcode manually
            </p>
        </div>

        <!-- Video element for webcam stream -->
        <div class="relative">
            <video 
                x-ref="video" 
                class="w-full max-w-md mx-auto rounded-md shadow bg-black" 
                style="display: none;"
                autoplay
                playsinline
                muted
            ></video>
            
            <!-- Manual entry when camera is active -->
            <div x-show="isScanning" class="mt-2 space-y-2">
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        id="{{ $field->getId() }}_manual"
                        placeholder="Enter code manually from camera view..."
                        class="filament-input block w-full border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                    <button 
                        type="button"
                        @click="captureValue()"
                        class="filament-button filament-button--size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                    >
                        Capture
                    </button>
                </div>
                <p class="text-xs text-gray-500">
                    Point camera at QR code and enter the value or click capture
                </p>
            </div>
        </div>

        <!-- Error message -->
        <div x-show="error" x-text="error" class="text-red-600 text-sm"></div>

        <!-- Scanner controls -->
        <div class="flex gap-2 items-center">
            <button 
                type="button" 
                class="filament-button filament-button--size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700" 
                @click="toggleCamera"
                :disabled="!hasCamera"
            >
                <span x-text="isScanning ? 'Stop Camera' : 'Start Camera'"></span>
            </button>
            
            <span 
                x-show="!hasCamera && !error" 
                class="text-gray-500 text-sm"
            >
                No camera available
            </span>
        </div>
    </div>
</x-dynamic-component> 