<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPageType;
use App\Models\CmsPage;
use App\Models\Language;

class SacredProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create Sacred Products page type
        $productsPageType = CmsPageType::firstOrCreate(
            ['name' => 'Sacred Products'],
            [
                'description' => 'Sacred product pages with custom link field',
                'fields_config' => [
                    'show_comments' => false,
                    'show_posted_date' => false,
                    'show_author' => false,
                    'show_rating' => false,
                    'custom_fields' => [
                        [
                            'name' => 'product_link',
                            'label' => 'Product Link',
                            'type' => 'text',
                            'required' => true
                        ]
                    ]
                ],
                'is_active' => true
            ]
        );

        $defaultLang = Language::getDefaultLanguage()->code ?? 'en';

        // Create four sacred product pages
        $products = [
            [
                'title' => 'Gemstones',
                'body' => 'Authentic precious stones',
                'link' => '/shop/category/gemstones'
            ],
            [
                'title' => 'Rudraksha',
                'body' => 'Sacred beads for meditation',
                'link' => '/shop/category/rudraksha'
            ],
            [
                'title' => 'Yantras',
                'body' => 'Mystical geometric designs',
                'link' => '/shop/category/yantras'
            ],
            [
                'title' => 'Crystals',
                'body' => 'Healing crystal products',
                'link' => '/shop/category/crystals'
            ]
        ];

        foreach ($products as $product) {
            CmsPage::updateOrCreate(
                [
                    'title' => $product['title'],
                    'cms_page_type_id' => $productsPageType->id
                ],
                [
                    'slug' => \Str::slug($product['title']),
                    'body' => $product['body'],
                    'language_code' => $defaultLang,
                    'custom_fields' => [
                        'product_link' => $product['link']
                    ],
                    'is_published' => true,
                    'allow_comments' => false,
                    'meta_title' => $product['title'],
                    'meta_description' => $product['body']
                ]
            );
        }
    }
}