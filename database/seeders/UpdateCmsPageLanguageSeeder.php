<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use App\Models\Language;

class UpdateCmsPageLanguageSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLanguage = Language::getDefaultLanguage();
        $defaultCode = $defaultLanguage ? $defaultLanguage->code : 'en';
        
        CmsPage::whereNull('language_code')->update(['language_code' => $defaultCode]);
    }
}