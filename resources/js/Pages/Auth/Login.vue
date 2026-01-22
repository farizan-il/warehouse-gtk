<template>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div 
            class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat transition-transform duration-1000 scale-105"
            style="background-image: url('https://gondowangi.com/assets/banners/1752481195_RYkGIg9CND.png');"
        ></div>
        <div class="absolute inset-0 z-10 bg-gradient-to-br from-black/70 via-black/50 to-transparent"></div>

        <!-- Content -->
        <div class="relative z-20 w-full max-w-6xl px-4 flex flex-col items-center">
            
            <!-- Login Card -->
            <div class="w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl p-8 transform transition-all duration-500 hover:shadow-white/5">
                
                <!-- Logo/Header Area -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center p-2 rounded-2xl mb-4 border border-white/10 overflow-hidden">
                        <img 
                            src="https://gondowangi.com/assets/logo/Logo-gondowangi-berwarna.png" 
                            alt="Gondowangi Logo"
                            class="h-16 w-auto object-contain"
                        >
                    </div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Selamat Datang</h2>
                    <p class="text-gray-400 mt-2">Silakan login ke akun Anda</p>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- NIK/Email Input -->
                    <div class="space-y-2">
                        <label class="block text-gray-300 text-sm font-medium ml-1">
                            No Karyawan atau Email
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-green-400 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input 
                                v-model="form.identifier"
                                type="text" 
                                class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300 backdrop-blur-sm"
                                :class="{ 'border-red-500/50 bg-red-500/5': form.errors.identifier }"
                                placeholder="Masukkan NIK atau Email"
                                required
                            >
                        </div>
                        <div v-if="form.errors.identifier" class="text-red-400 text-xs mt-1 px-1 flex items-center animate-pulse">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ form.errors.identifier }}
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <label class="block text-gray-300 text-sm font-medium ml-1">
                            Kata Sandi
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-green-400 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full pl-12 pr-12 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300 backdrop-blur-sm"
                                placeholder="Masukkan password"
                                required
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-white transition-colors"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Database Selection -->
                    <div class="space-y-2">
                        <label class="block text-gray-300 text-sm font-medium ml-1">
                            Pilih Mode Database
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label 
                                class="relative flex flex-col p-3 border rounded-2xl cursor-pointer transition-all duration-300 group shadow-sm"
                                :class="form.database_connection === 'mysql' 
                                    ? 'bg-green-500/20 border-green-500/50 shadow-green-500/10' 
                                    : 'bg-white/5 border-white/10 hover:bg-white/10 hover:border-white/20'"
                            >
                                <input 
                                    v-model="form.database_connection"
                                    type="radio" 
                                    value="mysql"
                                    class="sr-only"
                                >
                                <span class="text-xs font-bold transition-colors" :class="form.database_connection === 'mysql' ? 'text-green-400' : 'text-gray-400'">PRODUCTION</span>
                                <span class="text-[10px] mt-1 transition-colors" :class="form.database_connection === 'mysql' ? 'text-green-300/70' : 'text-gray-500'">Main Server</span>
                                <div v-if="form.database_connection === 'mysql'" class="absolute top-2 right-2 w-1.5 h-1.5 bg-green-400 rounded-full"></div>
                            </label>
                            
                            <label 
                                class="relative flex flex-col p-3 border rounded-2xl cursor-pointer transition-all duration-300 group shadow-sm"
                                :class="form.database_connection === 'mysql_testing' 
                                    ? 'bg-orange-500/20 border-orange-500/50 shadow-orange-500/10' 
                                    : 'bg-white/5 border-white/10 hover:bg-white/10 hover:border-white/20'"
                            >
                                <input 
                                    v-model="form.database_connection"
                                    type="radio" 
                                    value="mysql_testing"
                                    class="sr-only"
                                >
                                <span class="text-xs font-bold transition-colors" :class="form.database_connection === 'mysql_testing' ? 'text-orange-400' : 'text-gray-400'">TESTING</span>
                                <span class="text-[10px] mt-1 transition-colors" :class="form.database_connection === 'mysql_testing' ? 'text-orange-300/70' : 'text-gray-500'">Untuk Dev</span>
                                <div v-if="form.database_connection === 'mysql_testing'" class="absolute top-2 right-2 w-1.5 h-1.5 bg-orange-400 rounded-full"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between pb-2">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input 
                                    v-model="form.remember"
                                    type="checkbox" 
                                    class="sr-only"
                                >
                                <div 
                                    class="w-4 h-4 border rounded transition-colors group-hover:border-green-400"
                                    :class="form.remember ? 'bg-green-500 border-green-500' : 'bg-transparent border-white/20'"
                                >
                                    <svg v-if="form.remember" class="w-3 h-3 text-white mx-auto mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <span class="ml-2 text-xs text-gray-400 group-hover:text-gray-200 transition-colors">Ingat Saya</span>
                        </label>
                        <Link href="/forgot-password" class="text-xs text-green-400 hover:text-green-300 hover:underline transition-colors font-medium">
                            Lupa Kata Sandi?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="relative w-full overflow-hidden group py-4 bg-gradient-to-r from-green-600 to-green-500 text-white rounded-2xl font-bold transition-all duration-300 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-green-900/40 hover:shadow-green-500/30"
                    >
                        <!-- Hover Effect Layer -->
                        <div class="absolute inset-0 w-full h-full bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        
                        <span v-if="form.processing" class="relative z-10 flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            MEMPROSES...
                        </span>
                        <span v-else class="relative z-10 flex items-center justify-center uppercase tracking-widest text-sm">
                            Masuk Ke Sistem
                        </span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center sm:flex sm:items-center sm:justify-center sm:gap-4 text-gray-500 text-[10px] sm:text-xs">
                <p>© 2026 GONDOWANGI WMS</p>
                <span class="hidden sm:inline text-gray-700">|</span>
                <p class="mt-1 sm:mt-0">VERSION 1.0.0-STABLE</p>
                <!-- <span class="hidden sm:inline text-gray-700">|</span>
                <p class="mt-1 sm:mt-0 font-medium text-gray-400">DESIGNED FOR PERFORMANCE</p> -->
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
    identifier: '',
    password: '',
    remember: false,
    database_connection: 'mysql', // Default to production
});

const submit = () => {
    form.post('/login', {
        onFinish: () => {
            // Reset password jika login gagal
            if (Object.keys(form.errors).length > 0) {
                form.password = '';
            }
        },
    });
};

const quickLogin = (email, password) => {
    // Set form data
    form.identifier = email;
    form.password = password;
    form.remember = true;
    form.database_connection = 'mysql';
    
    // Wait for form to be reactive, then submit
    nextTick(() => {
        submit();
    });
};
</script>