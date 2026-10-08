<?php

  // Population in millions, 2023, rounded - UN World Population Prospects.

  $population = [];

  foreach ( [ 'India' => 1428, 'China' => 1410, 'United States' => 340, 'Indonesia' => 277, 'Pakistan' => 240,
              'Nigeria' => 224, 'Brazil' => 216, 'Bangladesh' => 173, 'Russia' => 144, 'Mexico' => 128,
              'Ethiopia' => 127, 'Japan' => 124, 'Philippines' => 117, 'Egypt' => 113, 'DR Congo' => 102,
              'Vietnam' => 99, 'Iran' => 89, 'Turkey' => 85, 'Germany' => 84, 'Thailand' => 72,
              'United Kingdom' => 68, 'Tanzania' => 67, 'France' => 65, 'South Africa' => 60, 'Italy' => 59,
              'Kenya' => 55, 'Myanmar' => 54, 'Colombia' => 52, 'South Korea' => 52, 'Sudan' => 48,
              'Uganda' => 48, 'Spain' => 48, 'Argentina' => 46, 'Algeria' => 45, 'Iraq' => 45,
              'Afghanistan' => 42, 'Canada' => 39, 'Poland' => 37, 'Morocco' => 37, 'Saudi Arabia' => 37,
              'Ukraine' => 37, 'Angola' => 37, 'Uzbekistan' => 35, 'Peru' => 34, 'Malaysia' => 34,
              'Mozambique' => 34, 'Ghana' => 34, 'Yemen' => 34, 'Nepal' => 31, 'Venezuela' => 28,
              'Madagascar' => 30, 'Cameroon' => 28, 'Australia' => 26, 'North Korea' => 26, 'Niger' => 27,
              'Mali' => 23, 'Kazakhstan' => 20, 'Chile' => 20, 'Romania' => 19, 'Netherlands' => 18,
              'Ecuador' => 18, 'Guatemala' => 18, 'Zambia' => 21, 'Chad' => 18, 'Somalia' => 18,
              'Sweden' => 10, 'Norway' => 5, 'Finland' => 6, 'New Zealand' => 5, 'Mongolia' => 3,
              'Bolivia' => 12, 'Paraguay' => 7, 'Libya' => 7, 'Mauritania' => 5, 'Namibia' => 3,
              'Botswana' => 3, 'Greenland' => 0.06, 'Iceland' => 0.4, 'Papua New Guinea' => 10, 'Cuba' => 11 ] as $country => $millions )
    $population [] = [ 'country' => $country, 'millions' => $millions ];

?>
