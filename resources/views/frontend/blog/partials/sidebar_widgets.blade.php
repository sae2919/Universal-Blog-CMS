
{{-- Trending Posts --}}
@if($trendingPosts->count())
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-150 dark:border-slate-800 p-6 shadow-sm">
        <h4 class="font-extrabold text-gray-900 dark:text-slate-100 text-lg mb-4 flex items-center gap-2">
            <span class="text-red-500"></span> Trending Articles
        </h4>
        <div class="space-y-4">
            @foreach($trendingPosts as $t)
                <a href="{{ route('blog.show', [$t->category->slug, $t->slug]) }}" class="group flex gap-3 hover:text-indigo-600 transition-colors">
                    @if($t->featured_image)
                        <img src="{{ asset('storage/' . $t->featured_image) }}" alt="{{ $t->title }}"
                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0 border border-gray-100 dark:border-slate-800 group-hover:opacity-90 transition-opacity">
                    @else
                        <div class="w-14 h-14 rounded-lg bg-indigo-50 dark:bg-slate-800 text-indigo-500 font-bold text-[10px] uppercase flex items-center justify-center flex-shrink-0 border border-gray-100 dark:border-slate-800">
                            Blog
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-semibold text-indigo-650 dark:text-indigo-400 uppercase tracking-wider block mb-0.5">{{ $t->category->name }}</span>
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-slate-205 group-hover:text-indigo-650 dark:group-hover:text-indigo-405 line-clamp-2 leading-snug">
                            {{ $t->title }}
                        </h5>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- Related by Category --}}
@if($relatedPosts->count())
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-150 dark:border-slate-800 p-6 shadow-sm">
        <h4 class="font-extrabold text-gray-900 dark:text-slate-100 text-lg mb-4 flex items-center gap-2">
            <span class="text-indigo-500"></span> Related Articles
        </h4>
        <div class="space-y-4">
            @foreach($relatedPosts->take(3) as $r)
                <a href="{{ route('blog.show', [$r->category->slug, $r->slug]) }}" class="group flex gap-3 hover:text-indigo-600 transition-colors">
                    @if($r->featured_image)
                        <img src="{{ asset('storage/' . $r->featured_image) }}" alt="{{ $r->title }}"
                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0 border border-gray-100 dark:border-slate-800 group-hover:opacity-90 transition-opacity">
                    @endif
                    <div class="flex-1 min-w-0">
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-slate-205 group-hover:text-indigo-650 dark:group-hover:text-indigo-405 line-clamp-2 leading-snug">
                            {{ $r->title }}
                        </h5>
                        <p class="text-xs text-gray-400 mt-1">{{ $r->published_at->diffForHumans() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
