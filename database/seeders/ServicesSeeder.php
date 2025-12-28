<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPageType;
use App\Models\CmsPage;
use App\Models\Language;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create Services page type
        $servicesPageType = CmsPageType::firstOrCreate(
            ['name' => 'Services'],
            [
                'description' => 'Service pages with custom link field',
                'fields_config' => [
                    'show_comments' => false,
                    'show_posted_date' => false,
                    'show_author' => false,
                    'show_rating' => false,
                    'custom_fields' => [
                        [
                            'name' => 'service_link',
                            'label' => 'Service Link',
                            'type' => 'text',
                            'required' => true
                        ]
                    ]
                ],
                'is_active' => true
            ]
        );

        $defaultLang = Language::getDefaultLanguage()->code ?? 'en';

        // Create three service pages
        $services = [
            [
                'title' => 'Astrology Consultation',
                'body' => 'Get personalized guidance through chat, video, or phone consultations with expert astrologers.',
                'link' => '/services/consultations'
            ],
            [
                'title' => 'Kundli Reading',
                'body' => 'Generate detailed birth charts and get comprehensive astrological analysis.',
                'link' => '/services/kundli'
            ],
            [
                'title' => 'Pooja & Rituals',
                'body' => 'Book temple poojas, home ceremonies, and connect with experienced pandits.',
                'link' => '/pooja'
            ]
        ];

        foreach ($services as $service) {
            CmsPage::updateOrCreate(
                [
                    'title' => $service['title'],
                    'cms_page_type_id' => $servicesPageType->id
                ],
                [
                    'slug' => \Str::slug($service['title']),
                    'body' => $service['body'],
                    'language_code' => $defaultLang,
                    'custom_fields' => [
                        'service_link' => $service['link']
                    ],
                    'is_published' => true,
                    'allow_comments' => false,
                    'meta_title' => $service['title'],
                    'meta_description' => $service['body']
                ]
            );
        }
    }
}