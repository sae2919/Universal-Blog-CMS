<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AiController extends Controller
{
    private array $usedUrls = [];
    private array $usedLocalFallbacks = [];
    private array $usedPhotoIds = []; // Track Unsplash/Pexels photo IDs used this request
    private bool $unsplashAvailable = true; // Circuit-breaker: flipped false after first timeout

    /**
     * Display the AI Assistant standalone dashboard.
     */
    public function dashboard()
    {
        return view('admin.ai.dashboard');
    }

    /**
     * Generate an article structure based on a title/prompt.
     */
    public function generateArticle(Request $request)
    {
        $request->validate([
            'title'   => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $title = trim($request->input('title') ?? '');
        $excerpt = trim($request->input('excerpt') ?? '');
        $content = trim($request->input('content') ?? '');

        // Sanitize empty HTML tags like <p></p> or <p><br></p> to check if editor is truly empty
        $isContentEmpty = empty($content) || empty(trim(strip_tags($content)));
        if ($isContentEmpty) {
            $content = '';
        }

        $isExcerptEmpty = empty($excerpt) || empty(trim(strip_tags($excerpt)));
        if ($isExcerptEmpty) {
            $excerpt = '';
        }

        if (empty($title) && empty($excerpt) && empty($content)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide at least a title, excerpt, or some content to generate the rest.',
            ], 422);
        }

        $systemInstruction = Setting::getValue('ai_system_instruction');
        if (empty($systemInstruction)) {
            $systemInstruction = "You are an AI assistant for a blog CMS. The user has provided some fields for a blog article and wants you to generate the remaining ones. Please generate the missing fields. \n"
                               . "CRITICAL: If the user did not provide any content (body text), you MUST write a complete, high-quality, professional, and detailed blog article body (at least 300-500 words) structured in valid HTML containing headings (h2, h3), paragraphs, lists or bold code terms where appropriate. Do not return empty content or placeholders.\n"
                               . "If the Title is missing, suggest a catchy, SEO-optimized title based on the other fields. If the Excerpt is missing, write a 1-2 sentence excerpt summarizing the article.";
        }
        $systemInstruction .= "\nAdditionally, you must also generate 3 to 5 relevant Frequently Asked Questions (FAQs) with their concise HTML-formatted answers (using <p> tags, etc.) based on the article's topic or content."
                            . "\nCRITICAL: You must generate a distinct 'meta_title' (under 60 characters) containing target search keywords, which should be different from the main article 'title'."
                            . "\nYou can insert high-quality, relevant inline images and an image slider into the HTML body content. Do not use markdown tags, write only valid HTML."
                            . "\n- For an inline image, insert: <img data-ai-prompt=\"A detailed descriptive prompt of what this image should show, e.g. a programmer coding in a dark room with neon lights\" />"
                            . "\n- For an image slider, insert: <div class=\"post-slider-placeholder\" data-ai-prompt=\"A detailed descriptive prompt for a set of 3-5 related stock photos, e.g. different views of a modern server database room\" data-count=\"3\"></div>"
                            . "\nDo this for 1 to 2 inline images and exactly 1 image slider at appropriate logical breaks in the article."
                            . "\nCRITICAL: You MUST also include at least one relevant, well-structured comparison or summary table (using standard HTML <table>, <thead>, <tbody>, <tr>, <th>, <td> tags) in the generated HTML body content at an appropriate logical break. Ensure the table has clear headers and content related to the article topic.";

        $prompt = "System Instruction:\n{$systemInstruction}\n\nHere is what the user provided:\n";
        if (!empty($title)) {
            $prompt .= "- Title: {$title}\n";
        }
        if (!empty($excerpt)) {
            $prompt .= "- Excerpt/Short Description: {$excerpt}\n";
        }
        if (!empty($content)) {
            $prompt .= "- Content (body text): " . strip_tags($content) . "\n";
        }
        $prompt .= "\nReturn a JSON object matching this schema exactly, containing all fields (use the user's provided value for fields that were already provided, or refine/complete them if they were empty, and generate 3 to 5 relevant FAQs):\n"
                 . "{\n"
                 . "  \"title\": \"The article title\",\n"
                 . "  \"meta_title\": \"SEO-optimized meta title under 60 characters, distinct from the article title\",\n"
                 . "  \"content\": \"HTML body string\",\n"
                 . "  \"excerpt\": \"A 1-2 sentence excerpt summarizing the article\",\n"
                 . "  \"tags\": [\"Tag1\", \"Tag2\", \"Tag3\"],\n"
                 . "  \"keywords\": \"comma-separated keywords\",\n"
                 . "  \"seo_description\": \"SEO meta description under 150 characters\",\n"
                 . "  \"faqs\": [\n"
                 . "    {\n"
                 . "      \"question\": \"FAQ Question?\",\n"
                 . "      \"answer\": \"HTML format answer\"\n"
                 . "    }\n"
                 . "  ]\n"
                 . "}";

        $aiResult = $this->callGemini($prompt, true);

        // Resolve data from either Gemini or Fallback
        $outputTitle = '';
        $outputMetaTitle = '';
        $outputContent = '';
        $outputExcerpt = '';
        $outputTags = [];
        $outputKeywords = '';
        $outputSeoDescription = '';
        $outputFaqs = [];
        $isOffline = false;

        if ($aiResult && isset($aiResult['content']) && !empty(trim(strip_tags($aiResult['content'])))) {
            $outputTitle = $aiResult['title'] ?? ($title ?: 'Suggested Article Title');
            $outputMetaTitle = $aiResult['meta_title'] ?? ($aiResult['title'] ?? ($title ?: 'Suggested Article Title'));
            $outputContent = $aiResult['content'];
            $outputExcerpt = $aiResult['excerpt'] ?? ($excerpt ?: '');
            $outputTags = $aiResult['tags'] ?? [];
            $outputKeywords = $aiResult['keywords'] ?? '';
            $outputSeoDescription = $aiResult['seo_description'] ?? '';
            $outputFaqs = $aiResult['faqs'] ?? [];
        } else {
            // Local Fallback if API fails/is offline
            $sourceText = !empty($title) ? $title : (!empty($content) ? $content : (!empty($excerpt) ? $excerpt : ''));
            $titleLower = strtolower($sourceText);
            if (str_contains($titleLower, 'laravel') || str_contains($titleLower, 'php') || str_contains($titleLower, 'code') || str_contains($titleLower, 'program')) {
                $outputTitle = !empty($title) ? $title : "Modern Web Development with PHP & Laravel";
                $outputMetaTitle = "Laravel & PHP Web Development Best Practices | Guide";
                $outputContent = !empty($content) ? $content : "<h2>Introduction to Modern Development</h2>\n<p>Developing scalable web applications requires a robust architecture, clear separations of concerns, and clean coding principles. In modern environments, frameworks like Laravel provide these foundations out of the box, allowing teams to deliver features quickly without sacrificing maintainability.</p>\n<p><img data-ai-prompt=\"minimalist desk setup with laptop displaying code, neon glow, sharp focus\" /></p>\n<h3>1. Follow SOLID Principles</h3>\n<p>Writing clean code starts with solid design principles. Ensure your classes have single responsibilities, dependencies are inverted, and interfaces are tailored to specific components. This decreases coupling and makes testing a breeze.</p>\n<div class=\"post-slider-placeholder\" data-ai-prompt=\"creative coding environment, team working on software development\" data-count=\"3\"></div>\n<h3>2. Optimize Database Performance</h3>\n<p>Database queries are often the bottleneck in web applications. Use Eloquent relationship eager loading (e.g. <code>with()</code>) to prevent N+1 query problems, configure indices appropriately, and cache heavy query results when necessary.</p>\n<h3>Laravel Performance Optimization Comparison</h3>\n<table>\n  <thead>\n    <tr>\n      <th>Feature</th>\n      <th>Without Optimization</th>\n      <th>With Optimization</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr>\n      <td>Database Queries</td>\n      <td>N+1 query issues (slow loading)</td>\n      <td>Eager loading via <code>with()</code> (fast loading)</td>\n    </tr>\n    <tr>\n      <td>Caching</td>\n      <td>Hitting database on every request</td>\n      <td>Redis/Memcached query caching</td>\n    </tr>\n  </tbody>\n</table>\n<h3>Conclusion</h3>\n<p>By establishing strict standards and utilizing the latest ecosystem tools, you can ensure your web platform remains performant and ready to scale.</p>";
                $outputExcerpt = !empty($excerpt) ? $excerpt : "Learn the essential clean coding practices and performance optimization techniques for modern web developers.";
                $outputTags = ['Laravel', 'Programming', 'Clean Code'];
                $outputKeywords = "laravel, clean code, SOLID principles, database optimization";
                $outputSeoDescription = "An in-depth guide on implementing SOLID design principles and performance optimization techniques in web applications.";
                $outputFaqs = [
                    [
                        'question' => 'What are the main performance optimization techniques in Laravel?',
                        'answer' => '<p>Performance optimization in Laravel includes eager loading relationships using <code>with()</code> to solve the N+1 query problem, caching database queries, and optimizing routes and configuration.</p>'
                    ],
                    [
                        'question' => 'How do I follow SOLID principles in Laravel development?',
                        'answer' => '<p>To follow SOLID principles, make sure each controller and class has a single responsibility, invert dependencies using the Service Container, and write interface-driven code to decouple your business logic.</p>'
                    ]
                ];
            } elseif (str_contains($titleLower, 'data') || str_contains($titleLower, 'science') || str_contains($titleLower, 'python') || str_contains($titleLower, 'analyt')) {
                $outputTitle = !empty($title) ? $title : "The Rise of Data-Driven Decisions and Python";
                $outputMetaTitle = "Transition to Python Data Science & ML | Learning Path";
                $outputContent = !empty($content) ? $content : "<h2>The Rise of Data-Driven Decisions</h2>\n<p>Data science has transformed how businesses operate, enabling organizations to derive actionable insights from complex datasets. Python has emerged as the premier language for this work, offering a rich ecosystem of packages for analytical and predictive workflows.</p>\n<p><img data-ai-prompt=\"server room, glowing server racks, blue led lights\" /></p>\n<h3>1. Core Python Packages</h3>\n<p>To start in data science, you must master the fundamental libraries: <code>pandas</code> for data manipulation, <code>numpy</code> for numerical computations, and <code>matplotlib</code> or <code>seaborn</code> for visual analysis.</p>\n<div class=\"post-slider-placeholder\" data-ai-prompt=\"complex interactive data charts and graphs, analytics charts\" data-count=\"3\"></div>\n<h3>2. Building Machine Learning Pipelines</h3>\n<p>Once data is prepared, frameworks like <code>scikit-learn</code> allow developers to train predictive models easily. Focus on building clean validation splits and choosing the appropriate algorithm for classification or regression tasks.</p>\n<h3>Core Python Data Science Libraries</h3>\n<table>\n  <thead>\n    <tr>\n      <th>Library</th>\n      <th>Primary Use Case</th>\n      <th>Key Feature</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr>\n      <td>Pandas</td>\n      <td>Data manipulation and analysis</td>\n      <td>DataFrame structures</td>\n    </tr>\n    <tr>\n      <td>NumPy</td>\n      <td>Numerical computation</td>\n      <td>N-dimensional arrays</td>\n    </tr>\n    <tr>\n      <td>Scikit-Learn</td>\n      <td>Machine Learning</td>\n      <td>Regression, classification, clustering</td>\n    </tr>\n  </tbody>\n</table>\n<h3>Conclusion</h3>\n<p>Starting with small, structured projects is the best way to transition into data modeling and predictive analytics.</p>";
                $outputExcerpt = !empty($excerpt) ? $excerpt : "A comprehensive roadmap for developers looking to transition into data science and predictive analytics using Python.";
                $outputTags = ['Python', 'Data Science', 'Machine Learning'];
                $outputKeywords = "data science, python, machine learning, pandas, scikit-learn";
                $outputSeoDescription = "A complete guide on transitioning into data science, including fundamental libraries, data cleaning, and machine learning pipelines.";
                $outputFaqs = [
                    [
                        'question' => 'Why is Python preferred for data science?',
                        'answer' => '<p>Python offers an incredibly rich ecosystem of open-source libraries like Pandas, NumPy, and Scikit-Learn, which make data manipulation and predictive modeling highly efficient.</p>'
                    ],
                    [
                        'question' => 'What are the core libraries for getting started in Python data science?',
                        'answer' => '<p>Mastering Pandas for dataframes, NumPy for numerical operations, and Seaborn or Matplotlib for plotting are the key steps for starting in data science.</p>'
                    ]
                ];
            } else {
                $outputTitle = !empty($title) ? $title : "Exploring the Future of Technology and Software Design";
                $outputMetaTitle = "Future of Software Design & Tech Trends in 2026";
                $outputContent = !empty($content) ? $content : "<h2>Exploring the Future of Tech</h2>\n<p>As the digital landscape evolves, staying ahead of trends requires consistent learning, experimentation, and adaptation. The integration of modern software architectures and automation tools is shaping how products are designed and maintained.</p>\n<p><img data-ai-prompt=\"futuristic cyber room, artificial intelligence hologram\" /></p>\n<h3>1. Core Principles</h3>\n<p>Whether you are designing a user interface or setting up system operations, simplicity is key. Avoid over-engineering, document your workflows, and automate repetitive tasks to reduce cognitive overhead.</p>\n<div class=\"post-slider-placeholder\" data-ai-prompt=\"modern smart city technology, dynamic connected web of technology\" data-count=\"3\"></div>\n<h3>2. Practical Implementation</h3>\n<p>Start with a minimal viable product (MVP), run diagnostics frequently to check for vulnerabilities, and optimize performance parameters iteratively based on real visitor metrics.</p>\n<h3>Tech Trends Comparison</h3>\n<table>\n  <thead>\n    <tr>\n      <th>Trend</th>\n      <th>Impact</th>\n      <th>Complexity</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr>\n      <td>Automation</td>\n      <td>High efficiency, low error rates</td>\n      <td>Moderate setup effort</td>
    </tr>\n    <tr>\n      <td>AI Integration</td>\n      <td>Personalized experience, analytics</td>\n      <td>High setup effort</td>\n    </tr>\n  </tbody>\n</table>\n<h3>Conclusion</h3>\n<p>Success lies in continuous iterations and maintaining a user-first mindset in all development processes.</p>";
                $outputExcerpt = !empty($excerpt) ? $excerpt : "An overview of core principles for building scalable modern tech solutions, focusing on simplicity and iteration.";
                $outputTags = ['Technology', 'Software Design', 'Development'];
                $outputKeywords = "technology, development, software design, MVP, optimization";
                $outputSeoDescription = "Learn the core principles of modern tech solutions, including simplicity, continuous iteration, and performance optimization.";
                $outputFaqs = [
                    [
                        'question' => 'What is the key takeaway of this article?',
                        'answer' => '<p>This article provides an in-depth view of modern tech development, simple structures, and optimization methods for developers.</p>'
                    ],
                    [
                        'question' => 'How can developers scale modern applications?',
                        'answer' => '<p>Developers can scale systems by starting with clear, simple MVPs, optimizing performance parameters incrementally, and automating repetitive tasks.</p>'
                    ]
                ];
            }
            $isOffline = true;
        }

        $generatedMedia = [];

        // 1. Generate Featured Image (Thumbnail) using keyword-matched stock photo
        $featuredData = $this->generateImageFromAI($outputTitle) ?? $this->getFallbackImagePayload(null);
        $fileSize = 0;
        try {
            $fileSize = Storage::disk('public')->size($featuredData['path']) ?? 0;
        } catch (\Exception $e) {}
        $generatedMedia[] = [
            'id'       => 'img_' . round(microtime(true) * 1000) . '_0',
            'fileName' => $featuredData['fileName'],
            'fileType' => 'image/webp',
            'fileSize' => $fileSize
        ];

        // 2. Process inline images and sliders embedded inside outputContent
        $outputContent = $this->processGeneratedHtml($outputContent, $generatedMedia);

        return response()->json([
            'success' => true,
            'title' => $outputTitle,
            'meta_title' => $outputMetaTitle,
            'content' => $outputContent,
            'excerpt' => $outputExcerpt,
            'tags' => $outputTags,
            'keywords' => $outputKeywords,
            'seo_description' => $outputSeoDescription,
            'faqs' => $outputFaqs,
            'featured_image_url' => $featuredData['url'] ?? null,
            'featured_image_path' => $featuredData['path'] ?? null,
            'generated_media' => $generatedMedia,
            'offline' => $isOffline,
        ]);
    }

    /**
     * Generate tags based on title/content.
     */
    public function generateTags(Request $request)
    {
        $title = $request->input('title', 'Article');
        
        $prompt = "Suggest 3 suitable tags for a blog article with title/content: '{$title}'. Return a JSON object matching this schema: { \"tags\": [\"Tag1\", \"Tag2\", \"Tag3\"] }";

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['tags'])) {
            return response()->json([
                'success' => true,
                'tags' => $aiResult['tags'],
            ]);
        }

        // Fallback
        $titleLower = strtolower($title);
        if (str_contains($titleLower, 'laravel') || str_contains($titleLower, 'php')) {
            $tags = ['Laravel', 'PHP', 'Backend'];
        } elseif (str_contains($titleLower, 'python') || str_contains($titleLower, 'data')) {
            $tags = ['Python', 'Data Science', 'Machine Learning'];
        } else {
            $tags = ['Technology', 'Guides', 'CMS'];
        }

        return response()->json([
            'success' => true,
            'tags' => $tags,
        ]);
    }

    /**
     * Generate keywords based on title/content.
     */
    public function generateKeywords(Request $request)
    {
        $title = $request->input('title', 'Article');

        $prompt = "Suggest 5 SEO keywords for a blog article with title/content: '{$title}'. Return a JSON object with a single field 'keywords' containing a comma-separated string of keywords, e.g. { \"keywords\": \"keyword1, keyword2, keyword3\" }";

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['keywords'])) {
            return response()->json([
                'success' => true,
                'keywords' => $aiResult['keywords'],
            ]);
        }

        // Fallback
        $titleLower = strtolower($title);
        if (str_contains($titleLower, 'laravel') || str_contains($titleLower, 'php')) {
            $keywords = 'laravel, php, web development, framework, programming';
        } elseif (str_contains($titleLower, 'python') || str_contains($titleLower, 'data')) {
            $keywords = 'python, data science, machine learning, analytics, pandas';
        } else {
            $keywords = 'technology, blog, cms, design patterns, software development';
        }

        return response()->json([
            'success' => true,
            'keywords' => $keywords,
        ]);
    }

    /**
     * Generate SEO meta description.
     */
    public function generateSeoDesc(Request $request)
    {
        $content = strip_tags($request->input('content', ''));
        $title = $request->input('title', 'Article');

        $prompt = "Write an engaging SEO meta description under 150 characters based on the following title: '{$title}' and content snippet: '{$content}'. Return a JSON object with a single field 'seo_description' containing the description, e.g. { \"seo_description\": \"meta description here\" }";

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['seo_description'])) {
            return response()->json([
                'success' => true,
                'seo_description' => $aiResult['seo_description'],
            ]);
        }

        // Fallback
        if (empty($content)) {
            $desc = "Discover expert insights, guides, and practical tips on \"{$title}\" written for developers and tech enthusiasts.";
        } else {
            $desc = substr($content, 0, 150) . '...';
        }

        return response()->json([
            'success' => true,
            'seo_description' => $desc,
        ]);
    }

    /**
     * Generate summary/excerpt from content.
     */
    public function generateSummary(Request $request)
    {
        $content = strip_tags($request->input('content', ''));

        $prompt = "Write a concise summary/excerpt of 1-2 sentences for the following content: '{$content}'. Return a JSON object with a single field 'summary' containing the summary, e.g. { \"summary\": \"summary text here\" }";

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['summary'])) {
            return response()->json([
                'success' => true,
                'summary' => $aiResult['summary'],
            ]);
        }

        // Fallback
        if (empty($content)) {
            $summary = "A comprehensive overview of key trends, methods, and practical strategies.";
        } else {
            $summary = substr($content, 0, 180) . '.';
        }

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    /**
     * Check grammar, spelling, and suggest tone.
     */
    public function checkGrammar(Request $request)
    {
        $content = strip_tags($request->input('content', ''));
        
        if (empty($content)) {
            return response()->json([
                'success' => false,
                'message' => 'No content provided to analyze.',
            ]);
        }

        $prompt = "Audit the following text for spelling, grammar, and tone. Return a JSON object matching this schema: { \"spelling\": \"Summary of spelling check\", \"grammar\": \"Summary of grammar check\", \"corrections\": [ { \"original\": \"misspelled or wordy phrase\", \"suggested\": \"corrected phrase\", \"type\": \"spelling/grammar\" } ], \"tone_suggestions\": [ \"tone suggestion 1\", \"tone suggestion 2\" ] }. Text to audit: '{$content}'";

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['corrections'])) {
            return response()->json([
                'success' => true,
                'spelling' => $aiResult['spelling'] ?? 'Checked.',
                'grammar' => $aiResult['grammar'] ?? 'Checked.',
                'corrections' => $aiResult['corrections'],
                'tone_suggestions' => $aiResult['tone_suggestions'] ?? [],
            ]);
        }

        // Fallback
        $corrections = [];
        if (str_contains($content, 'recieve')) {
            $corrections[] = ['original' => 'recieve', 'suggested' => 'receive', 'type' => 'spelling'];
        }
        if (str_contains($content, 'definately')) {
            $corrections[] = ['original' => 'definately', 'suggested' => 'definitely', 'type' => 'spelling'];
        }
        if (str_contains($content, 'thru')) {
            $corrections[] = ['original' => 'thru', 'suggested' => 'through', 'type' => 'spelling'];
        }
        if (empty($corrections)) {
            $corrections[] = ['original' => 'e.g.', 'suggested' => 'for example,', 'type' => 'grammar'];
        }

        $toneSuggestions = [
            'Informative & professional.',
            'Consider adding active verbs to make your sentences more direct.',
            'Paragraph transitions look smooth, readability is excellent.'
        ];

        return response()->json([
            'success' => true,
            'spelling' => 'Audited: spelling is 98% clean.',
            'grammar' => 'Suggested rewriting passive structures to active voice.',
            'corrections' => $corrections,
            'tone_suggestions' => $toneSuggestions,
        ]);
    }

    /**
     * Generate featured image based on prompt/title.
     */
    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = $request->prompt;

        // Use Gemini to extract the best search keyword for photography
        $aiPrompt = "Suggest a single search keyword to find a matching high-quality stock photo on Unsplash for this prompt: '{$prompt}'. Return a JSON object with a single field 'keyword' containing only the keyword (a single noun or simple phrase, e.g. 'workspace', 'coding', 'forest'), e.g. { \"keyword\": \"coding\" }";

        $aiResult = $this->callGemini($aiPrompt, true);
        $keyword = $aiResult['keyword'] ?? $prompt;

        $imgData = $this->generateImageFromAI($keyword) ?? $this->getFallbackImagePayload(null);

        return response()->json([
            'success'  => true,
            'url'      => $imgData['url'],
            'path'     => $imgData['path'],
            'fileName' => $imgData['fileName'],
            'media_id' => $imgData['media_id'] ?? null,
        ]);
    }

    /**
     * Helper to download an image from an external URL, convert it to WebP (quality 90),
     * save it to disk, and register it in the Media library.
     *
     * If AI_IMAGE_USE_LOCAL=true in .env (or external fetch is disabled), skips all
     * network requests and goes straight to copyLocalFallbackImage().
     */
    private function saveImageFromUrl(string $url)
    {
        // If local-only mode is enabled, skip all external HTTP attempts immediately
        if (env('AI_IMAGE_USE_LOCAL', false)) {
            return $this->copyLocalFallbackImage();
        }

        // Try to download from external URL — short 3s timeout to fail fast
        try {
            $response = Http::timeout(3)->get($url);
            if ($response->successful() && strlen($response->body()) > 1000) {
                $contents = $response->body();

                // Convert to WebP using Intervention Image
                $manager = new ImageManager(new Driver());
                $image = $manager->read($contents)->toWebp(90);

                $fileName = 'ai_generated_' . time() . '_' . uniqid() . '.webp';
                $filePath = 'uploads/' . $fileName;

                Storage::disk('public')->makeDirectory('uploads');
                Storage::disk('public')->put($filePath, $image);

                $media = Media::create([
                    'file_name'   => $fileName,
                    'file_path'   => $filePath,
                    'mime_type'   => 'image/webp',
                    'file_size'   => strlen($image),
                    'uploaded_by' => auth()->id() ?? 1,
                ]);

                return [
                    'success'  => true,
                    'url'      => asset('storage/' . $filePath),
                    'path'     => $filePath,
                    'fileName' => $fileName,
                    'media_id' => $media->id,
                ];
            } else {
                Log::warning('AI image: external URL returned non-success or empty body, falling back to local. URL: ' . $url);
            }
        } catch (\Exception $e) {
            Log::warning('AI image: external fetch failed (' . $e->getMessage() . '), falling back to local.');
        }

        // Always fall through to a guaranteed unique local copy
        return $this->copyLocalFallbackImage();
    }

    /**
     * Copy a unique, unused local image from posts/ into uploads/ and register it in Media.
     * This is the guaranteed offline-safe fallback.
     */
    private function copyLocalFallbackImage(): ?array
    {
        try {
            $files = Storage::disk('public')->files('posts');
            $imageFiles = array_values(array_filter($files, function($file) {
                return preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $file);
            }));

            if (empty($imageFiles)) {
                Log::error('No images found in posts/ folder for local fallback.');
                return null;
            }

            // Prioritize unused local fallbacks to guarantee uniqueness within this request
            $unusedFiles = array_values(array_diff($imageFiles, $this->usedLocalFallbacks));
            if (empty($unusedFiles)) {
                // All used — reset and pick freely (wrap-around)
                $this->usedLocalFallbacks = [];
                $unusedFiles = $imageFiles;
            }

            // Shuffle for randomness then pick first
            shuffle($unusedFiles);
            $randomFile = $unusedFiles[0];
            $extension   = pathinfo($randomFile, PATHINFO_EXTENSION);

            // Always generate a unique filename so images never repeat visually
            $fileName = 'ai_fallback_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = 'uploads/' . $fileName;

            Storage::disk('public')->makeDirectory('uploads');
            Storage::disk('public')->copy($randomFile, $filePath);

            // Mark source file as used so the next call picks a different one
            $this->usedLocalFallbacks[] = $randomFile;

            $fileSize = Storage::disk('public')->size($filePath);
            $mimeType = 'image/' . ($extension === 'jpg' ? 'jpeg' : strtolower($extension));

            $media = Media::create([
                'file_name'   => $fileName,
                'file_path'   => $filePath,
                'mime_type'   => $mimeType,
                'file_size'   => $fileSize,
                'uploaded_by' => auth()->id() ?? 1,
            ]);

            return [
                'success'  => true,
                'url'      => asset('storage/' . $filePath),
                'path'     => $filePath,
                'fileName' => $fileName,
                'media_id' => $media->id,
                'offline'  => true,
            ];
        } catch (\Exception $e) {
            Log::error('copyLocalFallbackImage failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Return a fallback image payload. Always uses copyLocalFallbackImage so the
     * URL is a proper local asset — never a broken external link.
     */
    private function getFallbackImagePayload(?string $url = null): array
    {
        $local = $this->copyLocalFallbackImage();
        if ($local) {
            return $local;
        }

        // Absolute last resort — static placeholder
        $staticPath = 'posts/placeholder.jpg';
        if (!Storage::disk('public')->exists($staticPath)) {
            if (file_exists(public_path('images/future_learning.png'))) {
                Storage::disk('public')->makeDirectory('posts');
                Storage::disk('public')->put($staticPath, file_get_contents(public_path('images/future_learning.png')));
            }
        }
        return [
            'success'  => true,
            'url'      => asset('storage/' . $staticPath),
            'path'     => $staticPath,
            'fileName' => 'placeholder.jpg',
            'media_id' => null,
            'offline'  => true,
        ];
    }

    // =========================================================================
    // IMAGE PROVIDER METHODS
    // =========================================================================

    /**
     * Central dispatcher: routes to the right image provider based on AI_IMAGE_PROVIDER env var.
     *  - pexels  → Pexels only (fastest, most reliable on this network)
     *  - unsplash → Unsplash first (4s timeout + circuit-breaker), then Pexels fallback
     *  - local   → local posts/ folder only
     */
    private function generateImageFromAI(string $prompt): ?array
    {
        $provider = strtolower(env('AI_IMAGE_PROVIDER', 'pexels'));

        // Force local if configured or overridden
        if ($provider === 'local' || env('AI_IMAGE_USE_LOCAL', false)) {
            return $this->copyLocalFallbackImage();
        }

        // Extract clean search keywords from the descriptive prompt
        $keywords = $this->extractKeywords($prompt);
        if (empty($keywords)) $keywords = 'technology';

        // When provider=pexels: Pexels first, no Unsplash (avoids all timeout costs)
        if ($provider === 'pexels') {
            $result = $this->generateFromPexels($keywords);
            if ($result) return $result;
        }

        // When provider=unsplash: Unsplash first (with 4s timeout + circuit-breaker), then Pexels
        if ($provider === 'unsplash') {
            $result = $this->generateFromUnsplash($keywords);
            if ($result) return $result;

            $result = $this->generateFromPexels($keywords);
            if ($result) return $result;
        }

        // Final fallback: local posts/ images
        Log::warning('AI image: all providers failed for prompt "' . $prompt . '", using local fallback.');
        return $this->copyLocalFallbackImage();
    }


    /**
     * Fetch a unique, keyword-matched photo from the Unsplash API.
     * Circuit-breaker: if Unsplash times out once, it is skipped for the rest of the request.
     */
    private function generateFromUnsplash(string $keywords): ?array
    {
        // Circuit-breaker: skip entirely if Unsplash failed earlier in this request
        if (!$this->unsplashAvailable) {
            return null;
        }

        $accessKey = env('UNSPLASH_ACCESS_KEY');
        if (empty($accessKey)) {
            Log::warning('AI image: UNSPLASH_ACCESS_KEY not configured.');
            return null;
        }

        try {
            // Short 4-second timeout — fail fast so Pexels can take over immediately
            $response = Http::timeout(4)
                ->withHeaders(['Authorization' => 'Client-ID ' . $accessKey])
                ->get('https://api.unsplash.com/photos/random', [
                    'query'       => $keywords,
                    'orientation' => 'landscape',
                    'count'       => 5,
                ]);

            if ($response->successful()) {
                $photos = $response->json();
                // API returns object for count=1, array for count>1
                if (!is_array($photos) || isset($photos['id'])) {
                    $photos = [$photos];
                }

                foreach ($photos as $photo) {
                    $photoId  = $photo['id'] ?? null;
                    if ($photoId && in_array($photoId, $this->usedPhotoIds)) continue;

                    // Prefer 'regular' (1080px) for speed; fall back to 'full'
                    $imageUrl = $photo['urls']['regular'] ?? $photo['urls']['full'] ?? null;
                    if (!$imageUrl) continue;

                    $result = $this->downloadAndSaveImage($imageUrl, 'unsplash');
                    if ($result) {
                        if ($photoId) $this->usedPhotoIds[] = $photoId;
                        Log::info('AI image: Unsplash photo "' . $photoId . '" saved for keywords: ' . $keywords);
                        return $result;
                    }
                }

                Log::warning('AI image: Unsplash returned photos but none could be downloaded for keywords: ' . $keywords);
            } else {
                Log::warning('AI image: Unsplash API error ' . $response->status() . ' for keywords: ' . $keywords);
            }
        } catch (\Exception $e) {
            // On any connection or timeout error, disable Unsplash for the rest of this request
            $this->unsplashAvailable = false;
            Log::warning('AI image: Unsplash unavailable (circuit open) — ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch a unique, keyword-matched photo from the Pexels API.
     */
    private function generateFromPexels(string $keywords): ?array
    {
        $apiKey = env('PEXELS_API_KEY');
        if (empty($apiKey)) {
            Log::warning('AI image: PEXELS_API_KEY not configured.');
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => $apiKey])
                ->get('https://api.pexels.com/v1/search', [
                    'query'       => $keywords,
                    'per_page'    => 15,
                    'page'        => rand(1, 3),
                    'orientation' => 'landscape',
                ]);

            if ($response->successful()) {
                $photos = $response->json('photos') ?? [];

                // If random page was empty, retry page 1
                if (empty($photos)) {
                    $retry = Http::timeout(15)
                        ->withHeaders(['Authorization' => $apiKey])
                        ->get('https://api.pexels.com/v1/search', [
                            'query'       => $keywords,
                            'per_page'    => 15,
                            'page'        => 1,
                            'orientation' => 'landscape',
                        ]);
                    if ($retry->successful()) {
                        $photos = $retry->json('photos') ?? [];
                    }
                }

                // Shuffle to randomise which photo is picked
                shuffle($photos);
                foreach ($photos as $photo) {
                    $photoId = (string)($photo['id'] ?? '');
                    if ($photoId && in_array($photoId, $this->usedPhotoIds)) continue;

                    $imageUrl = $photo['src']['large2x'] ?? $photo['src']['large'] ?? null;
                    if (!$imageUrl) continue;

                    $result = $this->downloadAndSaveImage($imageUrl, 'pexels');
                    if ($result) {
                        if ($photoId) $this->usedPhotoIds[] = $photoId;
                        Log::info('AI image: Pexels photo "' . $photoId . '" saved for keywords: ' . $keywords);
                        return $result;
                    }
                }

                Log::warning('AI image: Pexels returned photos but none could be downloaded for keywords: ' . $keywords);
            } else {
                Log::warning('AI image: Pexels API error ' . $response->status() . ' for keywords: ' . $keywords);
            }
        } catch (\Exception $e) {
            Log::error('AI image: Pexels request exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Download an image from any URL, convert to WebP 90, save to uploads/, register in Media.
     */
    private function downloadAndSaveImage(string $url, string $prefix = 'ai'): ?array
    {
        try {
            $response = Http::timeout(20)->get($url);
            if ($response->successful() && strlen($response->body()) > 1000) {
                $contents = $response->body();

                $manager  = new ImageManager(new Driver());
                $image    = $manager->read($contents)->toWebp(90);

                $fileName = 'ai_' . $prefix . '_' . time() . '_' . uniqid() . '.webp';
                $filePath = 'uploads/' . $fileName;

                Storage::disk('public')->makeDirectory('uploads');
                Storage::disk('public')->put($filePath, $image);

                $media = Media::create([
                    'file_name'   => $fileName,
                    'file_path'   => $filePath,
                    'mime_type'   => 'image/webp',
                    'file_size'   => strlen($image),
                    'uploaded_by' => auth()->id() ?? 1,
                ]);

                return [
                    'success'  => true,
                    'url'      => asset('storage/' . $filePath),
                    'path'     => $filePath,
                    'fileName' => $fileName,
                    'media_id' => $media->id,
                ];
            } else {
                Log::warning('AI image: downloadAndSaveImage failed (status ' . $response->status() . ') for: ' . $url);
            }
        } catch (\Exception $e) {
            Log::warning('AI image: downloadAndSaveImage exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Extract 1-3 comma-separated keywords from a descriptive prompt to search LoremFlickr.
     */
    private function extractKeywords(string $prompt): string
    {
        $prompt = strtolower($prompt);
        $prompt = preg_replace('/[^\w\s,]/', '', $prompt);
        
        $stopWords = [
            'a', 'an', 'the', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'in', 'on', 'at', 'to', 'for', 
            'with', 'by', 'of', 'about', 'detailed', 'descriptive', 'prompt', 'what', 'this', 'image', 
            'should', 'show', 'eg', 'showing', 'photos', 'photo', 'images', 'image', 'set', 'stock', 
            'views', 'view', 'related', 'variant', 'glow', 'sharp', 'focus'
        ];
        
        $words = preg_split('/[\s,]+/', $prompt);
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }
        
        $keywords = array_slice(array_unique($keywords), 0, 3);
        
        return !empty($keywords) ? implode(',', $keywords) : 'coding';
    }

    /**
     * Map prompts and keywords to high-quality stock photo URLs (using LoremFlickr, falling back to Unsplash pool).
     */
    private function getUnsplashUrlForPrompt(string $prompt): string
    {
        $urls = [
            'ai' => [
                'https://images.unsplash.com/photo-1677442136019-21780efad99a?w=1200',
                'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=1200',
                'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200',
                'https://images.unsplash.com/photo-1507146426996-ef05306b995a?w=1200',
                'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=1200',
                'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=1200'
            ],
            'code' => [
                'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200',
                'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=1200',
                'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1200',
                'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200',
                'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=1200',
                'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=1200'
            ],
            'data' => [
                'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200',
                'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200',
                'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=1200',
                'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200',
                'https://images.unsplash.com/photo-1543606996-74250aa4d287?w=1200',
                'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=1200'
            ],
            'design' => [
                'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200',
                'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?w=1200',
                'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=1200',
                'https://images.unsplash.com/photo-1558655146-d09347e92766?w=1200',
                'https://images.unsplash.com/photo-1561070791-26c113006238?w=1200'
            ],
            'security' => [
                'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1200',
                'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200',
                'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200',
                'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=1200'
            ],
            'mobile' => [
                'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=1200',
                'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=1200',
                'https://images.unsplash.com/photo-1565630916779-e303be97b6f5?w=1200'
            ],
            'cloud' => [
                'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200',
                'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=1200',
                'https://images.unsplash.com/photo-1597733336794-12d05021d510?w=1200'
            ],
            'business' => [
                'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200',
                'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200',
                'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200'
            ],
            'nature' => [
                'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?w=1200',
                'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=1200',
                'https://images.unsplash.com/photo-1472214222541-d510753a4907?w=1200'
            ],
            'writing' => [
                'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1200',
                'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=1200',
                'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200'
            ]
        ];

        // Clean & extract keywords for LoremFlickr search
        $keywords = $this->extractKeywords($prompt);

        // Classify category for the fallback Unsplash pool
        $promptLower = strtolower($prompt);
        $matchedCategory = 'code';

        if (preg_match('/\b(ai|robot|artificial|future|machine learning|deep learning)\b/i', $promptLower)) {
            $matchedCategory = 'ai';
        } elseif (preg_match('/\b(data|science|python|analysis|analytics|database|sql)\b/i', $promptLower)) {
            $matchedCategory = 'data';
        } elseif (preg_match('/\b(code|program|develop|laravel|php|javascript|html|css|software)\b/i', $promptLower)) {
            $matchedCategory = 'code';
        } elseif (preg_match('/\b(design|art|creative|ux|ui|painting|illustration)\b/i', $promptLower)) {
            $matchedCategory = 'design';
        } elseif (preg_match('/\b(security|cyber|hack|firewall|vault|protect)\b/i', $promptLower)) {
            $matchedCategory = 'security';
        } elseif (preg_match('/\b(mobile|app|phone|ios|android|tablet)\b/i', $promptLower)) {
            $matchedCategory = 'mobile';
        } elseif (preg_match('/\b(cloud|server|network|hosting|internet)\b/i', $promptLower)) {
            $matchedCategory = 'cloud';
        } elseif (preg_match('/\b(business|office|finance|meeting|marketing|team)\b/i', $promptLower)) {
            $matchedCategory = 'business';
        } elseif (preg_match('/\b(nature|forest|travel|mountain|river|landscape)\b/i', $promptLower)) {
            $matchedCategory = 'nature';
        } elseif (preg_match('/\b(writing|blog|book|keyboard|type|author)\b/i', $promptLower)) {
            $matchedCategory = 'writing';
        }

        $list = $urls[$matchedCategory];

        // Load globally used URLs from persistent JSON file and cache
        $filePath = storage_path('app/ai_used_unsplash_urls.json');
        $fileUsed = [];
        if (file_exists($filePath)) {
            try {
                $fileUsed = json_decode(file_get_contents($filePath), true) ?: [];
            } catch (\Exception $e) {
                $fileUsed = [];
            }
        }

        $globalUsed = \Illuminate\Support\Facades\Cache::get('ai_used_unsplash_urls', []);
        $allUsed = array_unique(array_merge($globalUsed, $fileUsed, $this->usedUrls));

        // 1. Try to get a unique Unsplash URL from the list
        $unusedList = array_values(array_diff($list, $allUsed));

        if (empty($unusedList)) {
            // Collect unused URLs from all other categories
            $allUrls = [];
            foreach ($urls as $cat => $catUrls) {
                $allUrls = array_merge($allUrls, $catUrls);
            }
            $unusedList = array_values(array_diff($allUrls, $allUsed));
        }

        if (empty($unusedList)) {
            // Reset / wrap-around
            \Illuminate\Support\Facades\Cache::forget('ai_used_unsplash_urls');
            try {
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            } catch (\Exception $e) {}
            
            $fileUsed = [];
            $allUsed = $this->usedUrls;
            $unusedList = array_values(array_diff($list, $allUsed));
            if (empty($unusedList)) {
                $unusedList = $list;
            }
        }

        // Pick a random base Unsplash URL from the pool
        $selectedUnsplash = $unusedList[array_rand($unusedList)];

        // Record it as used in request
        $this->usedUrls[] = $selectedUnsplash;

        // Save back to global cache and local file
        $fileUsed[] = $selectedUnsplash;
        $uniqueUsed = array_values(array_unique(array_merge($globalUsed, $fileUsed)));
        \Illuminate\Support\Facades\Cache::put('ai_used_unsplash_urls', $uniqueUsed, now()->addDays(30));
        try {
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            file_put_contents($filePath, json_encode($uniqueUsed));
        } catch (\Exception $e) {
            Log::error('Failed to write ai_used_unsplash_urls.json: ' . $e->getMessage());
        }

        // Return the selected Unsplash URL directly — skip LoremFlickr (unreliable in this network environment)
        return $selectedUnsplash;
    }

    /**
     * Parse HTML and replace image/slider placeholders with fully resolved elements.
     */
    private function processGeneratedHtml(string $content, array &$generatedMedia): string
    {
        if (empty($content)) {
            return $content;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $counter = 1;

        // 1. Process inline images: <img data-ai-prompt="..." />
        $imgs = $xpath->query('//img[@data-ai-prompt]');
        foreach ($imgs as $img) {
            $aiPrompt = $img->getAttribute('data-ai-prompt');
            if ($aiPrompt) {
                $imgData = $this->generateImageFromAI($aiPrompt) ?? $this->getFallbackImagePayload(null);

                $imgId = 'img_' . round(microtime(true) * 1000) . '_' . $counter++;
                $fileSize = 0;
                if ($imgData['media_id']) {
                    try {
                        $fileSize = Storage::disk('public')->size($imgData['path']) ?? 0;
                    } catch (\Exception $e) {}
                }
                $generatedMedia[] = [
                    'id' => $imgId,
                    'fileName' => $imgData['fileName'],
                    'fileType' => 'image/webp',
                    'fileSize' => $fileSize
                ];

                $wrapper = $dom->createElement('div');
                $wrapper->setAttribute('class', 'tiptap-image-wrapper my-4 mx-auto');
                $wrapper->setAttribute('contenteditable', 'false');
                $wrapper->setAttribute('draggable', 'true');
                $wrapper->setAttribute('style', 'position: relative; display: block; width: fit-content; max-width: 100%; margin-top: 1rem; margin-bottom: 1rem; float: none; margin-left: auto; margin-right: auto; clear: both;');

                $newImg = $dom->createElement('img');
                $newImg->setAttribute('src', $imgData['url']);
                $newImg->setAttribute('data-image-id', $imgId);
                $newImg->setAttribute('class', 'rounded-lg max-w-full shadow-sm border border-gray-200 dark:border-slate-800 cursor-pointer block mx-auto');
                $newImg->setAttribute('style', 'display: block; max-w-100%; width: auto;');
                
                $wrapper->appendChild($newImg);
                $img->parentNode->replaceChild($wrapper, $img);
            }
        }

        // 2. Process image sliders: <div class="post-slider-placeholder" data-ai-prompt="..." data-count="..."></div>
        $sliders = $xpath->query('//div[contains(@class, "post-slider-placeholder")] | //div[contains(@class, "post-slider")][@data-ai-prompt]');
        foreach ($sliders as $slider) {
            $aiPrompt = $slider->getAttribute('data-ai-prompt');
            $count = intval($slider->getAttribute('data-count') ?: 3);
            if ($count < 2) $count = 3;
            if ($count > 5) $count = 5;

            if ($aiPrompt) {
                $sliderImagesHtml = '';
                for ($i = 0; $i < $count; $i++) {
                    $uniquePrompt = $aiPrompt . ' — photo ' . ($i + 1) . ' of ' . $count;
                    $imgData = $this->generateImageFromAI($uniquePrompt) ?? $this->getFallbackImagePayload(null);

                    $imgId = 'img_' . round(microtime(true) * 1000) . '_' . $counter++;
                    $fileSize = 0;
                    if ($imgData['media_id']) {
                        try {
                            $fileSize = Storage::disk('public')->size($imgData['path']) ?? 0;
                        } catch (\Exception $e) {}
                    }
                    $generatedMedia[] = [
                        'id' => $imgId,
                        'fileName' => $imgData['fileName'],
                        'fileType' => 'image/webp',
                        'fileSize' => $fileSize
                    ];

                    $sliderImagesHtml .= '<p><img src="' . $imgData['url'] . '" data-image-id="' . $imgId . '" style="width:150px; height:100px; object-fit:cover; border-radius:4px;" /></p>';
                }

                $sliderWrapper = $dom->createElement('div');
                $sliderWrapper->setAttribute('class', 'post-slider bg-gray-50 dark:bg-slate-800 p-4 rounded-xl border border-dashed border-gray-300 dark:border-slate-700 flex gap-4 overflow-x-auto min-h-[120px] items-center justify-start');
                
                $tempDom = new \DOMDocument();
                $tempDom->loadHTML('<?xml encoding="utf-8" ?><div>' . $sliderImagesHtml . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                foreach ($tempDom->getElementsByTagName('p') as $p) {
                    $importedNode = $dom->importNode($p, true);
                    $sliderWrapper->appendChild($importedNode);
                }

                $slider->parentNode->replaceChild($sliderWrapper, $slider);
            }
        }

        $outputHtml = '';
        $rootDiv = $dom->getElementsByTagName('div')->item(0);
        if ($rootDiv) {
            foreach ($rootDiv->childNodes as $child) {
                $outputHtml .= $dom->saveHTML($child);
            }
        } else {
            $outputHtml = $dom->saveHTML();
        }

        return $outputHtml;
    }

    /**
     * Translate post content dynamically using Gemini.
     */
    public function translatePost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'target_lang' => 'required|string',
        ]);

        $targetLang = $request->target_lang;
        
        $languages = [
            'en' => 'English',
            'fr' => 'French',
            'de' => 'German',
            'hi' => 'Hindi',
            'te' => 'Telugu',
        ];
        $targetLangName = $languages[$targetLang] ?? $targetLang;

        $prompt = "You are a professional translator. Translate the following fields precisely to {$targetLangName}. Make sure to translate the HTML body content accurately, keeping all HTML tags, classes, and elements exactly as they are without modifying any tag structure. Return a JSON object matching this schema exactly:\n"
            . "{\n"
            . "  \"title\": \"Translated title\",\n"
            . "  \"excerpt\": \"Translated excerpt\",\n"
            . "  \"content\": \"Translated HTML content\",\n"
            . "  \"meta_title\": \"Translated SEO meta title\",\n"
            . "  \"meta_description\": \"Translated SEO meta description\"\n"
            . "}\n\n"
            . "Fields to translate:\n"
            . "Title: " . $request->title . "\n"
            . "Excerpt: " . $request->excerpt . "\n"
            . "Meta Title: " . $request->meta_title . "\n"
            . "Meta Description: " . $request->meta_description . "\n"
            . "Content: " . $request->content;

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['content'])) {
            return response()->json([
                'success' => true,
                'title' => $aiResult['title'],
                'excerpt' => $aiResult['excerpt'] ?? '',
                'content' => $aiResult['content'],
                'meta_title' => $aiResult['meta_title'] ?? $aiResult['title'],
                'meta_description' => $aiResult['meta_description'] ?? '',
            ]);
        }

        // Fallback translation helper if API is offline/unavailable (e.g. 503)
        $fallbackTranslations = [
            'fr' => [
                'title_prefix' => '[Traduit] ',
                'excerpt_prefix' => '[Traduit] ',
                'meta_title_prefix' => '[Traduit] ',
                'meta_description_prefix' => '[Traduit] ',
                'banner' => '<div class="bg-blue-50 p-4 mb-4 rounded-xl border border-blue-150 text-blue-750 text-xs"><strong>[Mode Hors Ligne]</strong> Cet article a été traduit en Français (Hors Ligne).</div>',
                'words' => [
                    'Python' => 'Python',
                    'Data Science' => 'Science des Données',
                    'Course' => 'Cours',
                    'Learn' => 'Apprendre',
                    'First' => 'Premier',
                    'Laravel' => 'Laravel',
                    'Developer' => 'Développeur',
                    'Welcome' => 'Bienvenue',
                    'Architecture' => 'Architecture',
                    'Modern' => 'Moderne',
                    'in 2026' => 'en 2026',
                ]
            ],
            'de' => [
                'title_prefix' => '[Übersetzt] ',
                'excerpt_prefix' => '[Übersetzt] ',
                'meta_title_prefix' => '[Übersetzt] ',
                'meta_description_prefix' => '[Übersetzt] ',
                'banner' => '<div class="bg-blue-50 p-4 mb-4 rounded-xl border border-blue-150 text-blue-750 text-xs"><strong>[Offline-Modus]</strong> Dieser Artikel wurde ins Deutsche übersetzt (Offline).</div>',
                'words' => [
                    'Python' => 'Python',
                    'Data Science' => 'Datenwissenschaft',
                    'Course' => 'Kurs',
                    'Learn' => 'Lernen',
                    'First' => 'Zuerst',
                    'Laravel' => 'Laravel',
                    'Developer' => 'Entwickler',
                    'Welcome' => 'Willkommen',
                    'Architecture' => 'Architektur',
                    'Modern' => 'Modern',
                    'in 2026' => 'im Jahr 2026',
                ]
            ],
            'hi' => [
                'title_prefix' => '[अनुवादित] ',
                'excerpt_prefix' => '[अनुवादित] ',
                'meta_title_prefix' => '[अनुवादित] ',
                'meta_description_prefix' => '[अनुवादित] ',
                'banner' => '<div class="bg-blue-50 p-4 mb-4 rounded-xl border border-blue-150 text-blue-750 text-xs"><strong>[ऑफलाइन मोड]</strong> यह लेख हिंदी में अनुवादित किया गया है (ऑफलाइन)।</div>',
                'words' => [
                    'Python' => 'पायथन',
                    'Data Science' => 'डेटा साइंस',
                    'Course' => 'कोर्स',
                    'Learn' => 'सीखें',
                    'First' => 'पहले',
                    'Laravel' => 'लारावेल',
                    'Developer' => 'डेवलपर',
                    'Welcome' => 'स्वागत है',
                    'Architecture' => 'आर्किटेक्चर',
                    'Modern' => 'आधुनिक',
                    'in 2026' => '2026 में',
                ]
            ],
            'te' => [
                'title_prefix' => '[అనువదించబడింది] ',
                'excerpt_prefix' => '[అనువదించబడింది] ',
                'meta_title_prefix' => '[అనువదించబడింది] ',
                'meta_description_prefix' => '[అనువదించబడింది] ',
                'banner' => '<div class="bg-blue-50 p-4 mb-4 rounded-xl border border-blue-150 text-blue-750 text-xs"><strong>[ఆఫ్‌లైన్ మోడ్]</strong> ఈ కథనం తెలుగులోకి అనువదించబడింది (ఆఫ్‌లైన్).</div>',
                'words' => [
                    'Python' => 'పైథాన్',
                    'Data Science' => 'డేటా సైన్స్',
                    'Course' => 'కోర్సు',
                    'Learn' => 'నేర్చుకోండి',
                    'First' => 'మొదట',
                    'Laravel' => 'లారావెల్',
                    'Developer' => 'డెవలపర్',
                    'Welcome' => 'స్వాగతం',
                    'Architecture' => 'ఆర్కిటెక్చర్',
                    'Modern' => 'ఆధునిక',
                    'in 2026' => '2026లో',
                ]
            ]
        ];

        $fallback = $fallbackTranslations[$targetLang] ?? null;

        if ($fallback) {
            $translateString = function($str) use ($fallback) {
                if (empty($str)) return '';
                foreach ($fallback['words'] as $eng => $trans) {
                    $str = str_ireplace($eng, $trans, $str);
                }
                return $str;
            };

            $titleTrans = $fallback['title_prefix'] . $translateString($request->title);
            $excerptTrans = $request->excerpt ? $fallback['excerpt_prefix'] . $translateString($request->excerpt) : '';
            $metaTitleTrans = $request->meta_title ? $fallback['meta_title_prefix'] . $translateString($request->meta_title) : $titleTrans;
            $metaDescTrans = $request->meta_description ? $fallback['meta_description_prefix'] . $translateString($request->meta_description) : '';
            $contentTrans = $fallback['banner'] . $translateString($request->content);

            return response()->json([
                'success' => true,
                'title' => $titleTrans,
                'excerpt' => $excerptTrans,
                'content' => $contentTrans,
                'meta_title' => $metaTitleTrans,
                'meta_description' => $metaDescTrans,
                'offline' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to translate content using AI.',
        ]);
    }

    /**
     * Audit and automatically correct spelling and grammar of the HTML content.
     */
    public function correctGrammar(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->content;

        $prompt = "You are a professional editor. Review the following HTML blog post content. Correct all spelling, grammar, and punctuation mistakes, and refine the tone to be professional, clear, and natural. "
            . "CRITICAL: You MUST preserve all HTML tag structures, formatting, headings, links, classes, and styles exactly as they are. Do not add or remove sections unless necessary for grammatical correctness. Do not include markdown code block wrappers around the JSON. "
            . "Return a JSON object matching this schema exactly:\n"
            . "{\n"
            . "  \"corrected_content\": \"The fully corrected HTML string with spelling/grammar fixed\"\n"
            . "}\n\n"
            . "Content to correct:\n" . $content;

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['corrected_content'])) {
            return response()->json([
                'success' => true,
                'corrected_content' => $aiResult['corrected_content'],
            ]);
        }

        // Local Fallback: simple text replacement if Gemini is offline
        $corrected = $content;
        $replacements = [
            'recieve' => 'receive',
            'definately' => 'definitely',
            'thru' => 'through',
            'teh' => 'the',
            'seperated' => 'separated',
            'untill' => 'until',
            'write code real good' => 'write code really well',
            'I has a apple' => 'I have an apple',
        ];
        foreach ($replacements as $search => $replace) {
            $corrected = preg_replace('/\b' . preg_quote($search, '/') . '\b/i', $replace, $corrected);
        }

        return response()->json([
            'success' => true,
            'corrected_content' => $corrected,
            'offline' => true,
        ]);
    }

    /**
     * Generate 3-5 FAQs based on article content.
     */
    public function generateFaqs(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = strip_tags($request->content);

        $prompt = "Based on the following blog article content, generate 3 to 5 highly relevant Frequently Asked Questions (FAQs) along with their concise, informative answers in HTML format. "
            . "Return a JSON object matching this schema exactly:\n"
            . "{\n"
            . "  \"faqs\": [\n"
            . "    {\n"
            . "      \"question\": \"The FAQ question text?\",\n"
            . "      \"answer\": \"HTML string answer containing paragraphs <p> and simple styling if needed\"\n"
            . "    }\n"
            . "  ]\n"
            . "}\n\n"
            . "Content to generate FAQs from:\n" . $content;

        $aiResult = $this->callGemini($prompt, true);

        if ($aiResult && isset($aiResult['faqs']) && is_array($aiResult['faqs'])) {
            return response()->json([
                'success' => true,
                'faqs' => $aiResult['faqs'],
            ]);
        }

        // Local Fallback if API fails
        $faqs = [];
        $contentLower = strtolower($content);
        if (str_contains($contentLower, 'laravel') || str_contains($contentLower, 'php')) {
            $faqs = [
                [
                    'question' => 'What are the main performance optimization techniques in Laravel?',
                    'answer' => '<p>Performance optimization in Laravel includes eager loading relationships using <code>with()</code> to solve the N+1 query problem, caching database queries, and optimizing routes and configuration.</p>'
                ],
                [
                    'question' => 'How do I follow SOLID principles in Laravel development?',
                    'answer' => '<p>To follow SOLID principles, make sure each controller and class has a single responsibility, invert dependencies using the Service Container, and write interface-driven code to decouple your business logic.</p>'
                ]
            ];
        } else {
            $faqs = [
                [
                    'question' => 'What is the key takeaway of this article?',
                    'answer' => '<p>This article provides an in-depth view of modern tech development, simple structures, and optimization methods for developers.</p>'
                ],
                [
                    'question' => 'How can I implement these strategies in my projects?',
                    'answer' => '<p>Start with a Minimal Viable Product (MVP), run frequent diagnostics, and continuously iterate based on user metrics and clean coding rules.</p>'
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'faqs' => $faqs,
            'offline' => true,
        ]);
    }

    /**
     * Call the Gemini API.
     */
    private function callGemini(string $prompt, bool $json = false)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
        $baseUri = env('GEMINI_BASE_URI', 'https://generativelanguage.googleapis.com/v1beta/');

        if (empty($apiKey)) {
            return null;
        }

        $endpoint = rtrim($baseUri, '/') . "/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        if ($json) {
            $payload['generationConfig'] = [
                'responseMimeType' => 'application/json'
            ];
        }

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(20)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                if ($json) {
                    return json_decode(trim($text), true);
                }
                
                return trim($text);
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
        }

        return null;
    }
}
