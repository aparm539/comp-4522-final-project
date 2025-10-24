<div>


        @php

            $history = $this->history();
            $processedHistory = [];
            $previousLocationId = null;

            // Determine location changes
            foreach ($history as $item) {
                $locationChanged = ! is_null($previousLocationId) && $item->storage_location_id !== $previousLocationId;

                $processedHistory[] = [
                    'item' => $item,
                    'locationChanged' => $locationChanged,
                    'previousLocationId' => $previousLocationId,
                ];

                $previousLocationId = $item->storage_location_id;
            }

            // Reverse the processed history for display
            $processedHistory = array_reverse($processedHistory);
        @endphp
        @php
            $lastReconciliationItem = \App\Models\ReconciliationItem::with('reconciliation')
            ->where('container_id', $history[0]->id)
            ->join('reconciliations', 'reconciliations.id', '=', 'reconciliation_items.reconciliation_id')
            ->orderBy('reconciliations.created_at', 'desc')
            ->select('reconciliation_items.*')
            ->first();

        @endphp


        {{-- Last Reconciliation Section --}}
        @if ($lastReconciliationItem)
            @php
                $dateTime = new DateTime($lastReconciliationItem->reconciliation->created_at);
                $dateTime->setTimezone(new DateTimeZone('America/Edmonton'));
                $formattedTime = $dateTime->format('g:i A, Y-m-d');
            @endphp

            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Last Reconciliation</h2>
                <p class="text-sm text-gray-600">
                    Container Status:
                    <x-filament::badge
                        :color="match($lastReconciliationItem->status) {
                        'pending' => 'gray',
                        'reconciled' => 'success',
                        'missing' => 'danger',
                        default => 'secondary'
                    }"
                        class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium"
                    >
                        {{ $lastReconciliationItem->status }}
                    </x-filament::badge>
                    (Time: <span class="font-medium">{{ $formattedTime }}</span>)
                </p>
            </div>
        @else
            <p class="text-sm text-gray-600">No reconciliation records found.</p>
        @endif

        {{-- Changes Section --}}
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Location History</h2>

            <div class="overflow-x-auto">
                <div class="space-y-4">
                    @foreach ($processedHistory as $entry)
                        @php if (! $entry['locationChanged']) { continue; } @endphp
                        @php
                            $item = $entry['item'];
                            $locationChanged = $entry['locationChanged'];
                            $previousLocationId = $entry['previousLocationId'];

                            $currentLocation = \App\Models\StorageLocation::find($item->storage_location_id);
                            $previousLocation = $previousLocationId ? \App\Models\StorageLocation::find($previousLocationId) : null;

                            $dateTime = new DateTime($item->ROW_START);
                            $dateTime->setTimezone(new DateTimeZone('America/Edmonton'));
                            $formattedTime = $dateTime->format('g:i A, F j, Y');
                            $user = \App\Models\User::find($item->last_edit_author_id);
                        @endphp
                            <!-- Entry Card -->
                        <div class="border border-gray-200 rounded-lg bg-white shadow hover:shadow-md transition">
                            <!-- Time in Grey Bar -->
                            <div class="bg-gray-100 text-sm font-medium text-gray-800 px-4 py-2 rounded-t-lg">
                                {{ $formattedTime }}
                            </div>
                            <!-- Details -->
                            <div class="p-4 flex flex-col space-y-1">
                                <!-- Location -->
                                @if ($locationChanged)
                                    <span class="text-lg font-medium text-blue-600">
                                        Moved from {{ $previousLocation?->lab?->room_number ?? 'Unknown Lab' }} {{ $previousLocation?->name ?? 'Unknown' }} to {{ $currentLocation?->lab?->room_number ?? 'Unknown Lab' }} {{ $currentLocation?->name ?? 'Unknown' }}
                                    </span>
                                @else
                                    <span class="text-gray-500 italic">
                                        Location: {{ $currentLocation?->name ?? 'Unknown' }}
                                    </span>
                                @endif
                                <!-- Changed By -->
                                <span class="text-sm text-gray-600">
                    Changed By: <span class="font-medium text-gray-800">{{ $user?->name ?? 'Unknown' }}</span>
                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
</div>

