<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locales = [
            [
                'name' => 'English',
                'short_name' => 'en',
                'display_type' => 'ltr',
                'is_default' => 1,
            ],

            [
                'name' => 'Vietnamese',
                'short_name' => 'vi',
                'display_type' => 'ltr',
                'is_default' => 0,

            ],

        ];

        foreach ($locales as $item) {
            \App\Models\Locale::firstOrCreate([
                'name' => $item['name'],
                'short_name' => $item['short_name'],
                'display_type' => $item['display_type'],
                'is_default' => $item['is_default']]);
        }



    }
}
