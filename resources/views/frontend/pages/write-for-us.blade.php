@extends('layouts.frontend')

@section('meta_title', $page->meta_title ?? $page->title . ' — ' . \App\Models\Setting::getValue('site_name'))
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<!-- Page Header Banner -->
<div class="relative text-white py-12 px-4 sm:px-6 lg:px-8 text-center overflow-hidden bg-cover bg-center" 
     style="min-height: 240px; display: flex; align-items: center; justify-content: center; {{ $page->featured_image ? 'background-image: linear-gradient(135deg, rgba(30, 27, 75, 0.85), rgba(15, 23, 42, 0.95)), url(\'' . asset('storage/' . $page->featured_image) . '\'); background-blend-mode: multiply;' : 'background: linear-gradient(135deg, #1e1b4b, #0f172a, #0f172a);' }}">
    @if(!$page->featured_image)
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px]"></div>
    @endif
    <div class="relative max-w-4xl mx-auto space-y-4 w-full">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight">
            {{ $page->title }}
        </h1>
        <div class="w-20 h-1.5 mx-auto rounded-full" style="background: linear-gradient(to right, #38bdf8, #6366f1);"></div>
        @if($page->meta_description)
            <p class="max-w-2xl mx-auto text-indigo-200 text-lg font-medium">
                {{ $page->meta_description }}
            </p>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    {{-- Success and Error Alerts --}}
    @if(session('success'))
        <div class="mb-10 p-5 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-300 rounded-2xl flex items-start gap-4 shadow-sm animate-fade-in-down">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-bold text-sm leading-none">{{ __('Submission Successful!') }}</p>
                <p class="text-xs mt-1">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-10 p-5 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900 text-red-800 dark:text-red-300 rounded-2xl flex items-start gap-4 shadow-sm">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-bold text-sm leading-none">{{ __('Please correct the following errors:') }}</p>
                <ul class="list-disc pl-5 mt-2 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
               <!-- Left Column: Editorial Guidelines (Dynamic content from Admin) -->
        <div class="lg:col-span-5 space-y-8">
            @if(!empty(trim(strip_tags($page->content))))
                {!! $page->content !!}
            @else
                <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
                    <h3 class="font-black text-gray-900 dark:text-white text-xl mb-4 tracking-tight border-b border-gray-100 dark:border-slate-800 pb-3">
                        ✍️ Editorial Guidelines
                    </h3>
                    <p class="text-sm text-gray-650 dark:text-slate-300 leading-relaxed mb-6 font-medium">
                        We accept guest articles from technology enthusiasts, developers, and writers. Please read the guidelines carefully before submitting your article:
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5">✓</span>
                            <span class="text-sm text-gray-650 dark:text-slate-350 leading-relaxed font-semibold">
                                <strong>Word Count:</strong> Minimum of 700 to 1,500 words of high-quality copy.
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5">✓</span>
                            <span class="text-sm text-gray-650 dark:text-slate-350 leading-relaxed font-semibold">
                                <strong>Topic Relevance:</strong> Content must focus on Software Development, Artificial Intelligence (AI), Cybersecurity, Blockchain, or emerging tech trends.
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5">✓</span>
                            <span class="text-sm text-gray-650 dark:text-slate-350 leading-relaxed font-semibold">
                                <strong>Original Content:</strong> Must be 100% original, unique, and not published elsewhere.
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5">✓</span>
                            <span class="text-sm text-gray-650 dark:text-slate-350 leading-relaxed font-semibold">
                                <strong>Formatting:</strong> Structure the article with clear H2/H3 subheadings, bullet points, and short paragraphs.
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Prohibited Topics Card -->
                <div class="bg-red-50/50 dark:bg-red-950/5 border border-red-150 dark:border-red-950/30 rounded-3xl p-8 shadow-sm">
                    <h3 class="font-black text-red-800 dark:text-red-400 text-lg mb-4 tracking-tight">
                        🚫 Prohibited Content
                    </h3>
                    <p class="text-xs text-red-750 dark:text-red-300/80 leading-relaxed mb-4 font-semibold">
                        We strictly reject submissions related to:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-red-100/50 dark:bg-red-950/40 text-red-800 dark:text-red-300 text-xs font-bold rounded-lg">Gambling & Casinos</span>
                        <span class="px-3 py-1 bg-red-100/50 dark:bg-red-950/40 text-red-800 dark:text-red-300 text-xs font-bold rounded-lg">Adult Content</span>
                        <span class="px-3 py-1 bg-red-100/50 dark:bg-red-950/40 text-red-800 dark:text-red-300 text-xs font-bold rounded-lg">CBD & Tobacco</span>
                        <span class="px-3 py-1 bg-red-100/50 dark:bg-red-950/40 text-red-800 dark:text-red-300 text-xs font-bold rounded-lg">Derogatory Content</span>
                    </div>
                </div>

                <!-- Contact Box -->
                <div class="bg-gray-50 dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 shadow-sm text-center">
                    <p class="text-sm text-gray-650 dark:text-slate-400 mb-3 font-semibold">
                        Have questions before submitting? Email us directly at:
                    </p>
                    <a href="mailto:writeforustechnology@gmail.com" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline text-lg">
                        writeforustechnology@gmail.com
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Column: Submission Form -->
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-slate-900 border border-gray-150 dark:border-slate-800 rounded-3xl p-8 lg:p-10 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 dark:bg-indigo-950/10 rounded-bl-full -z-10"></div>
                
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-2">
                    Submit Guest Post
                </h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-8">
                    Fill out the form below to submit your article directly to our moderation queue.
                </p>

                <form action="{{ route('guest-post.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Author Name -->
                        <div>
                            <label for="author_name" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Author Name</label>
                            <input type="text" name="author_name" id="author_name" required value="{{ old('author_name') }}"
                                   placeholder="Your Name"
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Author Email -->
                        <div>
                            <label for="author_email" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Author Email</label>
                            <input type="email" name="author_email" id="author_email" required value="{{ old('author_email') }}"
                                   placeholder="yourname@example.com"
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Post Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Article Title</label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                   placeholder="Catchy SEO Title"
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Category Select -->
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Category</label>
                            <select name="category_id" id="category_id" required
                                    class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-750">
                                <option value="" disabled selected>Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->icon_emoji }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Short Summary / Excerpt</label>
                        <textarea name="excerpt" id="excerpt" rows="2" placeholder="Brief summary of the article (1-2 sentences)..."
                                  class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('excerpt') }}</textarea>
                    </div>

                    <!-- Article Content -->
                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Article Content (HTML or plain text)</label>
                        <textarea name="content" id="content" rows="12" required placeholder="Write or paste your complete article draft here..."
                                  class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('content') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Focus Keyword -->
                        <div>
                            <label for="focus_keyword" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Focus Keyword</label>
                            <input type="text" name="focus_keyword" id="focus_keyword" value="{{ old('focus_keyword') }}"
                                   placeholder="e.g. artificial intelligence"
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Meta Description</label>
                            <input type="text" name="meta_description" id="meta_description" value="{{ old('meta_description') }}"
                                   placeholder="Brief snippet for search engine listing..."
                                   class="mt-2 block w-full rounded-xl border-gray-300 dark:border-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div>
                        <label for="featured_image" class="block text-sm font-semibold text-gray-750 dark:text-slate-350">Featured Cover Image (Optional)</label>
                        <input type="file" name="featured_image" id="featured_image" accept="image/*"
                               class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Submit Article
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
