<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gemstones', 'slug' => 'gemstones', 'description' => 'Authentic precious and semi-precious stones'],
            ['name' => 'Rudraksha', 'slug' => 'rudraksha', 'description' => 'Sacred beads for meditation and spiritual growth'],
            ['name' => 'Yantras', 'slug' => 'yantras', 'description' => 'Mystical geometric designs for prosperity and protection'],
            ['name' => 'Crystals', 'slug' => 'crystals', 'description' => 'Healing crystal products for energy and wellness'],
            ['name' => 'Pooja Samagri', 'slug' => 'pooja_samagri', 'description' => 'Essential items for religious ceremonies and rituals'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Spiritual and astrological books and guides'],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, ['is_active' => true]));
        }
    }
}
