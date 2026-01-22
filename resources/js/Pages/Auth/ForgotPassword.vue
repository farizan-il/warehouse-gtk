<template>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-900 font-outfit">
        <!-- Background Image with Overlay -->
        <div 
            class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat transition-transform duration-1000 scale-105"
            style="background-image: url('https://gondowangi.com/assets/banners/1752481195_RYkGIg9CND.png');"
        ></div>
        <div class="absolute inset-0 z-10 bg-gradient-to-br from-black/70 via-black/50 to-transparent"></div>

        <!-- Content -->
        <div class="relative z-20 w-full max-w-6xl px-4 flex flex-col items-center">
            
            <!-- Forgot Password Card -->
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
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Lupa Kata Sandi?</h2>
                    <p class="text-gray-400 mt-2 px-4 text-sm leading-relaxed">
                        Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi Anda.
                    </p>
                </div>

                <div v-if="status" class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-2xl text-green-400 text-sm font-medium text-center animate-pulse">
                    {{ status }}
                </div>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label class="block text-gray-300 text-sm font-medium ml-1">
                            Email
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-green-400 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input 
                                v-model="form.email"
                                type="email" 
                                class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300 backdrop-blur-sm"
                                :class="{ 'border-red-500/50 bg-red-500/5': form.errors.email }"
                                placeholder="Masukkan email Anda"
                                required
                            >
                        </div>
                        <div v-if="form.errors.email" class="text-red-400 text-xs mt-1 px-1 flex items-center animate-pulse">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="relative w-full overflow-hidden group py-4 bg-gradient-to-r from-green-600 to-green-500 text-white rounded-2xl font-bold transition-all duration-300 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-green-900/40 hover:shadow-green-500/30"
                    >
                        <div class="absolute inset-0 w-full h-full bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        
                        <span v-if="form.processing" class="relative z-10 flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            MENGIRIM...
                        </span>
                        <span v-else class="relative z-10 flex items-center justify-center uppercase tracking-widest text-sm">
                            Kirim Tautan Reset
                        </span>
                    </button>
                    
                    <div class="text-center pt-2">
                        <Link href="/login" class="text-xs text-gray-400 hover:text-green-400 transition-colors flex items-center justify-center gap-1 group">
                            <svg class="w-3.5 h-3.5 transform transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Login
                        </Link>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-gray-500 text-[10px] sm:text-xs tracking-wider uppercase">
                <p>© 2026 GONDOWANGI WMS | SYSTEM AUTHENTICATION</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>
