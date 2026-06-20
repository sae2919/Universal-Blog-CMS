<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Homepage --}}
    <url>
        <loc>{{ route('home') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Blog index --}}
    <url>
        <loc>{{ route('blog.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Pages --}}
    @foreach ($pages as $page)
        <url>
            <loc>{{ route('page.show', $page->slug) }}</loc>
            <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    {{-- Categories --}}
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('blog.category', $category->slug) }}</loc>
            <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    {{-- Tags --}}
    @foreach ($tags as $tag)
        <url>
            <loc>{{ route('blog.tag', $tag->slug) }}</loc>
            <lastmod>{{ $tag->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.5</priority>
        </url>
    @endforeach

    {{-- Posts --}}
    @foreach ($posts as $post)
        <url>
            <loc>{{ route('blog.show', [$post->category->slug, $post->slug]) }}</loc>
            <lastmod>{{ ($post->updated_at ?? $post->published_at)->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
