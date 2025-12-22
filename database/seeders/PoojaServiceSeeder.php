<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoojaServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $poojas = [
            [
                'name' => 'Ganesh Pooja',
                'slug' => 'ganesh-pooja',
                'icon' => '🏛️',
                'description' => 'Lord Ganesh is revered as the remover of obstacles and the patron of arts and sciences. This sacred pooja is performed to seek his blessings for new beginnings, success in endeavors, and removal of hurdles from your path.',
                'price' => 501.00,
                'includes' => ['Ganesh Aarti and Mantras', 'Modak and Laddu offerings', 'Flowers and Incense', 'Coconut and Fruits', 'Blessed Prasad', 'Digital photos of ceremony'],
                'benefits' => ['Removes obstacles from life', 'Brings prosperity and success', 'Enhances wisdom and intelligence', 'Protects from negative energies'],
                'duration' => 45,
                'category' => 'temple'
            ],
            [
                'name' => 'Shiva Abhishek',
                'slug' => 'shiva-abhishek',
                'icon' => '🕉️',
                'description' => 'Shiva Abhishek is a sacred ritual of bathing Lord Shiva with holy water, milk, and other sacred substances. This powerful ceremony purifies the soul and brings spiritual blessings.',
                'price' => 751.00,
                'includes' => ['Rudrabhishek with milk and water', 'Bilva leaves offering', 'Sacred mantras chanting', 'Dhoop and Deep', 'Blessed Prasad', 'Digital photos of ceremony'],
                'benefits' => ['Spiritual purification', 'Inner peace and tranquility', 'Removes negative karma', 'Enhances meditation power'],
                'duration' => 60,
                'category' => 'temple'
            ],
            [
                'name' => 'Lakshmi Pooja',
                'slug' => 'lakshmi-pooja',
                'icon' => '🌺',
                'description' => 'Goddess Lakshmi pooja is performed to attract wealth, prosperity, and abundance. This sacred ritual invokes the blessings of the goddess of wealth and fortune.',
                'price' => 1001.00,
                'includes' => ['Lakshmi Aarti and Mantras', 'Lotus flowers offering', 'Gold coin offering', 'Sweets and Fruits', 'Blessed Prasad', 'Digital photos of ceremony'],
                'benefits' => ['Attracts wealth and prosperity', 'Financial stability', 'Business success', 'Removes financial obstacles'],
                'duration' => 50,
                'category' => 'temple'
            ],
            [
                'name' => 'Hanuman Pooja',
                'slug' => 'hanuman-pooja',
                'icon' => '🙏',
                'description' => 'Lord Hanuman pooja is performed for strength, courage, and protection. This powerful ritual invokes the blessings of the mighty Hanuman for overcoming challenges.',
                'price' => 501.00,
                'includes' => ['Hanuman Chalisa recitation', 'Sindoor offering', 'Flowers and Incense', 'Coconut and Fruits', 'Blessed Prasad', 'Digital photos of ceremony'],
                'benefits' => ['Provides strength and courage', 'Protection from enemies', 'Removes fear and anxiety', 'Success in endeavors'],
                'duration' => 40,
                'category' => 'temple'
            ]
        ];

        foreach ($poojas as $pooja) {
            \App\Models\PoojaService::create($pooja);
        }
    }
}
