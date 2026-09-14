<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom font directory (Noto Sans Arabic for Pashto / Arabic script)
    |--------------------------------------------------------------------------
    */
    'font_dir' => env('MPDF_FONT_DIR', storage_path('fonts')),

    'default_font' => 'notosansarabic',

    /*
    | useOTL enables OpenType layout features required for connected Arabic/Pashto glyphs.
    */
    'font_data' => [
        'notosansarabic' => [
            'R' => 'NotoSansArabic-Regular.ttf',
            'B' => 'NotoSansArabic-Bold.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
    ],

    'temp_dir' => storage_path('app/mpdf-tmp'),

];
