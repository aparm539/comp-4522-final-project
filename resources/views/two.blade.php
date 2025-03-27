<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chemical Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Dashboard Container -->
    <div class="min-h-screen flex flex-col">
        <!-- Header with Logo and Buttons -->
        <header class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="https://www.mtroyal.ca/AboutMountRoyal/MarketingCommunications/_image/MRU_1c_logo_Outline_pms539.png" alt="Logo" class="h-10 w-auto">
                    <span class="ml-2 text-xl font-semibold text-gray-800 dark:text-gray-200">Chemical Inventory Management</span>
                </div>
                <!-- Buttons -->
                <div class="flex items-center space-x-4">
                    <!-- Add New Chemical -->
                    <a href="{{ route('chemicals.create') }}" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-red-700 dark:bg-indigo-500 dark:hover:bg-red-600">
                        Add New Chemical
                    </a>
                    
                    <!-- Remove Chemical -->
                    <a href="{{ route('chemicals.deleteForm') }}" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-red-700 dark:bg-indigo-500 dark:hover:bg-red-600">
                        Remove Chemical
                    </a>
                    
                    <!-- Edit Chemical -->
                    <a href="{{ route('chemicals.editForm') }}" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-red-600 dark:bg-indigo-400 dark:hover:bg-red-500">
                        Edit Chemical
                    </a>
                </div>
            </div>
        </header>

       <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="min-w-full mx-auto overflow-x-auto">
                <!-- Table Section -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <div class="relative overflow-x-auto">
                        <table class="min-w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Barcode</th>
                                    <th scope="col" class="px-6 py-3">CAS</th>
                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                    <th scope="col" class="px-6 py-3">Unit of Measure</th>
                                    <th scope="col" class="px-6 py-3">Location ID</th>
                                    <th scope="col" class="px-6 py-3">Hazardous</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Supervisor ID</th>
                                    <th scope="col" class="px-6 py-3">Chemical Name</th>
                                    <th scope="col" class="px-6 py-3">Container Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table Rows (Dummy Data) -->
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">1</td>
                                    <td class="px-6 py-4">MRUC0001BU</td>
                                    <td class="px-6 py-4">7647-14-5</td>
                                    <td class="px-6 py-4">10</td>
                                    <td class="px-6 py-4">3</td>
                                    <td class="px-6 py-4">55</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-02-01</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Sodium Chloride</td>
                                    <td class="px-6 py-4">Sodium chloride</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">2</td>
                                    <td class="px-6 py-4">MRUC000026</td>
                                    <td class="px-6 py-4">9001-62-1</td>
                                    <td class="px-6 py-4">70</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">7</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-09</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Lipase from Chromobacterium viscosum</td>
                                    <td class="px-6 py-4">Lipase from porcine pancreas BioChemika, powder, 15-35 units/mg</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">3</td>
                                    <td class="px-6 py-4">MRUC00002X</td>
                                    <td class="px-6 py-4">91079-38-8</td>
                                    <td class="px-6 py-4">400</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">56</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-09</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Peptone special</td>
                                    <td class="px-6 py-4">Peptone From Meat</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">4</td>
                                    <td class="px-6 py-4">MRUC00006Q</td>
                                    <td class="px-6 py-4">540-84-1</td>
                                    <td class="px-6 py-4">4</td>
                                    <td class="px-6 py-4">7</td>
                                    <td class="px-6 py-4">86</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-14</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Isooctane</td>
                                    <td class="px-6 py-4">Isooctane</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">5</td>
                                    <td class="px-6 py-4">MRUC00003X</td>
                                    <td class="px-6 py-4">103-84-4</td>
                                    <td class="px-6 py-4">250</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">22</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-11</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Acetanilide</td>
                                    <td class="px-6 py-4">Acetanilide</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">6</td>
                                    <td class="px-6 py-4">MRUC00003Y</td>
                                    <td class="px-6 py-4">7440-70-2</td>
                                    <td class="px-6 py-4">100</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">76</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-11</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Calcium</td>
                                    <td class="px-6 py-4">Calcium metal</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">7</td>
                                    <td class="px-6 py-4">MRUC000047</td>
                                    <td class="px-6 py-4">7791-18-6</td>
                                    <td class="px-6 py-4">500</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">52</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-11</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Magnesium Chloride Hexahydrate</td>
                                    <td class="px-6 py-4">Magnesium Chloride Hexahydrate</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">8</td>
                                    <td class="px-6 py-4">MRUC000048</td>
                                    <td class="px-6 py-4">619-50-1</td>
                                    <td class="px-6 py-4">100</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">81</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-11</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Methyl 4-nitrobenzoate</td>
                                    <td class="px-6 py-4">Methyl 4-nitrobenzoate</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">9</td>
                                    <td class="px-6 py-4">MRUC00004C</td>
                                    <td class="px-6 py-4">10124-56-8</td>
                                    <td class="px-6 py-4">1000</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">43</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-11</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">Sodium Metaphosphate</td>
                                    <td class="px-6 py-4">sodium hexametaphosphate, technical granular</td>
                                </tr>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">10</td>
                                    <td class="px-6 py-4">MRUC00004D</td>
                                    <td class="px-6 py-4">10124-56-8</td>
                                    <td class="px-6 py-4">1000</td>
                                    <td class="px-6 py-4">2</td>
                                    <td class="px-6 py-4">53</td>
                                    <td class="px-6 py-4">f</td>
                                    <td class="px-6 py-4">2019-01-11</td>
                                    <td class="px-6 py-4">1</td>
                                    <td class="px-6 py-4">sodium hexametaphosphate, technical granular</td>
                                    <td class="px-6 py-4">sodium hexametaphosphate, technical granular</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Page footer similar to Sunnys'-->
                <div class="flex items-center justify-between border-t border-gray-200 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 mt-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Previous</a>
                        <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Next</a>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">97</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:ring-gray-600 dark:hover:bg-gray-700">
                                    <span class="sr-only">Previous</span>
                                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">1</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">2</a>
                                <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">3</a>
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 ring-inset focus:outline-offset-0 dark:text-gray-300 dark:ring-gray-600">...</span>
                                <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">8</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">9</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">10</a>
                                <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:ring-gray-600 dark:hover:bg-gray-700">
                                    <span class="sr-only">Next</span>
                                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>