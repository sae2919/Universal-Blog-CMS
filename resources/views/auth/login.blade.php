<x-guest-layout>
    <div class="mb-8 text-center sm:text-left">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            {{ __('Welcome Back') }}
        </h2>
        <p class="text-sm text-slate-400 mt-2">
            {{ __('Sign in to manage your blog articles & configurations.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-350">
                {{ __('Email Address') }}
            </label>
            <div class="relative flex items-center group">
                <div class="absolute left-4 text-slate-500 group-focus-within:text-indigo-400 transition-colors pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="name@example.com"
                       class="block w-full pl-12 pr-4 py-3 bg-slate-900/60 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 shadow-sm transition-all duration-200">
            </div>
            @error('email')
                <p class="text-xs text-red-450 font-semibold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-350">
                    {{ __('Password') }}
                </label>
            </div>
            <div class="relative flex items-center group">
                <div class="absolute left-4 text-slate-500 group-focus-within:text-indigo-400 transition-colors pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="block w-full pl-12 pr-4 py-3 bg-slate-900/60 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 shadow-sm transition-all duration-200">
            </div>
            @error('password')
                <p class="text-xs text-red-450 font-semibold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember"
                       class="rounded border-slate-800 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0 bg-slate-900/50 cursor-pointer">
                <span class="ms-2 text-xs text-slate-350 font-medium">
                    {{ __('Remember me') }}
                </span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full inline-flex items-center justify-center px-4 py-3.5 bg-gradient-to-r from-indigo-500 via-indigo-600 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('Sign In to Dashboard') }}
            </button>
        </div>
    </form>
</x-guest-layout>
