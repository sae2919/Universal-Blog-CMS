<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Super Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'active',
                'bio' => 'The site super administrator.',
            ]
        );

        // 2. Seed Default Settings
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Universal Blog CMS',
                'site_tagline' => 'Powering niche blogs dynamically',
                'contact_email' => 'admin@example.com',
                'posts_per_page' => 9,
                'site_niche' => 'technology',
                'site_accent_color' => 'indigo',
                'site_font' => 'font-sans',
                'site_layout' => 'grid',
                'default_meta_title' => 'Universal Blog CMS — High Performance Blog Site',
                'default_meta_description' => 'A universal blog portal featuring dynamic settings, caching, full SEO support, and pure Tailwind styling.',
                'blog_description' => 'Welcome to Universal Blog CMS\'s Blog, your go-to source for the latest updates, insights, and trends in the world of software development.',
                'ai_system_instruction' => "You are an AI assistant for a blog CMS. The user has provided some fields for a blog article and wants you to generate the remaining ones. Please generate the missing fields. \n"
                                         . "CRITICAL: If the user did not provide any content (body text), you MUST write a complete, high-quality, professional, and detailed blog article body (at least 300-500 words) structured in valid HTML containing headings (h2, h3), paragraphs, lists or bold code terms where appropriate. Do not return empty content or placeholders.\n"
                                         . "If the Title is missing, suggest a catchy, SEO-optimized title based on the other fields. If the Excerpt is missing, write a 1-2 sentence excerpt summarizing the article.",
            ]
        );

        // Clear settings cache after seeding
        Setting::clearCache();

        // 3. Seed Category
        $itCourses = Category::updateOrCreate(
            ['slug' => 'it-courses'],
            [
                'name' => 'IT Courses',
                'description' => 'Detailed reviews and comparisons of programming, web development, and data science courses in India.',
                'sort_order' => 1,
                'status' => 'active',
                'accent_color' => '#4a1580', // Royal Purple match
                'icon_emoji' => '💻',
            ]
        );

        // 4. Seed Tags
        $tagPython = Tag::updateOrCreate(['slug' => 'python'], ['name' => 'Python']);
        $tagDataScience = Tag::updateOrCreate(['slug' => 'data-science'], ['name' => 'Data Science']);
        $tagCareer = Tag::updateOrCreate(['slug' => 'career-guidance'], ['name' => 'Career Guidance']);

        // 5. Seed Article: "Python vs Data Science Course: Which to Learn First?"
        $post = Post::updateOrCreate(
            ['slug' => 'learn-python-or-data-science-first'],
            [
                'user_id' => $admin->id,
                'category_id' => $itCourses->id,
                'title' => 'Python vs Data Science Course: Which to Learn First?',
                'excerpt' => 'Python vs Data Science course — which should Indian freshers learn first? This guide breaks down both paths, career outcomes, and the right starting point for you.',
                'content' => file_get_contents(database_path('seeders/article_content.html')),
                'faqs' => [
            ['question' => 'Should I learn Python or Data Science first as a complete beginner?', 'answer' => 'Start with Python. Data Science courses for beginners assume at least basic programming fluency — without it, you spend more cognitive effort on Python syntax than on learning data concepts. A focused Python course for beginners takes two to three months and makes every subsequent Data Science topic significantly easier to absorb. The Python vs Data Science course debate is really a question of sequence, not either/or — you will eventually need both.'],
            ['question' => 'Can I learn Data Science without learning Python first?', 'answer' => 'Technically yes, but practically it is not recommended for most freshers. Data Science courses cover Python alongside statistics and machine learning simultaneously. Without prior programming experience, this multi-track learning creates a very steep learning curve and leads to high dropout rates. The two to three months invested in Python foundations first substantially increases your Data Science course completion rate and the quality of projects you produce.'],
            ['question' => 'How long does it take to become job-ready in Data Science from scratch in India?', 'answer' => 'Most freshers starting from zero can reach a competitive entry-level standard in eight to twelve months with consistent daily study of one to two hours, structured courses, and personal project work. The breakdown is roughly: two to three months for Python foundations, three to four months for Data Science fundamentals and SQL, and two to three months for machine learning basics and portfolio development. Candidates with prior mathematics or statistics backgrounds often reach job-ready standard in six to eight months.'],
            ['question' => 'What is the difference between a Python course and a Data Science course?', 'answer' => 'A Python course teaches you to program using Python as the language — syntax, logic, functions, and general-purpose coding skills. A Data Science course teaches you to extract insights from data using Python (specifically data libraries like pandas and scikit-learn), statistics, and machine learning. Python is the tool; Data Science is the discipline that uses the tool. Learning Python first gives you the programming foundation that Data Science courses build upon.'],
            ['question' => 'Which pays more in India — Python developer or Data Scientist?', 'answer' => 'At the entry level, junior data scientist and ML engineer roles typically pay Rs. 5–9 LPA, slightly above Python backend developer roles at Rs. 3.5–6 LPA. However, experienced Python developers who specialise in high-demand areas (performance engineering, fintech, or cloud-native development) can earn as much as or more than data scientists at the senior level. The salary difference narrows with experience, and both tracks offer strong long-term earning potential in India.'],
            ['question' => 'Are free Python and Data Science courses worth it in India?', 'answer' => 'Yes, for foundational learning. Harvard\'s CS50P, Coursera\'s Python for Everybody (audited for free), and Kaggle Learn\'s Python and Data Science tracks are genuinely excellent resources that produce the same foundational knowledge as paid courses costing Rs. 30,000–1.5 lakh. The value of paid courses lies primarily in mentorship, structured accountability, placement support, and industry networking — not the content itself. For self-motivated learners, free courses combined with personal projects are an effective and economical path.'],
            ['question' => 'Which is better: Python or Data Science?', 'answer' => 'Python is better for beginners, while Data Science is better for those interested in analytics and AI. Ideally, learn Python first, then Data Science.'],
            ['question' => 'Is Python still in demand in 2026?', 'answer' => 'Yes. Python remains one of the most in-demand programming languages for software development, automation, AI, machine learning, and Data Science.'],
            ['question' => 'Should I learn DSA first or Python?', 'answer' => 'Learn Python first. Once you understand the basics of programming, start learning Data Structures and Algorithms (DSA).'],
            ['question' => 'Can a fresher get a job in Python?', 'answer' => 'Yes. Freshers can get Python-related roles such as Python Developer, Automation Engineer, QA Tester, or Junior Software Developer with the right skills and projects.'],
        ],
                'featured_image' => null, // Will use default or Unsplash link if needed
                'status' => 'published',
                'published_at' => now(),
                'views' => 0,
                'is_featured' => true,
                'is_trending' => true,
                'allow_comments' => true,
                'meta_title' => 'Python vs Data Science Course: Which to Learn First? - Find My Guru',
                'meta_description' => 'Python vs Data Science course — which should Indian freshers learn first? This guide breaks down both paths, career outcomes, and the right starting point for you.',
                'meta_keywords' => 'Python vs Data Science Course, Python vs Data Science, Python Course, Data Science Course, Python for Beginners, Data Science for Beginners, Learn Python, Learn Data Science',
                'og_title' => 'Python vs Data Science Course: Which to Learn First?',
                'og_description' => 'Python vs Data Science course — which should Indian freshers learn first? This guide breaks down both paths, career outcomes, and the right starting point for you.',
                'og_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200',
                'schema_type' => 'BlogPosting',
            ]
        );

        $post->tags()->sync([$tagPython->id, $tagDataScience->id, $tagCareer->id]);

        // 6. Seed Notifications
        \App\Models\Notification::updateOrCreate(
            ['title' => '5 New Comments'],
            [
                'message' => '5 new comments are pending moderation.',
                'type' => 'comment',
                'link' => '/admin/comments',
                'created_at' => now()->subHours(2),
            ]
        );

        \App\Models\Notification::updateOrCreate(
            ['title' => '2 New Users'],
            [
                'message' => '2 new user accounts were registered today.',
                'type' => 'user',
                'link' => '/admin/users',
                'created_at' => now()->subHours(4),
            ]
        );

        \App\Models\Notification::updateOrCreate(
            ['title' => 'SEO Warning'],
            [
                'message' => 'Your page "About Us" is missing a meta description.',
                'type' => 'seo',
                'link' => '/admin/seo',
                'created_at' => now()->subDay(),
            ]
        );

        \App\Models\Notification::updateOrCreate(
            ['title' => 'Scheduled Post Published'],
            [
                'message' => 'The scheduled post "Python vs Data Science Course" was published.',
                'type' => 'post',
                'link' => '/admin/posts',
                'created_at' => now()->subDays(2),
            ]
        );
    }
}

