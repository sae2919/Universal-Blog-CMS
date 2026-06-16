<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                @if(\App\Models\Setting::getValue('site_logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::getValue('site_logo')) }}"
                         alt="{{ \App\Models\Setting::getValue('site_name') }}"
                         class="h-8 w-auto">
                @else
                    <span class="text-xl font-bold text-indigo-600">
                        {{ \App\Models\Setting::getValue('site_name', config('app.name')) }}
                    </span>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ url('/') }}"
                   class="text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                    {{ __('Home') }}
                </a>

                @if(isset($mainMenu) && $mainMenu && $mainMenu->items->isNotEmpty())
                    @foreach($mainMenu->items as $item)
                        @if($item->children->isNotEmpty())
                            {{-- Dropdown --}}
                            <div class="relative flex items-center gap-0.5" x-data="{ open: false }" @click.outside="open = false">
                                <a href="{{ $item->url === '#' ? route('blog.index') : $item->url }}" target="{{ $item->target }}"
                                   class="text-sm font-medium {{ (request()->url() == url($item->url) || ($item->url === '#' && request()->routeIs('blog.*'))) ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                                    {{ $item->title }}
                                </a>
                                <button @click="open = !open"
                                        class="p-1 text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors">
                                    <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute top-full left-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-150 dark:border-slate-700 py-2 z-55">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->url }}" target="{{ $child->target }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 transition-colors">
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->url }}" target="{{ $item->target }}"
                               class="text-sm font-medium {{ request()->url() == url($item->url) ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                                {{ $item->title }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('blog.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                        {{ __('Blog') }}
                    </a>

                    {{-- Categories Dropdown --}}
                    @php
                        $navCategories = \Illuminate\Support\Facades\Cache::remember('nav.categories', now()->addHours(6), function() {
                            return \App\Models\Category::active()->roots()->orderBy('sort_order')->take(6)->get();
                        });
                    @endphp

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                            {{ __('Categories') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition
                             class="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            @foreach($navCategories as $cat)
                                <a href="{{ route('blog.category', $cat->slug) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($headerPages) && $headerPages->isNotEmpty())
                    @foreach($headerPages as $page)
                        <a href="{{ url('/' . $page->slug) }}"
                           class="text-sm font-medium {{ request()->url() == url($page->slug) ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                            {{ $page->title }}
                        </a>
                    @endforeach
                @endif
            </nav>

            {{-- Right Area (Language Switcher) --}}
            <div class="hidden md:flex items-center gap-4">
                {{-- Language Switcher --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" 
                            class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-gray-750 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-850 transition-colors cursor-pointer">
                        @if(app()->getLocale() === 'en')
                            🇺🇸 EN
                        @elseif(app()->getLocale() === 'fr')
                            🇫🇷 FR
                        @elseif(app()->getLocale() === 'de')
                            🇩🇪 DE
                        @elseif(app()->getLocale() === 'hi')
                            🇮🇳 HI
                        @elseif(app()->getLocale() === 'te')
                            🇮🇳 TE
                        @endif
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition 
                         class="absolute right-0 top-full mt-2 w-32 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-150 dark:border-slate-700 py-1 z-55 overflow-hidden"
                         style="display: none;">
                        <a href="?lang=en" class="block px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 transition-colors">🇺🇸 English</a>
                        <a href="?lang=fr" class="block px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 transition-colors">🇫🇷 Français</a>
                        <a href="?lang=de" class="block px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 transition-colors">🇩🇪 Deutsch</a>
                        <a href="?lang=hi" class="block px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 transition-colors">🇮🇳 हिन्दी</a>
                        <a href="?lang=te" class="block px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 transition-colors">🇮🇳 తెలుగు</a>
                    </div>
                </div>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-transition class="md:hidden bg-white border-t border-gray-100">
        <div class="px-4 py-3 space-y-2">
            <a href="{{ url('/') }}" class="block py-2 text-sm {{ request()->routeIs('home') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:text-indigo-600' }}">
                {{ __('Home') }}
            </a>
            @if(isset($mainMenu) && $mainMenu && $mainMenu->items->isNotEmpty())
                @foreach($mainMenu->items as $item)
                    @if($item->children->isNotEmpty())
                        <div x-data="{ open: false }" class="border-b border-gray-50 pb-1">
                            <div class="flex items-center justify-between py-1">
                                <a href="{{ $item->url === '#' ? route('blog.index') : $item->url }}" target="{{ $item->target }}" 
                                   class="text-sm text-gray-750 hover:text-indigo-600 font-medium">
                                    {{ $item->title }}
                                </a>
                                <button @click="open = !open" class="p-2 text-gray-500 hover:text-indigo-600 focus:outline-none">
                                    <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="open" class="pl-4 space-y-1 pb-2">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->url }}" target="{{ $child->target }}" class="block py-1.5 text-xs text-gray-500 hover:text-indigo-600">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url === '#' ? route('blog.index') : $item->url }}" target="{{ $item->target }}" class="block py-2 text-sm text-gray-700 hover:text-indigo-600">
                            {{ $item->title }}
                        </a>
                    @endif
                @endforeach
            @else
                <a href="{{ route('blog.index') }}" class="block py-2 text-sm text-gray-700 hover:text-indigo-600">{{ __('Blog') }}</a>
                @foreach($navCategories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="block py-2 text-sm text-gray-500 hover:text-indigo-600 pl-3">
                        → {{ $cat->name }}
                    </a>
                @endforeach
            @endif
            @if(isset($headerPages) && $headerPages->isNotEmpty())
                @foreach($headerPages as $page)
                    <a href="{{ url('/' . $page->slug) }}" class="block py-2 text-sm text-gray-700 hover:text-indigo-600">
                        {{ $page->title }}
                    </a>
                @endforeach
            @endif

            {{-- Mobile Language Selector --}}
            <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ __('Language') }}</span>
                <div class="grid grid-cols-5 gap-1.5">
                    <a href="?lang=en" class="px-2 py-1.5 text-center text-xs font-semibold rounded-lg border {{ app()->getLocale() === 'en' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-700' }}">🇺🇸 EN</a>
                    <a href="?lang=fr" class="px-2 py-1.5 text-center text-xs font-semibold rounded-lg border {{ app()->getLocale() === 'fr' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-700' }}">🇫🇷 FR</a>
                    <a href="?lang=de" class="px-2 py-1.5 text-center text-xs font-semibold rounded-lg border {{ app()->getLocale() === 'de' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-700' }}">🇩🇪 DE</a>
                    <a href="?lang=hi" class="px-2 py-1.5 text-center text-xs font-semibold rounded-lg border {{ app()->getLocale() === 'hi' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-700' }}">🇮🇳 HI</a>
                    <a href="?lang=te" class="px-2 py-1.5 text-center text-xs font-semibold rounded-lg border {{ app()->getLocale() === 'te' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-700' }}">🇮🇳 TE</a>
                </div>
            </div>
        </div>
    </div>
</header>
