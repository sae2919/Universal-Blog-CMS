<footer class="bg-gray-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            {{-- Brand Column --}}
            <div>
                <h3 class="text-white text-xl font-bold mb-3">
                    {{ \App\Models\Setting::getValue('site_name', config('app.name')) }}
                </h3>
                <p class="text-sm text-gray-400 leading-relaxed mb-5">
                    {{ \App\Models\Setting::getValue('site_tagline', 'Your go-to source for quality content.') }}
                </p>
                {{-- Social Links --}}
                <div class="flex items-center gap-3">
                    @if(\App\Models\Setting::getValue('facebook'))
                        <a href="{{ \App\Models\Setting::getValue('facebook') }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                    @endif
                    @if(\App\Models\Setting::getValue('twitter'))
                        <a href="{{ \App\Models\Setting::getValue('twitter') }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-sky-500 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                        </a>
                    @endif
                    @if(\App\Models\Setting::getValue('instagram'))
                        <a href="{{ \App\Models\Setting::getValue('instagram') }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path fill="none" stroke="currentColor" stroke-width="1.5" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Categories Column --}}
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">{{ __('Categories') }}</h4>
                <ul class="space-y-2">
                    @php
                        $footerCats = \Illuminate\Support\Facades\Cache::remember('footer.categories', now()->addHours(6), function() {
                            return \App\Models\Category::active()->roots()->orderBy('sort_order')->take(6)->get();
                        });
                    @endphp
                    @foreach($footerCats as $cat)
                        <li>
                            <a href="{{ route('blog.category', $cat->slug) }}"
                               class="text-sm text-gray-400 hover:text-white transition-colors">
                                → {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">{{ __('Quick Links') }}</h4>
                <ul class="space-y-2">
                    <li><a href="{{ url('/') }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Blog') }}</a></li>
                    @if(isset($footerPages) && $footerPages->isNotEmpty())
                        @foreach($footerPages as $page)
                            <li>
                                <a href="{{ url('/' . $page->slug) }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                                    {{ $page->title }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-10 pt-8 border-t border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-gray-500">
                &copy; {{ now()->year }} {{ \App\Models\Setting::getValue('site_name') }}. {{ __('All rights reserved.') }}
            </p>
            <a href="/sitemap.xml" class="text-xs text-gray-600 hover:text-gray-400 transition-colors">{{ __('Sitemap') }}</a>
        </div>
    </div>
</footer>
