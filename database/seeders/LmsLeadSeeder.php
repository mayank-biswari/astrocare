<?php

namespace Database\Seeders;

use App\Models\CampaignLead;
use Illuminate\Database\Seeder;

class LmsLeadSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];
        $sources = ['website', 'referral', 'tarot-reading-campaign', 'social-media', 'phone-inquiry'];

        $leads = [
            ['Aarav Sharma', 'aarav.sharma@gmail.com', '+91 9876543210', 'Delhi'],
            ['Priya Patel', 'priya.patel@yahoo.com', '+91 9123456789', 'Mumbai'],
            ['Rahul Verma', 'rahul.verma@outlook.com', '+91 8765432109', 'Bangalore'],
            ['Sneha Gupta', 'sneha.gupta@gmail.com', '+91 7654321098', 'Chennai'],
            ['Vikram Singh', 'vikram.singh@hotmail.com', '+91 6543210987', 'Jaipur'],
            ['Ananya Reddy', 'ananya.reddy@gmail.com', '+91 9988776655', 'Hyderabad'],
            ['Karthik Nair', 'karthik.nair@yahoo.com', '+91 8877665544', 'Kochi'],
            ['Meera Joshi', 'meera.joshi@gmail.com', '+91 7766554433', 'Pune'],
            ['Arjun Kumar', 'arjun.kumar@outlook.com', '+91 6655443322', 'Lucknow'],
            ['Divya Iyer', 'divya.iyer@gmail.com', '+91 5544332211', 'Coimbatore'],
            ['Rohan Mehta', 'rohan.mehta@yahoo.com', '+91 9871234567', 'Ahmedabad'],
            ['Kavya Menon', 'kavya.menon@gmail.com', '+91 8762345678', 'Trivandrum'],
            ['Siddharth Das', 'siddharth.das@hotmail.com', '+91 7653456789', 'Kolkata'],
            ['Pooja Agarwal', 'pooja.agarwal@gmail.com', '+91 6544567890', 'Noida'],
            ['Aditya Rao', 'aditya.rao@outlook.com', '+91 9435678901', 'Mysore'],
            ['Nisha Kapoor', 'nisha.kapoor@yahoo.com', '+91 8326789012', 'Chandigarh'],
            ['Manish Tiwari', 'manish.tiwari@gmail.com', '+91 7217890123', 'Bhopal'],
            ['Ritu Saxena', 'ritu.saxena@gmail.com', '+91 6108901234', 'Indore'],
            ['Deepak Pandey', 'deepak.pandey@hotmail.com', '+91 9999012345', 'Varanasi'],
            ['Swati Mishra', 'swati.mishra@yahoo.com', '+91 8890123456', 'Patna'],
            ['Nikhil Jain', 'nikhil.jain@gmail.com', '+91 7781234567', 'Udaipur'],
            ['Anjali Desai', 'anjali.desai@outlook.com', '+91 6672345678', 'Surat'],
            ['Rajesh Pillai', 'rajesh.pillai@gmail.com', '+91 9563456789', 'Thrissur'],
            ['Sunita Bhat', 'sunita.bhat@yahoo.com', '+91 8454567890', 'Mangalore'],
            ['Amit Choudhary', 'amit.choudhary@gmail.com', '+91 7345678901', 'Jodhpur'],
            ['Lakshmi Venkat', 'lakshmi.venkat@hotmail.com', '+91 6236789012', 'Visakhapatnam'],
            ['Gaurav Saxena', 'gaurav.saxena@gmail.com', '+91 9127890123', 'Gurgaon'],
            ['Pallavi Kulkarni', 'pallavi.kulkarni@yahoo.com', '+91 8018901234', 'Nagpur'],
            ['Suresh Nambiar', 'suresh.nambiar@outlook.com', '+91 7909012345', 'Calicut'],
            ['Tanvi Shah', 'tanvi.shah@gmail.com', '+91 6800123456', 'Vadodara'],
        ];

        foreach ($leads as $index => $lead) {
            CampaignLead::create([
                'full_name' => $lead[0],
                'email' => $lead[1],
                'phone_number' => $lead[2],
                'place_of_birth' => $lead[3],
                'date_of_birth' => fake()->date('Y-m-d', '-20 years'),
                'message' => fake()->optional(0.6)->sentence(),
                'source' => $sources[array_rand($sources)],
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }
    }
}
