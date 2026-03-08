<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoojaPageSeeder extends Seeder
{
    public function run(): void
    {
        $pageTypeId = DB::table('cms_page_types')->where('slug', 'pooja-page')->value('id');

        $poojas = [
            [
                'title' => 'Ganesh Pooja',
                'slug' => 'ganesh-pooja',
                'body' => 'Lord Ganesh is revered as the remover of obstacles and the patron of arts and sciences. This sacred pooja is performed to seek his blessings for new beginnings, success in endeavors, and removal of hurdles from your path.',
                'price' => 2100,
                'includes' => 'Ganesh idol, Flowers, Prasad, Aarti',
                'duration' => 45,
                'category' => 'temple'
            ],
            [
                'title' => 'Shiva Abhishek',
                'slug' => 'shiva-abhishek',
                'body' => 'Shiva Abhishek is a sacred ritual of bathing Lord Shiva with holy water, milk, honey, and other sacred items. This powerful ceremony is performed to seek blessings for health, prosperity, and spiritual growth.',
                'price' => 751,
                'includes' => 'Rudraksha, Bilva leaves, Sacred water, Milk and honey, Prasad',
                'benefits' => 'Removes negative energy, Brings peace and prosperity, Enhances spiritual growth, Fulfills desires',
                'duration' => 60,
                'category' => 'temple'
            ],
            [
                'title' => 'Lakshmi Pooja',
                'slug' => 'lakshmi-pooja',
                'body' => 'Goddess Lakshmi pooja is performed to attract wealth, prosperity, and abundance into your life. This sacred ritual invokes the blessings of the Goddess of wealth for financial stability and success.',
                'price' => 1001,
                'includes' => 'Lakshmi idol, Gold coin, Lotus flowers, Sacred lamps, Prasad',
                'benefits' => 'Attracts wealth and prosperity, Removes financial obstacles, Brings success in business, Ensures family harmony',
                'duration' => 75,
                'category' => 'temple'
            ],
            [
                'title' => 'Hanuman Pooja',
                'slug' => 'hanuman-pooja',
                'body' => 'Lord Hanuman pooja is performed for strength, courage, and protection from evil forces. This powerful ritual helps overcome obstacles and brings success in all endeavors.',
                'price' => 501,
                'includes' => 'Hanuman idol, Red flowers, Sindoor, Sacred oil, Prasad',
                'benefits' => 'Provides strength and courage, Removes obstacles, Protection from negative forces, Success in endeavors',
                'duration' => 45,
                'category' => 'temple'
            ],
            [
                'title' => 'Maha Mrityunjay Jaap',
                'slug' => 'maha-mrityunjay-jaap',
                'body' => 'Powerful mantra chanting for health, longevity, and protection from negative energies.',
                'price' => 2100,
                'includes' => '1,25,000 mantra chanting, Havan, Prasad',
                'benefits' => 'Promotes health and longevity, Protection from negative energies, Spiritual healing, Peace of mind',
                'duration' => 660,
                'category' => 'jaap'
            ],
            [
                'title' => 'Kaal Sarp Dosh Puja',
                'slug' => 'kaal-sarp-dosh-puja',
                'body' => 'Special ritual to neutralize the negative effects of Kaal Sarp Dosha in your horoscope.',
                'price' => 3100,
                'includes' => 'Complete Kaal Sarp Dosh nivaran, Rudrabhishek, Energized rudraksha',
                'benefits' => 'Removes Kaal Sarp Dosha effects, Brings peace and prosperity, Career growth, Relationship harmony',
                'duration' => 120,
                'category' => 'jaap'
            ],
            [
                'title' => 'Navgraha Shanti',
                'slug' => 'navgraha-shanti',
                'body' => 'Comprehensive ritual to appease all nine planets and reduce their malefic effects.',
                'price' => 5100,
                'includes' => 'All 9 planetary mantras, Havan for each planet, Gemstone recommendations',
                'benefits' => 'Balances planetary influences, Removes obstacles, Success in all endeavors, Mental peace',
                'duration' => 180,
                'category' => 'jaap'
            ],
            [
                'title' => 'Lakshmi Kubera Pooja',
                'slug' => 'lakshmi-kubera-pooja',
                'body' => 'Special worship of Goddess Lakshmi and Lord Kubera for wealth, prosperity, and financial stability.',
                'price' => 2100,
                'includes' => 'Lakshmi mantra chanting, Gold coin offering, Prosperity yantra energized',
                'benefits' => 'Attracts wealth and abundance, Financial stability, Business success, Debt removal',
                'duration' => 90,
                'category' => 'jaap'
            ],
            [
                'title' => 'Health Pooja',
                'slug' => 'health-pooja',
                'body' => 'Healing rituals and health-focused poojas for physical and mental well-being.',
                'price' => 1500,
                'includes' => 'Dhanvantari pooja, Healing mantras, Prasad, Energized rudraksha',
                'benefits' => 'Promotes good health, Speeds recovery, Mental peace, Protection from diseases',
                'duration' => 60,
                'category' => 'special'
            ],
            [
                'title' => 'Marriage Pooja',
                'slug' => 'marriage-pooja',
                'body' => 'Wedding ceremonies and marriage blessings for a harmonious married life.',
                'price' => 2500,
                'includes' => 'Vivah havan, Mangal sutra blessing, Couple blessing, Prasad',
                'benefits' => 'Harmonious married life, Removes marriage obstacles, Mutual understanding, Family happiness',
                'duration' => 90,
                'category' => 'special'
            ],
            [
                'title' => 'Wealth Pooja',
                'slug' => 'wealth-pooja',
                'body' => 'Prosperity and financial growth rituals to attract abundance and success.',
                'price' => 1800,
                'includes' => 'Lakshmi pooja, Kubera mantra, Wealth yantra, Prasad',
                'benefits' => 'Financial prosperity, Business growth, Debt removal, Career success',
                'duration' => 75,
                'category' => 'special'
            ],
            [
                'title' => 'Education Pooja',
                'slug' => 'education-pooja',
                'body' => 'Academic success and knowledge enhancement rituals for students and learners.',
                'price' => 1200,
                'includes' => 'Saraswati pooja, Knowledge mantras, Prasad, Blessed pen',
                'benefits' => 'Academic excellence, Enhanced concentration, Memory improvement, Success in exams',
                'duration' => 60,
                'category' => 'special'
            ],
            [
                'title' => 'Peace Pooja',
                'slug' => 'peace-pooja',
                'body' => 'Harmony and spiritual peace rituals for mental tranquility and family harmony.',
                'price' => 1500,
                'includes' => 'Shanti path, Peace mantras, Havan, Prasad',
                'benefits' => 'Mental peace, Family harmony, Stress relief, Spiritual growth',
                'duration' => 60,
                'category' => 'special'
            ]
        ];

        foreach ($poojas as $pooja) {
            DB::table('cms_pages')->updateOrInsert(
                ['slug' => $pooja['slug']],
                [
                    'cms_page_type_id' => $pageTypeId,
                    'title' => $pooja['title'],
                    'body' => $pooja['body'],
                    'image' => 'cms/5Q88MJNGhToBUZVdIcCYBzqLq3Wh29SIoxAnSEyX.jpg',
                    'custom_fields' => json_encode([
                        'price' => $pooja['price'],
                        'includes' => $pooja['includes'],
                        'benefits' => $pooja['benefits'] ?? null,
                        'duration' => $pooja['duration'],
                        'category' => $pooja['category']
                    ]),
                    'is_published' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
