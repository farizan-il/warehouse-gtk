<template>
    <AppLayout title="Profile">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-all duration-500">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Left Column: User Profile -->
                <div v-show="!isChatExpanded" class="space-y-8 animate-fadeIn">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="h-32 bg-[#157347]"></div>
                        <div class="px-6 pb-8">
                            <div class="relative flex justify-center -mt-16 mb-6">
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-3xl overflow-hidden border-4 border-white dark:border-gray-900 shadow-2xl bg-gray-100 dark:bg-gray-800">
                                        <img v-if="profilePhotoUrl" :src="profilePhotoUrl" class="w-full h-full object-cover">
                                        <div v-else class="w-full h-full flex items-center justify-center text-[#157347] text-4xl font-bold bg-[#157347]/10 dark:bg-[#157347]/20">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <button 
                                        @click="$refs.photoInput.click()"
                                        class="absolute bottom-2 right-2 p-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#157347] dark:hover:text-[#157347] transition-all opacity-0 group-hover:opacity-100 scale-90 group-hover:scale-100"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    <input type="file" ref="photoInput" class="hidden" accept="image/*" @change="handlePhotoUpload">
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ user.name }}</h3>
                                <p class="text-sm font-bold text-[#157347] bg-[#157347]/10 dark:bg-[#157347]/20 inline-block px-4 py-1.5 rounded-2xl mt-2 tracking-tight">
                                    {{ user.nik }}
                                </p>
                            </div>

                            <div class="mt-8 space-y-4">
                                <!-- Email Info (New) -->
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                    <div class="p-2 bg-[#157347]/10 dark:bg-[#157347]/20 rounded-lg text-[#157347]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Email</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ user.email }}</p>
                                    </div>
                                </div>

                                <!-- Departemen Info -->
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                    <div class="p-2 bg-[#157347]/10 dark:bg-[#157347]/20 rounded-lg text-[#157347]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Departemen</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ user.departement || '-' }}</p>
                                    </div>
                                </div>

                                <!-- Jabatan Info -->
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                    <div class="p-2 bg-[#157347]/10 dark:bg-[#157347]/20 rounded-lg text-[#157347]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Jabatan</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ user.jabatan || '-' }}</p>
                                    </div>
                                </div>

                                <!-- Role & Status Info (New) -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-center">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Status</p>
                                        <span 
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tighter shadow-sm"
                                            :class="user.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                                        >
                                            {{ user.status }}
                                        </span>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 text-center">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Role</p>
                                        <p class="text-xs font-bold text-[#157347] truncate">{{ user.role?.role_name || 'Staff' }}</p>
                                    </div>
                                </div>

                                <!-- Join Date (New) -->
                                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex justify-between items-center px-6">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Terdaftar Sejak</p>
                                    <p class="text-xs font-bold text-gray-900 dark:text-gray-100">{{ formatDate(user.created_at) }}</p>
                                </div>
                            </div>

                            <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-800">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-amber-800 dark:text-amber-200">
                                        Data profile dikelola oleh IT. Hubungi tim developer jika ingin mengubah data diri.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Activity & Support -->
                <div :class="isChatExpanded ? 'lg:col-span-3 h-[calc(100vh-160px)] translate-y-[-32px] sm:translate-y-0' : 'lg:col-span-2'" class="space-y-8 transition-all duration-500 flex flex-col">
                    <!-- Activity Chart -->
                    <div v-show="!isChatExpanded" class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 animate-fadeIn">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Aktivitas Anda</h3>
                                <p class="text-sm text-gray-500">Statistik penggunaan WMS 30 hari terakhir</p>
                            </div>
                            <div class="p-3 bg-[#157347]/10 dark:bg-[#157347]/20 rounded-2xl">
                                <svg class="w-6 h-6 text-[#157347]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="h-64">
                            <Line :data="activityChartData" :options="chartOptions" />
                        </div>
                    </div>

                    <!-- Support Chat Section -->
                    <div :class="isChatExpanded ? 'h-full flex-1' : 'min-h-[500px]'" class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col transition-all duration-500">
                        <!-- Chat Header -->
                        <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-[#157347]/5 dark:bg-[#157347]/10 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-[#157347] rounded-2xl text-white shadow-lg shadow-[#157347]/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                                <div v-if="selectedTicket">
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ selectedTicket.title }}</h3>
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">{{ selectedTicket.status }}</p>
                                </div>
                                <div v-else>
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100">Live Support / Keluhan</h3>
                                    <p class="text-xs text-gray-500">Punya kendala? Chat developer langsung di sini.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button 
                                    @click="isChatExpanded = !isChatExpanded"
                                    class="p-2 text-gray-500 hover:text-[#157347] hover:bg-[#157347]/10 rounded-xl transition-all"
                                    :title="isChatExpanded ? 'Kecilkan Chat' : 'Expand Chat'"
                                >
                                    <svg v-if="!isChatExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </button>
                                <button 
                                    v-if="selectedTicket"
                                    @click="selectedTicket = null"
                                    class="text-sm font-bold text-[#157347] hover:text-[#157347]/80 flex items-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Kembali
                                </button>
                                <button 
                                    v-else
                                    @click="showNewComplaintModal = true"
                                    class="px-4 py-2 bg-[#157347] text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-[#157347]/30 transition-all flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Buat Keluhan
                                </button>
                            </div>
                        </div>

                        <!-- Chat Messages / List -->
                        <div 
                            :class="[
                                'flex-1 overflow-y-auto p-6 space-y-4 transition-all duration-500',
                                isChatExpanded ? 'max-h-[calc(100vh-280px)]' : 'max-h-[400px]'
                            ]"
                            ref="messageContainer"
                        >
                            <template v-if="selectedTicket">
                                <div v-for="msg in messages" :key="msg.id" 
                                    :class="['flex flex-col', msg.sender_id === user.id ? 'items-end' : 'items-start']"
                                >
                                    <div :class="[
                                        'max-w-[80%] p-4 rounded-3xl shadow-sm text-sm',
                                        msg.sender_id === user.id 
                                            ? 'bg-[#157347] text-white rounded-tr-none shadow-[#157347]/10' 
                                            : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-tl-none border border-gray-100 dark:border-gray-800 shadow-sm'
                                    ]">
                                        <p>{{ msg.message }}</p>
                                        <img v-if="msg.attachment_path" 
                                            :src="'/storage/' + msg.attachment_path" 
                                            class="mt-3 rounded-2xl border border-white/20 max-h-48 cursor-pointer"
                                            @click="window.open('/storage/' + msg.attachment_path)"
                                        >
                                    </div>
                                    <span class="text-[10px] text-gray-500 mt-1 uppercase font-bold px-2">
                                        {{ formatDateGlobal(msg.created_at) }}
                                    </span>
                                </div>
                            </template>
                            <template v-else>
                                <div v-if="tickets.length === 0" class="h-full flex flex-col items-center justify-center text-center p-8 opacity-50">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada riwayat keluhan</p>
                                </div>
                                <div v-else v-for="tk in tickets" :key="tk.id" 
                                    @click="loadTicket(tk)"
                                    class="p-4 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 cursor-pointer transition-all flex items-center justify-between"
                                >
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ tk.title }}</h4>
                                        <p class="text-xs text-gray-500 line-clamp-1 italic">
                                            {{ tk.messages?.[0]?.message || 'Tiada pesan' }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span :class="[
                                            'text-[10px] font-bold uppercase px-2 py-0.5 rounded-full',
                                            tk.status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-[#157347]/10 text-[#157347]'
                                        ]">
                                            {{ tk.status }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ formatDateGlobal(tk.last_message_at) }}</span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input -->
                        <div v-if="selectedTicket" class="p-6 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
                            <div class="flex items-end gap-4">
                                <div class="flex-1 relative">
                                    <textarea 
                                        v-model="newMessage"
                                        rows="1"
                                        placeholder="Ketik pesan..."
                                        class="w-full pl-4 pr-32 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#157347] transition-all"
                                        @keydown.enter.prevent="sendReply"
                                    ></textarea>
                                    <div class="absolute right-2 bottom-2 flex items-center gap-2">
                                        <button 
                                            @click="$refs.attachmentInput.click()"
                                            class="p-2 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all text-gray-500"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        </button>
                                        <button 
                                            @click="sendReply"
                                            :disabled="!newMessage.trim() && !pendingAttachment"
                                            class="px-6 py-2 bg-[#157347] text-white text-sm font-bold rounded-xl shadow-lg shadow-[#157347]/30 hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:grayscale"
                                        >
                                            Kirim
                                        </button>
                                    </div>
                                    <input type="file" ref="attachmentInput" class="hidden" @change="handleMessageAttachment">
                                    <div v-if="pendingAttachment" class="absolute -top-12 left-0 right-0 bg-[#157347]/10 dark:bg-[#157347]/20 p-2 rounded-xl border border-[#157347]/20 flex items-center justify-between">
                                        <span class="text-xs text-[#157347] dark:text-[#157347] font-bold truncate">{{ pendingAttachment.name }}</span>
                                        <button @click="pendingAttachment = null" class="text-[#157347]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Complaint Modal -->
        <div v-if="showNewComplaintModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fadeIn">
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-slideUp border border-gray-100 dark:border-gray-800">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-[#157347]/10 dark:bg-[#157347]/20 rounded-lg text-[#157347]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Buat Keluhan Baru</h3>
                    </div>
                    <button @click="showNewComplaintModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Keluhan</label>
                        <input 
                            v-model="newComplaint.title"
                            type="text"
                            placeholder="Contoh: Kesalahan Stok Material A"
                            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-[#157347] focus:border-transparent transition-all dark:text-gray-100"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Pesan</label>
                        <textarea 
                            v-model="newComplaint.message"
                            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-[#157347] focus:border-transparent transition-all dark:text-gray-100 min-h-[120px]"
                            placeholder="Jelaskan kendala Anda secara detail..."
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Foto/Screenshot Bukti</label>
                        <div 
                            @click="$refs.newAttachmentInput.click()"
                            class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-8 cursor-pointer hover:border-[#157347] dark:hover:border-[#157347] transition-all bg-gray-50 dark:bg-gray-800/50"
                        >
                            <div v-if="!newComplaint.attachment" class="text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-500">Klik untuk upload bukti screenshot</p>
                            </div>
                            <div v-else class="flex flex-col items-center">
                                <img :src="attachmentPreview" class="max-h-40 rounded-lg mb-2 shadow-lg">
                                <p class="text-xs text-[#157347] font-bold truncate">{{ newComplaint.attachment.name }}</p>
                            </div>
                        </div>
                        <input type="file" ref="newAttachmentInput" class="hidden" accept="image/*" @change="handleNewAttachment">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                    <button 
                        @click="showNewComplaintModal = false"
                        class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        @click="submitComplaint"
                        :disabled="!newComplaint.title || !newComplaint.message || isSubmitting"
                        class="px-6 py-2 bg-[#157347] text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-[#157347]/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ isSubmitting ? 'Mengirim...' : 'Kirim Keluhan' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useSettings } from '@/Composables/useSettings';
import { ref, computed, nextTick, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const props = defineProps({
    user: Object,
    activityData: Array,
    profilePhotoUrl: String
});

const { formatDateGlobal } = useSettings();
const formatDate = formatDateGlobal;
const isChatExpanded = ref(false);
const pollingInterval = ref(null);

// Activity Chart Data
const activityChartData = computed(() => ({
    labels: props.activityData.map(d => d.date),
    datasets: [{
        label: 'Total Aktivitas',
        data: props.activityData.map(d => d.count),
        borderColor: '#157347',
        backgroundColor: 'rgba(21, 115, 71, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#157347',
        pointRadius: 4,
        pointHoverRadius: 6
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: { 
            beginAtZero: true, 
            grid: { color: 'rgba(0,0,0,0.05)' },
            ticks: { font: { size: 10 } }
        },
        x: { 
            grid: { display: false },
            ticks: { font: { size: 10 } }
        }
    }
};

// Profile Photo Update
const photoInput = ref(null);
const handlePhotoUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 1024 * 1024) {
        alert('File terlalu besar! Maksimal 1MB');
        return;
    }

    const formData = new FormData();
    formData.append('photo', file);

    router.post(route('profile.photo.update'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            // Updated photo will be reflected via props
        }
    });
};

// Complaints & Support
const tickets = ref([]);
const selectedTicket = ref(null);
const messages = ref([]);
const newMessage = ref('');
const pendingAttachment = ref(null);
const messageContainer = ref(null);
const showNewComplaintModal = ref(false);
const isSubmitting = ref(false);

const newComplaint = ref({
    title: '',
    message: '',
    attachment: null
});
const attachmentPreview = ref(null);

const fetchTickets = async () => {
    try {
        const res = await axios.get(route('complaints.index'));
        tickets.value = res.data;
    } catch (e) {
        console.error('Failed to fetch tickets');
    }
};

const loadTicket = async (ticket) => {
    try {
        const res = await axios.get(route('complaints.show', ticket.id));
        selectedTicket.value = res.data;
        messages.value = res.data.messages;
        scrollToBottom();
        startPolling();
    } catch (e) {
        console.error('Failed to load ticket');
    }
};

const startPolling = () => {
    stopPolling();
    pollingInterval.value = setInterval(async () => {
        if (!selectedTicket.value) {
            stopPolling();
            return;
        }
        try {
            const res = await axios.get(route('complaints.show', selectedTicket.value.id));
            // Only update if count is different to avoid unnecessary re-renders
            if (res.data.messages.length !== messages.value.length) {
                messages.value = res.data.messages;
                scrollToBottom();
            }
            // Update status if changed by IT
            if (res.data.status !== selectedTicket.value.status) {
                selectedTicket.value.status = res.data.status;
                // Also update in tickets list
                const tk = tickets.value.find(t => t.id === res.data.id);
                if (tk) tk.status = res.data.status;
            }
        } catch (e) {
            console.error('Polling failed');
        }
    }, 5000); // Poll every 5 seconds
};

const stopPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
        pollingInterval.value = null;
    }
};

const handleNewAttachment = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    newComplaint.value.attachment = file;
    attachmentPreview.value = URL.createObjectURL(file);
};

const handleMessageAttachment = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    pendingAttachment.value = file;
};

const submitComplaint = async () => {
    isSubmitting.value = true;
    const formData = new FormData();
    formData.append('title', newComplaint.value.title);
    formData.append('message', newComplaint.value.message);
    if (newComplaint.value.attachment) {
        formData.append('attachment', newComplaint.value.attachment);
    }

    try {
        const res = await axios.post(route('complaints.store'), formData);
        showNewComplaintModal.value = false;
        newComplaint.value = { title: '', message: '', attachment: null };
        attachmentPreview.value = null;
        await fetchTickets();
        loadTicket(res.data.ticket);
    } catch (e) {
        alert('Gagal mengirim keluhan');
    } finally {
        isSubmitting.value = false;
    }
};

const sendReply = async () => {
    if (!newMessage.value.trim() && !pendingAttachment.value) return;

    const formData = new FormData();
    formData.append('message', newMessage.value);
    if (pendingAttachment.value) {
        formData.append('attachment', pendingAttachment.value);
    }

    try {
        const res = await axios.post(route('complaints.send-message', selectedTicket.value.id), formData);
        messages.value.push(res.data);
        newMessage.value = '';
        pendingAttachment.value = null;
        scrollToBottom();
    } catch (e) {
        alert('Gagal mengirim pesan');
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    });
};

import { onUnmounted } from 'vue';

onMounted(() => {
    fetchTickets();
});

onUnmounted(() => {
    stopPolling();
});
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.animate-fadeIn {
    animation: fadeIn 0.4s ease-out;
}

.animate-slideUp {
    animation: slideUp 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
