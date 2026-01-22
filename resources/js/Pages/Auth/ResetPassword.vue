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
            
            <!-- Reset Password Card -->
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
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Atur Ulang Sandi</h2>
                    <p class="text-gray-400 mt-2 text-sm">Buat kata sandi baru untuk akun Anda</p>
                </div>
                
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email (Hidden or Readonly) -->
                    <div class="space-y-2">
                        <label class="block text-gray-300 text-sm font-medium ml-1">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input 
                                v-model="form.email"
                                type="email" 
                                class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-gray-400 cursor-not-allowed backdrop-blur-sm"
                                readonly
                                required
                            >
                        </div>
                    </div>

                    <!-- New Password Input -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="block text-gray-300 text-sm font-medium">
                                Kata Sandi Baru
                            </label>
                            <button 
                                type="button" 
                                @click="generateStrongPassword"
                                class="text-xs text-green-400 hover:text-green-300 transition-colors font-medium flex items-center group/btn"
                            >
                                <svg class="w-3.5 h-3.5 mr-1 group-hover/btn:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Saran sandi kuat
                            </button>
                        </div>
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
                                placeholder="Min. 8 karakter"
                                required
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-white transition-colors"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.047m4.5 4.5a3 3 0 003.758 3.758m4.214-4.214a3 3 0 01-3.758-3.758M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="text-red-400 text-xs mt-1 px-1 flex items-center">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="space-y-2">
                        <label class="block text-gray-300 text-sm font-medium ml-1">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-green-400 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="w-full pl-12 pr-12 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-300 backdrop-blur-sm"
                                placeholder="Ulangi kata sandi baru"
                                required
                            >
                            <button 
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-white transition-colors"
                            >
                                <svg v-if="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.047m4.5 4.5a3 3 0 003.758 3.758m4.214-4.214a3 3 0 01-3.758-3.758M3 3l18 18" />
                                </svg>
                            </button>
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
                            MEMPROSES...
                        </span>
                        <span v-else class="relative z-10 flex items-center justify-center uppercase tracking-widest text-sm">
                            Reset Kata Sandi
                        </span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-gray-500 text-[10px] tracking-wider uppercase">
                <p>© 2026 GONDOWANGI WMS | SECURITY CENTER</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: String,
    token: String,
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const generateStrongPassword = () => {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
    let retVal = "";
    for (let i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    form.password = retVal;
    form.password_confirmation = retVal;
    
    // Tampilkan password setelah generate agar user bisa mencatat/melihat
    showPassword.value = true;
    showConfirmPassword.value = true;
};

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
