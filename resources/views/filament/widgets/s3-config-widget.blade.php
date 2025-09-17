<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            S3 & Environment Configuration
        </x-slot>

        <x-slot name="description">
            Production environment status and configuration verification
        </x-slot>

        <!-- Primary Status Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Environment Status -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                        <x-heroicon-m-server class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($environment) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Environment</p>
                    </div>
                </div>
            </div>

            <!-- S3 Connection Status -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $s3Connected ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                        <x-heroicon-m-signal class="h-5 w-5 {{ $s3Connected ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                    </div>
                    <div>
                        <h3 class="font-semibold {{ $s3Connected ? 'text-green-900 dark:text-green-100' : 'text-red-900 dark:text-red-100' }}">
                            {{ $s3ConnectionTest }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">S3 Connection</p>
                    </div>
                </div>
            </div>

            <!-- Session Status -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $sessionDriver === 'database' ? 'bg-green-100 dark:bg-green-900' : 'bg-yellow-100 dark:bg-yellow-900' }}">
                        <x-heroicon-m-identification class="h-5 w-5 {{ $sessionDriver === 'database' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($sessionDriver) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Session Driver</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Server Details Accordion -->
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
            <button
                type="button"
                class="w-full px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-t-lg"
                onclick="toggleAccordion()"
                id="accordion-button"
            >
                <div class="flex items-center justify-between">
                    <span>Server Configuration Details</span>
                    <x-heroicon-m-chevron-down class="h-4 w-4 transition-transform duration-200" id="accordion-icon" />
                </div>
            </button>

            <div class="hidden border-t border-gray-200 dark:border-gray-700" id="accordion-content">
                <div class="p-4 space-y-4">

                    <!-- S3 Configuration -->
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-heroicon-m-cloud class="h-4 w-4" />
                            S3 Storage Configuration
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Default Disk:</span>
                                <span class="ml-2 {{ $defaultDisk === 's3' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                    {{ $defaultDisk }}
                                </span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Region:</span>
                                <span class="ml-2 text-gray-900 dark:text-white">{{ $s3Region }}</span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Bucket:</span>
                                <span class="ml-2 text-gray-900 dark:text-white">{{ $s3Bucket }}</span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Endpoint:</span>
                                <span class="ml-2 text-gray-900 dark:text-white text-xs">{{ $s3Endpoint }}</span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <!-- Authentication & Security -->
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-heroicon-m-key class="h-4 w-4" />
                            Authentication & Security
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Access Key:</span>
                                <span class="ml-2 {{ $hasS3AccessKey ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $s3AccessKey }}
                                </span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Secret Key:</span>
                                <span class="ml-2 {{ $hasS3SecretKey ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $s3SecretKey }}
                                </span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">App Key:</span>
                                <span class="ml-2 {{ $hasAppKey ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $appKey }}
                                </span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Secure Cookies:</span>
                                <span class="ml-2 {{ $sessionSecure ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                    {{ $sessionSecure ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <!-- System Configuration -->
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-heroicon-m-cog-6-tooth class="h-4 w-4" />
                            System Configuration
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Cache Store:</span>
                                <span class="ml-2 {{ $cacheStore === 'database' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                    {{ $cacheStore }}
                                </span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Connection Test:</span>
                                <span class="ml-2 {{ $s3Connected ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $s3ConnectionTest }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    <script>
        function toggleAccordion() {
            const content = document.getElementById('accordion-content');
            const icon = document.getElementById('accordion-icon');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-filament-widgets::widget>