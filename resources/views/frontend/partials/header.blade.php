<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if(\App\Models\Setting::getValue('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::getValue('site_logo')) }}"
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
                @if(isset($mainMenu) && $mainMenu && $mainMenu->items->isNotEmpty())
                    @foreach($mainMenu->items as $item)
                        @if($item->children->isNotEmpty())
                            {{-- Dropdown --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false"
                                        class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                                    {{ $item->title }}
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute top-full left-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 py-2 z-50">
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
                               class="text-sm font-medium {{ request()->url() == url($item->url) ? 'text-indigo-650 font-semibold' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                                {{ $item->title }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('home') }}"
                       class="text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                        Home
                    </a>
                    <a href="{{ route('blog.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('blog.*') ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                        Blog
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
                            Categories
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
            </nav>

            {{-- Search & Theme Switcher --}}
            <div class="hidden md:flex items-center gap-4">
                <div class="relative" x-data="{ query: '', results: [], showDropdown: false }">
                    <form action="{{ route('blog.search') }}" method="GET" class="flex items-center">
                        <div class="relative">
                            <input type="text" name="q" placeholder="Search..." autocomplete="off"
                                   x-model="query"
                                   @input.debounce.300ms="
                                       if(query.length >= 2) {
                                           fetch('{{ route('blog.search.autocomplete') }}?q=' + encodeURIComponent(query))
                                               .then(res => res.json())
                                               .then(data => { results = data; showDropdown = true; })
                                       } else {
                                           results = []; showDropdown = false;
                                       }
                                   "
                                   @focus="if(query.length >= 2) showDropdown = true"
                                   @click.outside="showDropdown = false"
                                   class="w-48 pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-all focus:w-64">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>

                    {{-- Autocomplete Dropdown --}}
                    <div x-show="showDropdown && results.length > 0" x-transition
                         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-150 py-2 z-55 divide-y divide-gray-100 max-h-96 overflow-y-auto"
                         style="display: none;">
                        <template x-for="item in results" :key="item.url">
                            <a :href="item.url" class="block px-4 py-3 hover:bg-indigo-50/50 transition-colors">
                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-indigo-600 uppercase">
                                    <span x-text="item.category_emoji"></span>
                                    <span x-text="item.category_name"></span>
                                </div>
                                <div class="text-sm font-semibold text-gray-800 line-clamp-2 mt-0.5" x-text="item.title"></div>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Toggle Theme --}}
                <button @click="
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                " class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Toggle Theme">
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z"/>
                    </svg>
                </button>
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
            @if(isset($mainMenu) && $mainMenu && $mainMenu->items->isNotEmpty())
                @foreach($mainMenu->items as $item)
                    @if($item->children->isNotEmpty())
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center justify-between w-full py-2 text-sm text-gray-700 hover:text-indigo-600">
                                <span>{{ $item->title }}</span>
                                <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" class="pl-4 space-y-1">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->url }}" target="{{ $child->target }}" class="block py-1.5 text-xs text-gray-500 hover:text-indigo-600">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url }}" target="{{ $item->target }}" class="block py-2 text-sm text-gray-700 hover:text-indigo-600">
                            {{ $item->title }}
                        </a>
                    @endif
                @endforeach
            @else
                <a href="{{ route('home') }}" class="block py-2 text-sm text-gray-700 hover:text-indigo-600">Home</a>
                <a href="{{ route('blog.index') }}" class="block py-2 text-sm text-gray-700 hover:text-indigo-600">Blog</a>
                @foreach($navCategories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="block py-2 text-sm text-gray-500 hover:text-indigo-600 pl-3">
                        → {{ $cat->name }}
                    </a>
                @endforeach
            @endif
            <div class="relative pt-2" x-data="{ query: '', results: [], showDropdown: false }">
                <form action="{{ route('blog.search') }}" method="GET">
                    <input type="text" name="q" placeholder="Search..." autocomplete="off"
                           x-model="query"
                           @input.debounce.300ms="
                               if(query.length >= 2) {
                                   fetch('{{ route('blog.search.autocomplete') }}?q=' + encodeURIComponent(query))
                                       .then(res => res.json())
                                       .then(data => { results = data; showDropdown = true; })
                               } else {
                                   results = []; showDropdown = false;
                               }
                           "
                           @focus="if(query.length >= 2) showDropdown = true"
                           @click.outside="showDropdown = false"
                           class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </form>

                {{-- Autocomplete Dropdown (Mobile) --}}
                <div x-show="showDropdown && results.length > 0" x-transition
                     class="absolute left-0 right-0 top-full mt-1 bg-white rounded-lg shadow-lg border border-gray-150 py-2 z-55 divide-y divide-gray-100 max-h-60 overflow-y-auto"
                     style="display: none;">
                    <template x-for="item in results" :key="item.url">
                        <a :href="item.url" class="block px-4 py-2.5 hover:bg-indigo-50/50 transition-colors">
                            <div class="flex items-center gap-1.5 text-[9px] font-bold text-indigo-600 uppercase">
                                <span x-text="item.category_emoji"></span>
                                <span x-text="item.category_name"></span>
                            </div>
                            <div class="text-xs font-semibold text-gray-800 line-clamp-1 mt-0.5" x-text="item.title"></div>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Mobile Theme Toggle --}}
            <div class="pt-4 flex items-center justify-between border-t border-gray-100">
                <span class="text-sm font-medium text-gray-650">Dark Mode</span>
                <button @click="
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                " class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 transition-colors" title="Toggle Theme">
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
