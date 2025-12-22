<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Gemstones
            ['name' => 'Natural Ruby (Manik)', 'category' => 'gemstones', 'description' => 'Certified 3.5 carat ruby for Sun. Enhances leadership qualities and confidence.', 'price' => 15999, 'slug' => 'natural-ruby-manik', 'specifications' => ['Weight: 3.5 carat', 'Origin: Burma', 'Certification: GIA'], 'features' => ['Enhances leadership', 'Boosts confidence', 'Improves health'], 'is_active' => true, 'is_featured' => true],
            ['name' => 'Natural Emerald (Panna)', 'category' => 'gemstones', 'description' => 'Certified 4 carat emerald for Mercury. Improves communication and intelligence.', 'price' => 22999, 'slug' => 'natural-emerald-panna', 'specifications' => ['Weight: 4 carat', 'Origin: Colombia', 'Certification: GIA'], 'features' => ['Improves communication', 'Enhances intelligence', 'Brings prosperity'], 'is_active' => true, 'is_featured' => true],
            ['name' => 'Yellow Sapphire (Pukhraj)', 'category' => 'gemstones', 'description' => 'Certified 5 carat yellow sapphire for Jupiter. Brings wisdom and prosperity.', 'price' => 18999, 'slug' => 'yellow-sapphire-pukhraj', 'specifications' => ['Weight: 5 carat', 'Origin: Ceylon', 'Certification: GIA'], 'features' => ['Brings wisdom', 'Attracts wealth', 'Improves marriage prospects'], 'is_active' => true, 'is_featured' => true],
            ['name' => 'Blue Sapphire (Neelam)', 'category' => 'gemstones', 'description' => 'Certified 4.5 carat blue sapphire for Saturn. Provides protection and success.', 'price' => 25999, 'slug' => 'blue-sapphire-neelam', 'specifications' => ['Weight: 4.5 carat', 'Origin: Kashmir', 'Certification: GIA'], 'features' => ['Provides protection', 'Brings success', 'Removes obstacles'], 'is_active' => true],
            ['name' => 'Natural Pearl (Moti)', 'category' => 'gemstones', 'description' => 'Certified 6 carat natural pearl for Moon. Brings peace and emotional balance.', 'price' => 8999, 'slug' => 'natural-pearl-moti', 'specifications' => ['Weight: 6 carat', 'Origin: South Sea', 'Certification: Lab tested'], 'features' => ['Brings peace', 'Emotional balance', 'Improves relationships'], 'is_active' => true],
            
            // Rudraksha
            ['name' => '1 Mukhi Rudraksha', 'category' => 'rudraksha', 'description' => 'Rare 1 Mukhi Rudraksha representing Lord Shiva. Brings spiritual enlightenment.', 'price' => 2999, 'slug' => '1-mukhi-rudraksha', 'specifications' => ['Origin: Nepal', 'Size: 22-25mm', 'Certification: Lab tested'], 'features' => ['Spiritual enlightenment', 'Removes sins', 'Brings moksha'], 'is_active' => true, 'is_featured' => true],
            ['name' => '5 Mukhi Rudraksha', 'category' => 'rudraksha', 'description' => 'Nepali origin, lab certified. Represents Lord Shiva and brings peace.', 'price' => 299, 'slug' => '5-mukhi-rudraksha', 'specifications' => ['Origin: Nepal', 'Size: 18-20mm', 'Certification: Lab tested'], 'features' => ['Brings peace', 'Improves health', 'Reduces stress'], 'is_active' => true],
            ['name' => '7 Mukhi Rudraksha', 'category' => 'rudraksha', 'description' => 'Seven faced rudraksha for wealth and prosperity. Blessed by Goddess Lakshmi.', 'price' => 899, 'slug' => '7-mukhi-rudraksha', 'specifications' => ['Origin: Nepal', 'Size: 19-21mm', 'Certification: Lab tested'], 'features' => ['Attracts wealth', 'Brings prosperity', 'Removes poverty'], 'is_active' => true],
            ['name' => 'Gauri Shankar Rudraksha', 'category' => 'rudraksha', 'description' => 'Two naturally joined rudraksha beads representing Shiva-Parvati unity.', 'price' => 1999, 'slug' => 'gauri-shankar-rudraksha', 'specifications' => ['Origin: Nepal', 'Size: 20-22mm', 'Certification: Lab tested'], 'features' => ['Improves relationships', 'Brings harmony', 'Enhances love'], 'is_active' => true],
            
            // Yantras
            ['name' => 'Shri Yantra (Copper)', 'category' => 'yantras', 'description' => 'Copper Shri Yantra, 3x3 inches, energized. Attracts wealth and prosperity.', 'price' => 899, 'slug' => 'shri-yantra-copper', 'specifications' => ['Material: Copper', 'Size: 3x3 inches', 'Energized: Yes'], 'features' => ['Attracts wealth', 'Brings prosperity', 'Removes obstacles'], 'is_active' => true, 'is_featured' => true],
            ['name' => 'Mahamrityunjaya Yantra', 'category' => 'yantras', 'description' => 'Silver plated yantra for health and protection from negative energies.', 'price' => 1299, 'slug' => 'mahamrityunjaya-yantra', 'specifications' => ['Material: Silver plated', 'Size: 4x4 inches', 'Energized: Yes'], 'features' => ['Protects from diseases', 'Removes negative energy', 'Brings good health'], 'is_active' => true],
            ['name' => 'Kuber Yantra', 'category' => 'yantras', 'description' => 'Brass Kuber Yantra for wealth and financial prosperity. Lord of treasures.', 'price' => 699, 'slug' => 'kuber-yantra', 'specifications' => ['Material: Brass', 'Size: 3x3 inches', 'Energized: Yes'], 'features' => ['Attracts money', 'Business success', 'Financial stability'], 'is_active' => true],
            
            // Crystals
            ['name' => 'Rose Quartz Bracelet', 'category' => 'crystals', 'description' => 'Natural rose quartz healing bracelet for love and emotional healing.', 'price' => 599, 'slug' => 'rose-quartz-bracelet', 'specifications' => ['Material: Natural Rose Quartz', 'Size: Adjustable', 'Beads: 8mm'], 'features' => ['Attracts love', 'Emotional healing', 'Reduces stress'], 'is_active' => true],
            ['name' => 'Clear Quartz Crystal', 'category' => 'crystals', 'description' => 'Master healer crystal for amplifying energy and clarity of mind.', 'price' => 399, 'slug' => 'clear-quartz-crystal', 'specifications' => ['Material: Natural Clear Quartz', 'Size: 2-3 inches', 'Weight: 100-150g'], 'features' => ['Amplifies energy', 'Brings clarity', 'Master healer'], 'is_active' => true],
            ['name' => 'Amethyst Cluster', 'category' => 'crystals', 'description' => 'Natural amethyst cluster for spiritual protection and intuition.', 'price' => 1299, 'slug' => 'amethyst-cluster', 'specifications' => ['Material: Natural Amethyst', 'Size: 3-4 inches', 'Weight: 200-300g'], 'features' => ['Spiritual protection', 'Enhances intuition', 'Promotes peace'], 'is_active' => true],
            
            // Pooja Samagri
            ['name' => 'Complete Pooja Kit', 'category' => 'pooja_samagri', 'description' => 'Complete pooja kit with all essential items for daily worship.', 'price' => 799, 'slug' => 'complete-pooja-kit', 'specifications' => ['Items: 25+ pieces', 'Includes: Diya, Incense, Kumkum, etc.'], 'features' => ['Complete worship set', 'High quality items', 'Ready to use'], 'is_active' => true],
            ['name' => 'Brass Diya Set (5 pieces)', 'category' => 'pooja_samagri', 'description' => 'Traditional brass diyas for lighting during prayers and festivals.', 'price' => 299, 'slug' => 'brass-diya-set', 'specifications' => ['Material: Brass', 'Quantity: 5 pieces', 'Size: Medium'], 'features' => ['Traditional design', 'Durable brass', 'Festival ready'], 'is_active' => true],
            ['name' => 'Incense Sticks Combo', 'category' => 'pooja_samagri', 'description' => 'Premium incense sticks combo pack with 10 different fragrances.', 'price' => 199, 'slug' => 'incense-sticks-combo', 'specifications' => ['Fragrances: 10 types', 'Sticks: 200 pieces', 'Burn time: 45 minutes each'], 'features' => ['Premium quality', 'Long lasting', 'Natural fragrances'], 'is_active' => true],
            
            // Books
            ['name' => 'Lal Kitab (Hindi)', 'category' => 'books', 'description' => 'Complete Lal Kitab with remedies and predictions in Hindi language.', 'price' => 499, 'slug' => 'lal-kitab-hindi', 'specifications' => ['Language: Hindi', 'Pages: 400+', 'Binding: Hardcover'], 'features' => ['Complete remedies', 'Easy to understand', 'Authentic content'], 'is_active' => true],
            ['name' => 'Brihat Parashara Hora Shastra', 'category' => 'books', 'description' => 'Classical text on Vedic astrology by Sage Parashara with English translation.', 'price' => 899, 'slug' => 'brihat-parashara-hora-shastra', 'specifications' => ['Language: English', 'Pages: 600+', 'Binding: Hardcover'], 'features' => ['Classical text', 'English translation', 'Comprehensive guide'], 'is_active' => true]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
