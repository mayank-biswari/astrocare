<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use App\Models\CmsPageType;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonialsPageType = CmsPageType::where('slug', 'testimonials')->first();
        
        if (!$testimonialsPageType) {
            return;
        }

        $testimonials = [
            [
                'title' => 'Amazing Kundli Reading Experience',
                'body' => 'I was amazed by the accuracy of my Kundli reading. The astrologer provided detailed insights about my career and relationships that were spot on. The predictions have been coming true, and I feel more confident about my future decisions.',
                'custom_fields' => [
                    'client_name' => 'Priya Sharma',
                    'client_location' => 'Mumbai, India',
                    'service_type' => 'Kundli',
                    'testimonial_rating' => 5
                ]
            ],
            [
                'title' => 'Life-Changing Consultation',
                'body' => 'The astrology consultation helped me understand my life path better. The astrologer was very knowledgeable and patient in explaining everything. I highly recommend their services to anyone seeking guidance.',
                'custom_fields' => [
                    'client_name' => 'Rajesh Kumar',
                    'client_location' => 'Delhi, India',
                    'service_type' => 'Consultation',
                    'testimonial_rating' => 5
                ]
            ],
            [
                'title' => 'Excellent Pooja Service',
                'body' => 'The home pooja service was exceptional. The pandit was very experienced and conducted the ceremony with great devotion. Everything was arranged perfectly, and we felt blessed.',
                'custom_fields' => [
                    'client_name' => 'Meera Patel',
                    'client_location' => 'Ahmedabad, India',
                    'service_type' => 'Pooja',
                    'testimonial_rating' => 5
                ]
            ],
            [
                'title' => 'Authentic Gemstone Quality',
                'body' => 'I purchased a blue sapphire from their store, and the quality is outstanding. The gemstone was certified, and I can feel the positive energy. Great customer service and fast delivery.',
                'custom_fields' => [
                    'client_name' => 'Amit Singh',
                    'client_location' => 'Jaipur, India',
                    'service_type' => 'Product',
                    'testimonial_rating' => 4
                ]
            ],
            [
                'title' => 'Accurate Marriage Predictions',
                'body' => 'The astrologer accurately predicted my marriage timing and compatibility with my partner. The horoscope matching service was thorough and helped us make an informed decision.',
                'custom_fields' => [
                    'client_name' => 'Sneha Gupta',
                    'client_location' => 'Pune, India',
                    'service_type' => 'Consultation',
                    'testimonial_rating' => 5
                ]
            ],
            [
                'title' => 'Professional Temple Pooja',
                'body' => 'The temple pooja booking service was seamless. The ceremony was conducted with proper rituals, and we received the prasad and photos as promised. Very satisfied with the service.',
                'custom_fields' => [
                    'client_name' => 'Vikram Reddy',
                    'client_location' => 'Hyderabad, India',
                    'service_type' => 'Pooja',
                    'testimonial_rating' => 4
                ]
            ],
            [
                'title' => 'Helpful Career Guidance',
                'body' => 'I was confused about my career path, and the astrology consultation provided clear direction. The remedies suggested have been very effective, and I got a promotion within 3 months.',
                'custom_fields' => [
                    'client_name' => 'Anita Joshi',
                    'client_location' => 'Bangalore, India',
                    'service_type' => 'Consultation',
                    'testimonial_rating' => 5
                ]
            ],
            [
                'title' => 'Quality Rudraksha Beads',
                'body' => 'The Rudraksha beads I ordered are genuine and of excellent quality. I can feel the spiritual energy, and my meditation practice has improved significantly since wearing them.',
                'custom_fields' => [
                    'client_name' => 'Suresh Nair',
                    'client_location' => 'Kochi, India',
                    'service_type' => 'Product',
                    'testimonial_rating' => 4
                ]
            ],
            [
                'title' => 'Detailed Birth Chart Analysis',
                'body' => 'The birth chart analysis was incredibly detailed and accurate. The astrologer explained my strengths, weaknesses, and life patterns in a way that made perfect sense. Truly enlightening experience.',
                'custom_fields' => [
                    'client_name' => 'Kavita Agarwal',
                    'client_location' => 'Kolkata, India',
                    'service_type' => 'Kundli',
                    'testimonial_rating' => 5
                ]
            ],
            [
                'title' => 'Effective Remedial Solutions',
                'body' => 'The remedial solutions provided during my consultation have been very effective. My financial situation has improved, and family relationships are much better now. Thank you for the guidance.',
                'custom_fields' => [
                    'client_name' => 'Deepak Mehta',
                    'client_location' => 'Chennai, India',
                    'service_type' => 'Consultation',
                    'testimonial_rating' => 4
                ]
            ]
        ];

        foreach ($testimonials as $testimonial) {
            CmsPage::create([
                'title' => $testimonial['title'],
                'body' => $testimonial['body'],
                'cms_page_type_id' => $testimonialsPageType->id,
                'custom_fields' => $testimonial['custom_fields'],
                'is_published' => true,
                'allow_comments' => false
            ]);
        }
    }
}