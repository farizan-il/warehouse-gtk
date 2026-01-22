import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

// Default settings structure
const DEFAULT_SETTINGS = {
    theme: {
        mode: 'light',
        color_scheme: 'blue',
    },
    language: {
        locale: 'id',
        timezone: 'Asia/Jakarta',
        date_format: 'd/m/Y',
        time_format: 'H:i',
    },
    notifications: {
        email_enabled: true,
        browser_enabled: true,
        sound_enabled: true,
        desktop_enabled: false,
    },
    display: {
        rows_per_page: 10,
        compact_mode: false,
        animations_enabled: true,
        show_tooltips: true,
    },
    accessibility: {
        font_size: 'medium',
        high_contrast: false,
        keyboard_navigation: true,
        screen_reader: false,
    },
    layout: {
        sidebar_mode: 'auto',
        density: 'comfortable',
    },
    dashboard: {
        default_page: '/dashboard',
        show_stats: true,
        show_charts: true,
        show_recent_activity: true,
    },
    advanced: {
        auto_refresh: false,
        refresh_interval: 60,
        cache_enabled: true,
    },
};

// Move settings to global scope to share state across components
const settings = ref(JSON.parse(JSON.stringify(DEFAULT_SETTINGS)));
const isInitialized = ref(false);

export function useSettings() {
    // Return the shared settings state

    /**
     * Initialize settings from page props
     */
    const initializeSettings = (pageProps, force = false) => {
        if (isInitialized.value && !force) return;

        const userSettings = pageProps?.auth?.user?.settings;
        settings.value = userSettings || getDefaultSettings();
        isInitialized.value = true;
    };

    /**
     * Get default settings
     */
    const getDefaultSettings = () => {
        return JSON.parse(JSON.stringify(DEFAULT_SETTINGS));
    };

    /**
     * Update settings on server
     */
    const updateSettings = (newSettings) => {
        return new Promise((resolve) => {
            router.put('/api/settings', newSettings, {
                preserveScroll: true,
                onSuccess: (page) => {
                    const updatedSettings = page.props.auth.user?.settings;
                    if (updatedSettings) {
                        settings.value = updatedSettings;
                        applySettings(updatedSettings);
                    }
                    resolve({ success: true, settings: updatedSettings });
                },
                onError: (errors) => {
                    console.error('Error updating settings:', errors);
                    resolve({ success: false, message: 'Gagal memperbarui pengaturan', errors });
                }
            });
        });
    };

    /**
     * Reset settings to default
     */
    const resetSettings = () => {
        return new Promise((resolve) => {
            router.post('/api/settings/reset', {}, {
                preserveScroll: true,
                onSuccess: (page) => {
                    const updatedSettings = page.props.auth.user?.settings;
                    if (updatedSettings) {
                        settings.value = updatedSettings;
                        applySettings(updatedSettings);
                    }
                    resolve({ success: true, settings: updatedSettings });
                },
                onError: (errors) => {
                    console.error('Error resetting settings:', errors);
                    resolve({ success: false, message: 'Gagal mereset pengaturan', errors });
                }
            });
        });
    };

    /**
     * Apply settings to the UI (dark mode, font size, etc.)
     */
    const applySettings = (settingsToApply) => {
        if (!settingsToApply || typeof document === 'undefined') return;

        const root = document.documentElement;

        // Apply theme mode (dark/light)
        const themeMode = settingsToApply.theme?.mode || 'light';
        if (themeMode === 'dark') {
            root.classList.add('dark');
            localStorage.setItem('theme-mode', 'dark');
        } else if (themeMode === 'light') {
            root.classList.remove('dark');
            localStorage.setItem('theme-mode', 'light');
        } else if (themeMode === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
            localStorage.setItem('theme-mode', 'auto');
        }

        // Apply font size
        const fontSizeMap = {
            small: '14px',
            medium: '16px',
            large: '18px',
            xlarge: '20px',
        };
        if (settingsToApply.accessibility?.font_size) {
            root.style.fontSize = fontSizeMap[settingsToApply.accessibility.font_size] || '16px';
        }

        // Apply high contrast
        if (settingsToApply.accessibility?.high_contrast) {
            root.classList.add('high-contrast');
        } else {
            root.classList.remove('high-contrast');
        }

        // Apply density
        if (settingsToApply.layout?.density) {
            root.setAttribute('data-density', settingsToApply.layout.density);
        } else {
            root.removeAttribute('data-density');
        }

        // Apply compact mode
        if (settingsToApply.display?.compact_mode) {
            root.classList.add('compact-mode');
        } else {
            root.classList.remove('compact-mode');
        }

        // Apply animations
        if (settingsToApply.display?.animations_enabled === false) {
            root.classList.add('no-animations');
        } else {
            root.classList.remove('no-animations');
        }

        // Apply tooltips visibility
        if (settingsToApply.display?.show_tooltips === false) {
            root.classList.add('hide-tooltips');
        } else {
            root.classList.remove('hide-tooltips');
        }

        // Apply sidebar mode attribute
        if (settingsToApply.layout?.sidebar_mode) {
            root.setAttribute('data-sidebar-mode', settingsToApply.layout.sidebar_mode);
        } else {
            root.removeAttribute('data-sidebar-mode');
        }
    };

    /**
     * Toggle dark mode
     */
    const toggleDarkMode = async () => {
        const currentMode = settings.value.theme?.mode || 'light';
        const newMode = currentMode === 'dark' ? 'light' : 'dark';

        const newSettings = {
            ...settings.value,
            theme: {
                ...settings.value.theme,
                mode: newMode,
            },
        };

        return await updateSettings(newSettings);
    };

    /**
     * Get a specific setting value
     */
    const getSetting = (key, defaultValue = null) => {
        const keys = key.split('.');
        let value = settings.value;

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return defaultValue;
            }
        }

        return value;
    };

    /**
     * Set a specific setting value
     */
    const setSetting = async (key, value) => {
        const keys = key.split('.');
        const newSettings = JSON.parse(JSON.stringify(settings.value));
        let current = newSettings;

        for (let i = 0; i < keys.length - 1; i++) {
            const k = keys[i];
            if (!current[k] || typeof current[k] !== 'object') {
                current[k] = {};
            }
            current = current[k];
        }

        current[keys[keys.length - 1]] = value;

        return await updateSettings(newSettings);
    };

    // Watch for system theme changes when in auto mode
    if (typeof window !== 'undefined') {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', (e) => {
            if (settings.value.theme?.mode === 'auto') {
                applySettings(settings.value);
            }
        });
    }

    /**
     * Localization helper
     */
    const translations = {
        id: {
            'dashboard.title': 'Central Data WMS',
            'dashboard.subtitle': 'Kelola semua material di gudang',
            'dashboard.stats': 'Statistik',
            'dashboard.charts': 'Grafik & Tren',
            'dashboard.recent_activity': 'Aktivitas Terkini',
            'dashboard.no_data': 'Tidak ada data',
            'dashboard.search_placeholder': 'Cari kode, nama material, atau lot...',
            'dashboard.filter_all_status': 'Semua Status',
            'dashboard.filter_all_type': 'Semua Tipe',
            'dashboard.filter_all_location': 'Semua Lokasi',
            'dashboard.sort_default': 'Default Sort (Kode)',
            'dashboard.sort_newest': 'Terbaru (Newest)',
            'dashboard.sort_oldest': 'Terlama (Oldest)',
            'dashboard.expired_materials': 'Material Expired',
            'dashboard.items': 'item',
            'dashboard.no_expired_materials': 'Tidak ada material expired',
            'dashboard.alerts_notifications': 'Alerts & Notifikasi',
            'dashboard.new': 'baru',
            'dashboard.no_new_alerts': 'Tidak ada alert baru',
            'dashboard.online_users': 'User Online',
            'dashboard.active': 'Aktif',
            'dashboard.no_users_online': 'Tidak ada user online',
            'dashboard.recent_activity_feed': 'Feed Aktivitas Terkini',
            'dashboard.view_all': 'Lihat Semua',
            'dashboard.performed': 'melakukan',
            'dashboard.no_recent_activities': 'Tidak ada aktivitas baru',
            'activity.title': 'Riwayat Aktivitas',
            'common.loading': 'Memuat...',
            'common.error': 'Terjadi kesalahan',
            'common.close': 'Tutup',
            'common.cancel': 'Batal',
            'common.save': 'Simpan',
            'common.delete': 'Hapus',
            'common.edit': 'Edit',
            'common.print': 'Cetak',
            'common.export': 'Export',
            'common.import': 'Import',
        },
        en: {
            'dashboard.title': 'WMS Central Data',
            'dashboard.subtitle': 'Manage all materials in the warehouse',
            'dashboard.stats': 'Statistics',
            'dashboard.charts': 'Charts & Trends',
            'dashboard.recent_activity': 'Recent Activity',
            'dashboard.no_data': 'No data available',
            'dashboard.search_placeholder': 'Search code, material name, or lot...',
            'dashboard.filter_all_status': 'All Status',
            'dashboard.filter_all_type': 'All Types',
            'dashboard.filter_all_location': 'All Locations',
            'dashboard.sort_default': 'Default Sort (Code)',
            'dashboard.sort_newest': 'Newest',
            'dashboard.sort_oldest': 'Oldest',
            'dashboard.expired_materials': 'Expired Materials',
            'dashboard.items': 'items',
            'dashboard.no_expired_materials': 'No expired materials',
            'dashboard.alerts_notifications': 'Alerts & Notifications',
            'dashboard.new': 'new',
            'dashboard.no_new_alerts': 'No new alerts',
            'dashboard.online_users': 'Online Users',
            'dashboard.active': 'Active',
            'dashboard.no_users_online': 'No users currently online',
            'dashboard.recent_activity_feed': 'Recent Activity Feed',
            'dashboard.view_all': 'View All',
            'dashboard.performed': 'performed',
            'dashboard.no_recent_activities': 'No recent activities found',
            'activity.title': 'Activity History',
            'common.loading': 'Loading...',
            'common.error': 'An error occurred',
            'common.close': 'Close',
            'common.cancel': 'Cancel',
            'common.save': 'Save',
            'common.delete': 'Delete',
            'common.edit': 'Edit',
            'common.print': 'Print',
            'common.export': 'Export',
            'common.import': 'Import',
        }
    };

    const t = (key) => {
        const locale = settings.value.language?.locale || 'id';
        return translations[locale]?.[key] || key;
    };

    /**
     * Global date formatting
     */
    const formatDateGlobal = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        const locale = settings.value.language?.locale === 'en' ? 'en-US' : 'id-ID';
        const format = settings.value.language?.date_format || 'd/m/Y';

        // simple format mapping (limited by toLocaleDateString capabilities without a library)
        if (format === 'Y-m-d') {
            return date.toISOString().split('T')[0];
        } else if (format === 'm/d/Y') {
            return date.toLocaleDateString('en-US');
        } else {
            return date.toLocaleDateString('id-ID'); // Default d/m/Y
        }
    };

    const formatDateTimeGlobal = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        const locale = settings.value.language?.locale === 'en' ? 'en-US' : 'id-ID';

        return date.toLocaleString(locale, {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: settings.value.language?.time_format === 'h:i A'
        });
    };

    return {
        settings,
        initializeSettings,
        updateSettings,
        resetSettings,
        applySettings,
        toggleDarkMode,
        getSetting,
        setSetting,
        getDefaultSettings,
        t,
        formatDateGlobal,
        formatDateTimeGlobal
    };
}
