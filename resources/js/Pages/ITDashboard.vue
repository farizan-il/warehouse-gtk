<template>
  <AppLayout title="Dashboard Admin IT">
    <div class="space-y-6">
      <!-- Header dengan Filter Tanggal -->
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard Admin IT</h2>
          <p class="text-sm text-gray-600 dark:text-gray-400">Monitoring & Statistik Sistem</p>
        </div>
        
        <!-- Filter Tanggal -->
        <div class="flex flex-wrap items-center gap-3 bg-white dark:bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Dari:</label>
            <input 
              type="date" 
              v-model="filters.startDate"
              class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sampai:</label>
            <input 
              type="date" 
              v-model="filters.endDate"
              class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <button 
            @click="applyFilter"
            :disabled="isLoading"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Terapkan
          </button>
          <button 
            @click="resetFilter"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
          >
            Reset
          </button>
        </div>
      </div>
      
      <!-- Info Filter Aktif -->
      <div v-if="filters.startDate && filters.endDate" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
        <p class="text-sm text-blue-800 dark:text-blue-200 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Menampilkan data dari <strong>{{ formatDateGlobal(filters.startDate) }}</strong> sampai <strong>{{ formatDateGlobal(filters.endDate) }}</strong>
        </p>
      </div>

      <!-- Stat Cards -->
      <div v-if="settings.dashboard.show_stats" class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Aktivitas Hari Ini -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full">+{{ stats.total_today }}</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ stats.total_today }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ filters.startDate ? 'Total Aktivitas (Range)' : 'Total Aktivitas Hari Ini' }}</p>
        </div>

        <!-- Pengguna Aktif -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-lg">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-2 py-1 rounded-full">Aktif</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ activeUsers }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengguna Unik</p>
        </div>

        <!-- Volume Mingguan -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-lg">
              <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-full">Mingguan</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ stats.total_week }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aktivitas Minggu Ini</p>
        </div>

        <!-- Volume Bulanan -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-full">Bulanan</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ stats.total_month }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aktivitas Bulan Ini</p>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div v-if="settings.dashboard.show_charts" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
              {{ chartMode === 'daily' ? 'Aktivitas Per Hari (Range Tanggal Dipilih)' : 'Aktivitas Per Jam (24 Jam Terakhir)' }}
            </h3>
            <span v-if="chartMode === 'daily'" class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full">
              Mode: Harian
            </span>
            <span v-else class="text-xs px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full">
              Mode: Per Jam
            </span>
          </div>
          <div class="h-64">
            <Line :data="timeChartData" :options="lineChartOptions" />
          </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
          <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Penggunaan Sistem per Modul</h3>
          <div class="h-64 flex justify-center">
            <Doughnut :data="moduleChartData" :options="doughnutOptions" />
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div v-if="settings.dashboard.show_charts" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Users Chart -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
          <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">5 Pengguna Paling Aktif</h3>
          <div class="h-64">
            <Bar :data="userChartData" :options="barOptions" />
          </div>
        </div>

        <!-- Error Rate Chart -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Tren Error Sistem
            </h3>
            <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400">Monitoring Real-time</span>
          </div>
          <div class="h-64">
            <Bar :data="errorBarData" :options="errorBarOptions" />
          </div>
        </div>
      </div>

      <!-- Online Users & recent Activities Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Expired Materials Widget -->
        <div v-if="settings.dashboard.show_stats && expiredMaterialsCount > 0" class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ t('dashboard.expired_materials') }}</h3>
                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-xs font-semibold rounded-full">
                    {{ expiredMaterialsCount }} {{ t('dashboard.items') }}
                </span>
            </div>
            <div class="space-y-2">
                <div v-for="material in expiredMaterials" :key="material.id" class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                    <span>{{ material.name }}</span>
                    <span class="text-red-600 dark:text-red-400">{{ formatDateGlobal(material.expiry_date) }}</span>
                </div>
            </div>
            <div v-if="expiredMaterials.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-500 text-sm">
                {{ t('dashboard.no_expired_materials') }}
            </div>
        </div>

        <!-- Alerts/Notifications -->
        <div v-if="settings.dashboard.show_stats && localAlerts.length > 0" class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ t('dashboard.alerts_notifications') }}</h3>
                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 text-xs font-semibold rounded-full">
                    {{ localAlerts.length }} {{ t('dashboard.new') }}
                </span>
            </div>
            <div class="space-y-3">
                <div v-for="alert in localAlerts" :key="alert.id" 
                    :class="[
                        'p-3 rounded-lg border transition-all hover:scale-[1.01]',
                        alert.severity === 'critical' 
                            ? 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800' 
                            : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-100 dark:border-yellow-800'
                    ]"
                >
                    <div class="flex items-start gap-3">
                        <div v-if="alert.severity === 'critical'" class="mt-0.5">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p :class="['text-sm font-medium', alert.severity === 'critical' ? 'text-red-900 dark:text-red-100' : 'text-yellow-900 dark:text-yellow-100']">
                                {{ alert.message }}
                            </p>
                            <p :class="['text-xs mt-1', alert.severity === 'critical' ? 'text-red-700 dark:text-red-400' : 'text-yellow-700 dark:text-yellow-400']">
                                {{ formatDateTimeGlobal(alert.created_at) }}
                            </p>
                        </div>
                        <div v-if="alert.id.startsWith('error_')" class="flex-shrink-0">
                            <button @click="resolveError(alert.id)" class="text-[10px] font-bold uppercase tracking-wider bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 px-2 py-1 rounded hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                                Tandai Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="localAlerts.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-500 text-sm">
                {{ t('dashboard.no_new_alerts') }}
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
                <p class="text-[10px] text-gray-400 dark:text-gray-600 uppercase tracking-widest font-semibold flex items-center justify-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    PENGIRIM: it.verif@gondowangi.com
                </p>
            </div>
        </div>

        <!-- Online Users -->
        <div v-if="settings.dashboard.show_recent_activity" class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ t('dashboard.online_users') }}</h3>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 text-xs font-semibold rounded-full animate-pulse">
                    {{ onlineUsers.length }} {{ t('dashboard.active') }}
                </span>
            </div>
            
            <div v-if="onlineUsers.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
                <div v-for="user in onlineUsers" :key="user.id" class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-500 text-sm">
                {{ t('dashboard.no_users_online') }}
            </div>
        </div>

        <!-- Recent Activities Feed -->
        <div v-if="settings.dashboard.show_recent_activity" class="lg:col-span-2 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Feed Aktivitas Terkini</h3>
                <div class="flex items-center gap-3">
                    <!-- Toggle Hide Login/Logout -->
                    <button 
                        @click="hideLoginLogout = !hideLoginLogout"
                        :class="[
                            'px-3 py-1.5 text-xs font-medium rounded-lg transition-colors flex items-center gap-1.5',
                            hideLoginLogout 
                                ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800' 
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700'
                        ]"
                    >
                        <svg v-if="hideLoginLogout" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        </svg>
                        <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ hideLoginLogout ? 'Login/Logout Disembunyikan' : 'Sembunyikan Login/Logout' }}
                    </button>
                    <Link href="/activity-log" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline">{{ t('dashboard.view_all') }}</Link>
                </div>
            </div>

            <div class="space-y-4">
                <div v-for="activity in filteredActivities" :key="activity.id + activity.module" class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="flex-shrink-0 mt-1">
                        <span v-if="activity.module === 'Incoming'" class="bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 p-1.5 rounded-lg text-xs">IN</span>
                        <span v-else-if="activity.module === 'QC'" class="bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 p-1.5 rounded-lg text-xs">QC</span>
                        <span v-else-if="activity.module === 'Stock Movement'" class="bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400 p-1.5 rounded-lg text-xs">MV</span>
                        <span v-else-if="activity.module === 'Master Data'" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 p-1.5 rounded-lg text-xs">MD</span>
                        <span v-else class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 p-1.5 rounded-lg text-xs">{{ activity.module.substring(0,2).toUpperCase() }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            <span class="font-bold">{{ activity.user }} </span> 
                            <span class="font-normal text-gray-600 dark:text-gray-400"> melakukan</span>
                            <span class="text-blue-600 dark:text-blue-400"> {{ activity.action }}</span>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ activity.description }}</p>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ activity.created_at }}
                    </div>
                </div>
                
                <div v-if="filteredActivities.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-500">
                    {{ hideLoginLogout ? 'Tidak ada aktivitas (Login/Logout disembunyikan)' : t('dashboard.no_recent_activities') }}
                </div>
            </div>
        </div>
      </div>
      
      </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref, reactive } from 'vue'
import { useSettings } from '@/Composables/useSettings';

const { settings, t, formatDateGlobal, formatDateTimeGlobal } = useSettings();

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line, Bar, Doughnut } from 'vue-chartjs'

// Register ChartJS components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
  stats: Object,
  activeUsers: Number,
  onlineUsers: Array,
  moduleStats: Object,
  chartMode: String, // 'hourly' or 'daily'
  chartLabels: Array,
  chartData: Array,
  errorChartData: Array,
  topUsers: Array,
  recentActivities: Array,
  expiredMaterials: Array,
  expiredMaterialsCount: Number,
  localAlerts: Array
})

// Filter state
const filters = reactive({
  startDate: '',
  endDate: ''
})

const isLoading = ref(false)
const hideLoginLogout = ref(false)

// Apply date filter
const applyFilter = () => {
  if (!filters.startDate || !filters.endDate) {
    alert('Silakan pilih tanggal mulai dan tanggal akhir')
    return
  }
  
  isLoading.value = true
  router.get('/it-dashboard', {
    start_date: filters.startDate,
    end_date: filters.endDate
  }, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false
    }
  })
}

// Reset filter
const resetFilter = () => {
  filters.startDate = ''
  filters.endDate = ''
  isLoading.value = true
  router.get('/it-dashboard', {}, {
    preserveState: false,
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false
    }
  })
}

// Filtered activities (hide login/logout if toggle is on)
const filteredActivities = computed(() => {
  if (!hideLoginLogout.value) {
    return props.recentActivities
  }
  
  return props.recentActivities.filter(activity => {
    const action = (activity.action || '').toLowerCase()
    const description = (activity.description || '').toLowerCase()
    
    // Filter out login and logout activities
    const isLoginLogout = 
      action.includes('login') || 
      action.includes('logout') ||
      action.includes('masuk') ||
      action.includes('keluar') ||
      description.includes('login') ||
      description.includes('logout') ||
      description.includes('masuk ke sistem') ||
      description.includes('keluar dari sistem')
    
    return !isLoginLogout
  })
})

// Resolve system error
const resolveError = (alertId) => {
    if (!alertId.startsWith('error_')) return;
    
    const dbId = alertId.replace('error_', '');
    
    if (confirm('Tandai error ini sebagai sudah diperbaiki?')) {
        router.post(`/it-dashboard/resolve-error/${dbId}`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Flash message handled by backend
            }
        });
    }
}

// Chart Data Configuration

// 1. Time-based Line Chart (Hourly or Daily)
const timeChartData = computed(() => ({
  labels: props.chartLabels,
  datasets: [
    {
      label: 'Aktivitas',
      data: props.chartData,
      borderColor: '#3B82F6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      tension: 0.4,
      fill: true
    },
    {
      label: 'Error Sistem',
      data: props.errorChartData || [],
      borderColor: '#EF4444',
      backgroundColor: 'rgba(239, 68, 68, 0.1)',
      tension: 0.4,
      fill: true
    }
  ]
}))

// 2. Module Doughnut Chart
const moduleChartData = computed(() => ({
  labels: Object.keys(props.moduleStats),
  datasets: [{
    data: Object.values(props.moduleStats),
    backgroundColor: [
      '#3B82F6', // Blue
      '#F59E0B', // Amber
      '#10B981', // Emerald
      '#EF4444', // Red
      '#8B5CF6', // Violet
      '#EC4899', // Pink
      '#6B7280'  // Gray
    ]
  }]
}))

// 3. Top Users Bar Chart
const userChartData = computed(() => ({
  labels: props.topUsers.map(u => u.name),
  datasets: [{
    label: 'Total Aksi',
    data: props.topUsers.map(u => u.count),
    backgroundColor: '#8B5CF6',
    borderRadius: 6
  }]
}))

// Chart Options
const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top',
      align: 'end',
      labels: {
        usePointStyle: true,
        padding: 20,
        boxWidth: 8,
        font: { size: 11 }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        display: true,
        borderDash: [2, 2]
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}

const errorBarData = computed(() => ({
  labels: props.chartLabels,
  datasets: [{
    label: 'Error Berdasarkan Waktu',
    data: props.errorChartData || [],
    backgroundColor: '#EF4444',
    borderRadius: 4,
    barThickness: 12
  }]
}))

const errorBarOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
         stepSize: 1
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'right'
    }
  }
}

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      beginAtZero: true
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}
</script>
