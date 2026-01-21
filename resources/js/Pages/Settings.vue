<template>
    <AppLayout page-title="Pengaturan" page-description="Kustomisasi pengalaman Anda">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Pengaturan Sistem</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Sesuaikan preferensi dan pengalaman Anda</p>
                    </div>
                    <button
                        @click="handleReset"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Reset ke Default</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-4 sticky top-6">
                        <nav class="space-y-1">
                            <button
                                v-for="section in sections"
                                :key="section.id"
                                @click="activeSection = section.id"
                                :class="[
                                    'w-full flex items-center gap-3 px-4 py-3 rounded-lg transition text-left',
                                    activeSection === section.id
                                        ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
                                ]"
                            >
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="section.icon"/>
                                </svg>
                                <span class="text-sm">{{ section.name }}</span>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6">
                        <!-- Appearance Section -->
                        <div v-show="activeSection === 'appearance'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Tampilan</h3>
                                
                                <!-- Theme Mode -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Mode Tema</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <button
                                            v-for="mode in themeMode"
                                            :key="mode.value"
                                            @click="updateThemeMode(mode.value)"
                                            :class="[
                                                'p-4 border-2 rounded-lg transition flex flex-col items-center gap-2',
                                                localSettings.theme.mode === mode.value
                                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'
                                                    : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700'
                                            ]"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="mode.icon"/>
                                            </svg>
                                            <span class="text-sm font-medium">{{ mode.label }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Language Section -->
                        <div v-show="activeSection === 'language'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Bahasa & Regional</h3>
                                
                                <!-- Language -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bahasa</label>
                                    <select
                                        v-model="localSettings.language.locale"
                                        @change="saveSettings"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="id">Bahasa Indonesia</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>

                                <!-- Timezone -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zona Waktu</label>
                                    <select
                                        v-model="localSettings.language.timezone"
                                        @change="saveSettings"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="Asia/Jakarta">WIB - Jakarta</option>
                                        <option value="Asia/Makassar">WITA - Makassar</option>
                                        <option value="Asia/Jayapura">WIT - Jayapura</option>
                                    </select>
                                </div>

                                <!-- Date Format -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format Tanggal</label>
                                    <select
                                        v-model="localSettings.language.date_format"
                                        @change="saveSettings"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="d/m/Y">DD/MM/YYYY (31/12/2026)</option>
                                        <option value="m/d/Y">MM/DD/YYYY (12/31/2026)</option>
                                        <option value="Y-m-d">YYYY-MM-DD (2026-12-31)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications Section -->
                        <div v-show="activeSection === 'notifications'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Notifikasi</h3>
                                
                                <div class="space-y-4">
                                    <div v-for="notif in notificationOptions" :key="notif.key" class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ notif.title }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ notif.description }}</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.notifications[notif.key]"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Display Section -->
                        <div v-show="activeSection === 'display'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Tampilan Data</h3>
                                
                                <!-- Rows per page -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Baris Per Halaman</label>
                                    <select
                                        v-model.number="localSettings.display.rows_per_page"
                                        @change="saveSettings"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100"
                                    >
                                        <option :value="5">5 baris</option>
                                        <option :value="10">10 baris</option>
                                        <option :value="25">25 baris</option>
                                        <option :value="50">50 baris</option>
                                        <option :value="100">100 baris</option>
                                    </select>
                                </div>

                                <!-- Toggle Options -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Mode Kompak</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tampilan lebih padat dengan spacing minimal</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.display.compact_mode"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Animasi</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Aktifkan animasi transisi (nonaktifkan untuk performa lebih baik)</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.display.animations_enabled"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Tooltips</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tampilkan petunjuk saat hover pada elemen</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.display.show_tooltips"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Accessibility Section -->
                        <div v-show="activeSection === 'accessibility'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Aksesibilitas</h3>
                                
                                <!-- Font Size -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Ukuran Font</label>
                                    <div class="grid grid-cols-4 gap-3">
                                        <button
                                            v-for="size in fontSizes"
                                            :key="size.value"
                                            @click="updateFontSize(size.value)"
                                            :class="[
                                                'p-3 border-2 rounded-lg transition',
                                                localSettings.accessibility.font_size === size.value
                                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'
                                                    : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700'
                                            ]"
                                        >
                                            <span class="font-medium" :style="{ fontSize: size.preview }">{{ size.label }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Toggles -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Kontras Tinggi</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tingkatkan kontras warna untuk visibilitas lebih baik</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.accessibility.high_contrast"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Navigasi Keyboard</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Aktifkan shortcut keyboard untuk navigasi cepat</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.accessibility.keyboard_navigation"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Layout Section -->
                        <div v-show="activeSection === 'layout'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Tata Letak</h3>
                                
                                <!-- Sidebar Mode -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Mode Sidebar</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <button
                                            v-for="mode in sidebarModes"
                                            :key="mode.value"
                                            @click="updateSidebarMode(mode.value)"
                                            :class="[
                                                'p-4 border-2 rounded-lg transition',
                                                localSettings.layout.sidebar_mode === mode.value
                                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'
                                                    : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700'
                                            ]"
                                        >
                                            <p class="font-medium text-sm">{{ mode.label }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ mode.description }}</p>
                                        </button>
                                    </div>
                                </div>

                                <!-- Density -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Kepadatan Layout</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <button
                                            v-for="density in densityOptions"
                                            :key="density.value"
                                            @click="updateDensity(density.value)"
                                            :class="[
                                                'p-4 border-2 rounded-lg transition',
                                                localSettings.layout.density === density.value
                                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'
                                                    : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700'
                                            ]"
                                        >
                                            <p class="font-medium text-sm">{{ density.label }}</p>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dashboard Section -->
                        <div v-show="activeSection === 'dashboard'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Dashboard</h3>
                                
                                <!-- Default Page -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Halaman Default</label>
                                    <select
                                        v-model="localSettings.dashboard.default_page"
                                        @change="saveSettings"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="/dashboard">On Hand Dashboard</option>
                                        <option value="/wms-dashboard">WMS Dashboard</option>
                                        <option value="/master-data">Master Data</option>
                                    </select>
                                </div>

                                <!-- Dashboard Widgets -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Statistik</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tampilkan widget statistik</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.dashboard.show_stats"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Grafik</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tampilkan chart dan visualisasi data</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.dashboard.show_charts"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-100">Aktivitas Terkini</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Tampilkan log aktivitas terbaru</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.dashboard.show_recent_activity"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Section -->
                        <div v-show="activeSection === 'advanced'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Lanjutan</h3>
                                
                                <!-- Auto Refresh -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Auto Refresh Data</label>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="localSettings.advanced.auto_refresh"
                                                @change="saveSettings"
                                                class="sr-only peer"
                                            >
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    
                                    <div v-if="localSettings.advanced.auto_refresh">
                                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Interval Refresh (detik)</label>
                                        <input
                                            type="number"
                                            v-model.number="localSettings.advanced.refresh_interval"
                                            @change="saveSettings"
                                            min="10"
                                            max="3600"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100"
                                        >
                                    </div>
                                </div>

                                <!-- Cache -->
                                <div class="flex items-center justify-between py-3">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-100">Cache Browser</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Aktifkan caching untuk performa lebih cepat</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            v-model="localSettings.advanced.cache_enabled"
                                            @change="saveSettings"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Save Status -->
                        <div v-if="saveStatus" class="mt-6 p-4 rounded-lg" :class="saveStatus.success ? 'bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300'">
                            <div class="flex items-center gap-2">
                                <svg v-if="saveStatus.success" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="font-medium">{{ saveStatus.message }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useSettings } from '@/Composables/useSettings.js';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    defaultSettings: {
        type: Object,
        required: true,
    },
});

const { updateSettings, resetSettings, applySettings, initializeSettings } = useSettings();

onMounted(() => {
    initializeSettings(props, true);
});

const activeSection = ref('appearance');
const saveStatus = ref(null);

// Local settings copy for editing
const localSettings = reactive(JSON.parse(JSON.stringify(props.settings)));

// Sections
const sections = [
    { id: 'appearance', name: 'Tampilan', icon: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' },
    { id: 'language', name: 'Bahasa & Regional', icon: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129' },
    { id: 'notifications', name: 'Notifikasi', icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' },
    { id: 'display', name: 'Tampilan Data', icon: 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2' },
    { id: 'accessibility', name: 'Aksesibilitas', icon: 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    { id: 'layout', name: 'Tata Letak', icon: 'M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z' },
    { id: 'dashboard', name: 'Dashboard', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { id: 'advanced', name: 'Lanjutan', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

// Theme modes
const themeMode = [
    { value: 'light', label: 'Terang', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z' },
    { value: 'dark', label: 'Gelap', icon: 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z' },
    { value: 'auto', label: 'Auto', icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z' },
];

// Font sizes
const fontSizes = [
    { value: 'small', label: 'Kecil', preview: '12px' },
    { value: 'medium', label: 'Sedang', preview: '14px' },
    { value: 'large', label: 'Besar', preview: '16px' },
    { value: 'xlarge', label: 'Sangat Besar', preview: '18px' },
];

// Sidebar modes
const sidebarModes = [
    { value: 'auto', label: 'Auto', description: 'Otomatis sesuai layar' },
    { value: 'always_open', label: 'Selalu Terbuka', description: 'Sidebar tetap terbuka' },
    { value: 'always_collapsed', label: 'Selalu Tutup', description: 'Sidebar tetap tutup' },
];

// Density options
const densityOptions = [
    { value: 'compact', label: 'Kompak' },
    { value: 'comfortable', label: 'Nyaman' },
    { value: 'spacious', label: 'Luas' },
];

// Notification options
const notificationOptions = [
    { key: 'email_enabled', title: 'Email', description: 'Terima notifikasi melalui email' },
    { key: 'browser_enabled', title: 'Browser', description: 'Notifikasi push browser' },
    { key: 'sound_enabled', title: 'Suara', description: 'Mainkan suara untuk notifikasi' },
    { key: 'desktop_enabled', title: 'Desktop', description: 'Notifikasi desktop OS' },
];

// Save settings
const saveSettings = async () => {
    const result = await updateSettings(localSettings);
    
    if (result.success) {
        saveStatus.value = {
            success: true,
            message: 'Pengaturan berhasil disimpan!',
        };
        
        // Apply settings immediately
        applySettings(localSettings);
    } else {
        saveStatus.value = {
            success: false,
            message: result.message || 'Gagal menyimpan pengaturan',
        };
    }
    
    // Clear status after 3 seconds
    setTimeout(() => {
        saveStatus.value = null;
    }, 3000);
};

// Update theme mode
const updateThemeMode = (mode) => {
    localSettings.theme.mode = mode;
    saveSettings();
};

// Update font size
const updateFontSize = (size) => {
    localSettings.accessibility.font_size = size;
    saveSettings();
};

// Update sidebar mode
const updateSidebarMode = (mode) => {
    localSettings.layout.sidebar_mode = mode;
    saveSettings();
};

// Update density
const updateDensity = (density) => {
    localSettings.layout.density = density;
    saveSettings();
};

// Reset settings
const handleReset = async () => {
    if (confirm('Yakin ingin mereset semua pengaturan ke default?')) {
        const result = await resetSettings();
        
        if (result.success) {
            // Update local settings
            Object.assign(localSettings, result.settings);
            
            saveStatus.value = {
                success: true,
                message: 'Pengaturan berhasil direset!',
            };
            
            // Apply default settings
            applySettings(result.settings);
        } else {
            saveStatus.value = {
                success: false,
                message: result.message || 'Gagal mereset pengaturan',
            };
        }
        
        // Clear status after 3 seconds
        setTimeout(() => {
            saveStatus.value = null;
        }, 3000);
    }
};

// Apply settings on mount
onMounted(() => {
    applySettings(localSettings);
});
</script>
