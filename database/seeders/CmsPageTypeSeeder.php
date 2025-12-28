<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPageType;

class CmsPageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $pageTypes = [
            [
                'name' => 'Pages',
                'slug' => 'pages',
                'description' => 'Standard pages with basic content',
                'fields_config' => [
                    'show_comments' => true,
                    'show_posted_date' => false,
                    'show_author' => false,
                    'show_rating' => false,
                    'custom_fields' => []
                ]
            ],
            [
                'name' => 'Blogs',
                'slug' => 'blogs',
                'description' => 'Blog posts with author and date information',
                'fields_config' => [
                    'show_comments' => true,
                    'show_posted_date' => true,
                    'show_author' => true,
                    'show_rating' => false,
                    'custom_fields' => [
                        ['name' => 'author_name', 'label' => 'Author Name', 'type' => 'text', 'required' => false],
                        ['name' => 'read_time', 'label' => 'Read Time (minutes)', 'type' => 'number', 'required' => false]
                    ]
                ]
            ],
            [
                'name' => 'Horoscopes',
                'slug' => 'horoscopes',
                'description' => 'Horoscope content with zodiac information',
                'fields_config' => [
                    'show_comments' => false,
                    'show_posted_date' => true,
                    'show_author' => true,
                    'show_rating' => false,
                    'custom_fields' => [
                        ['name' => 'zodiac_sign', 'label' => 'Zodiac Sign', 'type' => 'select', 'required' => true, 'options' => ['Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces']],
                        ['name' => 'date_range', 'label' => 'Date Range', 'type' => 'text', 'required' => false],
                        ['name' => 'lucky_number', 'label' => 'Lucky Number', 'type' => 'number', 'required' => false],
                        ['name' => 'lucky_color', 'label' => 'Lucky Color', 'type' => 'text', 'required' => false]
                    ]
                ]
            ],
            [
                'name' => 'Testimonials',
                'slug' => 'testimonials',
                'description' => 'Customer testimonials and reviews',
                'fields_config' => [
                    'show_comments' => false,
                    'show_posted_date' => true,
                    'show_author' => false,
                    'show_rating' => true,
                    'custom_fields' => [
                        ['name' => 'client_name', 'label' => 'Client Name', 'type' => 'text', 'required' => true],
                        ['name' => 'client_location', 'label' => 'Client Location', 'type' => 'text', 'required' => false],
                        ['name' => 'service_type', 'label' => 'Service Type', 'type' => 'select', 'required' => false, 'options' => ['Consultation', 'Kundli', 'Pooja', 'Product']],
                        ['name' => 'testimonial_rating', 'label' => 'Rating (1-5)', 'type' => 'number', 'required' => false]
                    ]
                ]
            ]
        ];

        foreach ($pageTypes as $pageType) {
            CmsPageType::create($pageType);
        }
    }
}