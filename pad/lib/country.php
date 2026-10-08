<?php

  // Countries - the {country} tag: a flag and an English name from an ISO 3166-1 code.
  //
  //   {country 'NL'}             <span class="pad-country" role="img" aria-label="Netherlands">🇳🇱</span>
  //   {country 'NLD', name}      the flag and the name beside it
  //
  // A flag is no picture of its own: it is the code's two letters written as regional
  // indicator symbols, which a system with flag emoji draws as the flag - one without them
  // (Windows) shows the two letters, which still say which country it is.
  //
  // padCountry         [ alpha-2, alpha-3, name ] of an alpha-2 code, an alpha-3 code or
  //                    an English name, any case; NULL when it is none
  // padCountryFlag     the flag of an alpha-2 code
  // padCountrySuggest  the code of the country nearest to text that is none, for the error
  // padCountryTable    alpha-2 => [ alpha-3, name ]: the 249 entries of ISO 3166-1, named
  //                    as the Unicode CLDR names them in English

  function padCountry ( $code ) {

    $code  = trim ( (string) $code );
    $upper = strtoupper ( $code );
    $table = padCountryTable ();

    if ( strlen ( $upper ) == 2 and isset ( $table [$upper] ) )
      return [ $upper, $table [$upper] [0], $table [$upper] [1] ];

    foreach ( $table as $two => list ( $three, $name ) )
      if ( $upper === $three or mb_strtolower ( $code ) === mb_strtolower ( $name ) )
        return [ $two, $three, $name ];

    return NULL;

  }

  function padCountryFlag ( $two ) {

    $flag = '';

    foreach ( str_split ( strtoupper ( (string) $two ) ) as $letter )
      $flag .= mb_chr ( 0x1F1E6 + ord ( $letter ) - 65, 'UTF-8' );

    return $flag;

  }

  function padCountrySuggest ( $text ) {

    $text = mb_strtolower ( trim ( (string) $text ) );
    $best = '';
    $near = PHP_INT_MAX;

    foreach ( padCountryTable () as $two => list ( $three, $name ) ) {

      $name     = mb_strtolower ( $name );
      $distance = levenshtein ( $text, $name );

      if ( strlen ( $text ) > 3 and str_contains ( $name, $text ) )
        $distance = min ( $distance, 2 );

      if ( $distance < $near ) {
        $near = $distance;
        $best = $two;
      }

    }

    return ( strlen ( $text ) > 3 and $near <= max ( 2, intdiv ( strlen ( $text ), 3 ) ) ) ? $best : '';

  }

  function padCountryTable () {

    static $table = [

      'AD' => [ 'AND', 'Andorra' ],
      'AE' => [ 'ARE', 'United Arab Emirates' ],
      'AF' => [ 'AFG', 'Afghanistan' ],
      'AG' => [ 'ATG', 'Antigua & Barbuda' ],
      'AI' => [ 'AIA', 'Anguilla' ],
      'AL' => [ 'ALB', 'Albania' ],
      'AM' => [ 'ARM', 'Armenia' ],
      'AO' => [ 'AGO', 'Angola' ],
      'AQ' => [ 'ATA', 'Antarctica' ],
      'AR' => [ 'ARG', 'Argentina' ],
      'AS' => [ 'ASM', 'American Samoa' ],
      'AT' => [ 'AUT', 'Austria' ],
      'AU' => [ 'AUS', 'Australia' ],
      'AW' => [ 'ABW', 'Aruba' ],
      'AX' => [ 'ALA', 'Åland Islands' ],
      'AZ' => [ 'AZE', 'Azerbaijan' ],
      'BA' => [ 'BIH', 'Bosnia & Herzegovina' ],
      'BB' => [ 'BRB', 'Barbados' ],
      'BD' => [ 'BGD', 'Bangladesh' ],
      'BE' => [ 'BEL', 'Belgium' ],
      'BF' => [ 'BFA', 'Burkina Faso' ],
      'BG' => [ 'BGR', 'Bulgaria' ],
      'BH' => [ 'BHR', 'Bahrain' ],
      'BI' => [ 'BDI', 'Burundi' ],
      'BJ' => [ 'BEN', 'Benin' ],
      'BL' => [ 'BLM', 'St. Barthélemy' ],
      'BM' => [ 'BMU', 'Bermuda' ],
      'BN' => [ 'BRN', 'Brunei' ],
      'BO' => [ 'BOL', 'Bolivia' ],
      'BQ' => [ 'BES', 'Caribbean Netherlands' ],
      'BR' => [ 'BRA', 'Brazil' ],
      'BS' => [ 'BHS', 'Bahamas' ],
      'BT' => [ 'BTN', 'Bhutan' ],
      'BV' => [ 'BVT', 'Bouvet Island' ],
      'BW' => [ 'BWA', 'Botswana' ],
      'BY' => [ 'BLR', 'Belarus' ],
      'BZ' => [ 'BLZ', 'Belize' ],
      'CA' => [ 'CAN', 'Canada' ],
      'CC' => [ 'CCK', 'Cocos (Keeling) Islands' ],
      'CD' => [ 'COD', 'Congo - Kinshasa' ],
      'CF' => [ 'CAF', 'Central African Republic' ],
      'CG' => [ 'COG', 'Congo - Brazzaville' ],
      'CH' => [ 'CHE', 'Switzerland' ],
      'CI' => [ 'CIV', 'Côte d’Ivoire' ],
      'CK' => [ 'COK', 'Cook Islands' ],
      'CL' => [ 'CHL', 'Chile' ],
      'CM' => [ 'CMR', 'Cameroon' ],
      'CN' => [ 'CHN', 'China' ],
      'CO' => [ 'COL', 'Colombia' ],
      'CR' => [ 'CRI', 'Costa Rica' ],
      'CU' => [ 'CUB', 'Cuba' ],
      'CV' => [ 'CPV', 'Cape Verde' ],
      'CW' => [ 'CUW', 'Curaçao' ],
      'CX' => [ 'CXR', 'Christmas Island' ],
      'CY' => [ 'CYP', 'Cyprus' ],
      'CZ' => [ 'CZE', 'Czechia' ],
      'DE' => [ 'DEU', 'Germany' ],
      'DJ' => [ 'DJI', 'Djibouti' ],
      'DK' => [ 'DNK', 'Denmark' ],
      'DM' => [ 'DMA', 'Dominica' ],
      'DO' => [ 'DOM', 'Dominican Republic' ],
      'DZ' => [ 'DZA', 'Algeria' ],
      'EC' => [ 'ECU', 'Ecuador' ],
      'EE' => [ 'EST', 'Estonia' ],
      'EG' => [ 'EGY', 'Egypt' ],
      'EH' => [ 'ESH', 'Western Sahara' ],
      'ER' => [ 'ERI', 'Eritrea' ],
      'ES' => [ 'ESP', 'Spain' ],
      'ET' => [ 'ETH', 'Ethiopia' ],
      'FI' => [ 'FIN', 'Finland' ],
      'FJ' => [ 'FJI', 'Fiji' ],
      'FK' => [ 'FLK', 'Falkland Islands' ],
      'FM' => [ 'FSM', 'Micronesia' ],
      'FO' => [ 'FRO', 'Faroe Islands' ],
      'FR' => [ 'FRA', 'France' ],
      'GA' => [ 'GAB', 'Gabon' ],
      'GB' => [ 'GBR', 'United Kingdom' ],
      'GD' => [ 'GRD', 'Grenada' ],
      'GE' => [ 'GEO', 'Georgia' ],
      'GF' => [ 'GUF', 'French Guiana' ],
      'GG' => [ 'GGY', 'Guernsey' ],
      'GH' => [ 'GHA', 'Ghana' ],
      'GI' => [ 'GIB', 'Gibraltar' ],
      'GL' => [ 'GRL', 'Greenland' ],
      'GM' => [ 'GMB', 'Gambia' ],
      'GN' => [ 'GIN', 'Guinea' ],
      'GP' => [ 'GLP', 'Guadeloupe' ],
      'GQ' => [ 'GNQ', 'Equatorial Guinea' ],
      'GR' => [ 'GRC', 'Greece' ],
      'GS' => [ 'SGS', 'South Georgia & South Sandwich Islands' ],
      'GT' => [ 'GTM', 'Guatemala' ],
      'GU' => [ 'GUM', 'Guam' ],
      'GW' => [ 'GNB', 'Guinea-Bissau' ],
      'GY' => [ 'GUY', 'Guyana' ],
      'HK' => [ 'HKG', 'Hong Kong' ],
      'HM' => [ 'HMD', 'Heard & McDonald Islands' ],
      'HN' => [ 'HND', 'Honduras' ],
      'HR' => [ 'HRV', 'Croatia' ],
      'HT' => [ 'HTI', 'Haiti' ],
      'HU' => [ 'HUN', 'Hungary' ],
      'ID' => [ 'IDN', 'Indonesia' ],
      'IE' => [ 'IRL', 'Ireland' ],
      'IL' => [ 'ISR', 'Israel' ],
      'IM' => [ 'IMN', 'Isle of Man' ],
      'IN' => [ 'IND', 'India' ],
      'IO' => [ 'IOT', 'British Indian Ocean Territory' ],
      'IQ' => [ 'IRQ', 'Iraq' ],
      'IR' => [ 'IRN', 'Iran' ],
      'IS' => [ 'ISL', 'Iceland' ],
      'IT' => [ 'ITA', 'Italy' ],
      'JE' => [ 'JEY', 'Jersey' ],
      'JM' => [ 'JAM', 'Jamaica' ],
      'JO' => [ 'JOR', 'Jordan' ],
      'JP' => [ 'JPN', 'Japan' ],
      'KE' => [ 'KEN', 'Kenya' ],
      'KG' => [ 'KGZ', 'Kyrgyzstan' ],
      'KH' => [ 'KHM', 'Cambodia' ],
      'KI' => [ 'KIR', 'Kiribati' ],
      'KM' => [ 'COM', 'Comoros' ],
      'KN' => [ 'KNA', 'St. Kitts & Nevis' ],
      'KP' => [ 'PRK', 'North Korea' ],
      'KR' => [ 'KOR', 'South Korea' ],
      'KW' => [ 'KWT', 'Kuwait' ],
      'KY' => [ 'CYM', 'Cayman Islands' ],
      'KZ' => [ 'KAZ', 'Kazakhstan' ],
      'LA' => [ 'LAO', 'Laos' ],
      'LB' => [ 'LBN', 'Lebanon' ],
      'LC' => [ 'LCA', 'St. Lucia' ],
      'LI' => [ 'LIE', 'Liechtenstein' ],
      'LK' => [ 'LKA', 'Sri Lanka' ],
      'LR' => [ 'LBR', 'Liberia' ],
      'LS' => [ 'LSO', 'Lesotho' ],
      'LT' => [ 'LTU', 'Lithuania' ],
      'LU' => [ 'LUX', 'Luxembourg' ],
      'LV' => [ 'LVA', 'Latvia' ],
      'LY' => [ 'LBY', 'Libya' ],
      'MA' => [ 'MAR', 'Morocco' ],
      'MC' => [ 'MCO', 'Monaco' ],
      'MD' => [ 'MDA', 'Moldova' ],
      'ME' => [ 'MNE', 'Montenegro' ],
      'MF' => [ 'MAF', 'St. Martin' ],
      'MG' => [ 'MDG', 'Madagascar' ],
      'MH' => [ 'MHL', 'Marshall Islands' ],
      'MK' => [ 'MKD', 'North Macedonia' ],
      'ML' => [ 'MLI', 'Mali' ],
      'MM' => [ 'MMR', 'Myanmar (Burma)' ],
      'MN' => [ 'MNG', 'Mongolia' ],
      'MO' => [ 'MAC', 'Macao' ],
      'MP' => [ 'MNP', 'Northern Mariana Islands' ],
      'MQ' => [ 'MTQ', 'Martinique' ],
      'MR' => [ 'MRT', 'Mauritania' ],
      'MS' => [ 'MSR', 'Montserrat' ],
      'MT' => [ 'MLT', 'Malta' ],
      'MU' => [ 'MUS', 'Mauritius' ],
      'MV' => [ 'MDV', 'Maldives' ],
      'MW' => [ 'MWI', 'Malawi' ],
      'MX' => [ 'MEX', 'Mexico' ],
      'MY' => [ 'MYS', 'Malaysia' ],
      'MZ' => [ 'MOZ', 'Mozambique' ],
      'NA' => [ 'NAM', 'Namibia' ],
      'NC' => [ 'NCL', 'New Caledonia' ],
      'NE' => [ 'NER', 'Niger' ],
      'NF' => [ 'NFK', 'Norfolk Island' ],
      'NG' => [ 'NGA', 'Nigeria' ],
      'NI' => [ 'NIC', 'Nicaragua' ],
      'NL' => [ 'NLD', 'Netherlands' ],
      'NO' => [ 'NOR', 'Norway' ],
      'NP' => [ 'NPL', 'Nepal' ],
      'NR' => [ 'NRU', 'Nauru' ],
      'NU' => [ 'NIU', 'Niue' ],
      'NZ' => [ 'NZL', 'New Zealand' ],
      'OM' => [ 'OMN', 'Oman' ],
      'PA' => [ 'PAN', 'Panama' ],
      'PE' => [ 'PER', 'Peru' ],
      'PF' => [ 'PYF', 'French Polynesia' ],
      'PG' => [ 'PNG', 'Papua New Guinea' ],
      'PH' => [ 'PHL', 'Philippines' ],
      'PK' => [ 'PAK', 'Pakistan' ],
      'PL' => [ 'POL', 'Poland' ],
      'PM' => [ 'SPM', 'St. Pierre & Miquelon' ],
      'PN' => [ 'PCN', 'Pitcairn Islands' ],
      'PR' => [ 'PRI', 'Puerto Rico' ],
      'PS' => [ 'PSE', 'Palestinian Territories' ],
      'PT' => [ 'PRT', 'Portugal' ],
      'PW' => [ 'PLW', 'Palau' ],
      'PY' => [ 'PRY', 'Paraguay' ],
      'QA' => [ 'QAT', 'Qatar' ],
      'RE' => [ 'REU', 'Réunion' ],
      'RO' => [ 'ROU', 'Romania' ],
      'RS' => [ 'SRB', 'Serbia' ],
      'RU' => [ 'RUS', 'Russia' ],
      'RW' => [ 'RWA', 'Rwanda' ],
      'SA' => [ 'SAU', 'Saudi Arabia' ],
      'SB' => [ 'SLB', 'Solomon Islands' ],
      'SC' => [ 'SYC', 'Seychelles' ],
      'SD' => [ 'SDN', 'Sudan' ],
      'SE' => [ 'SWE', 'Sweden' ],
      'SG' => [ 'SGP', 'Singapore' ],
      'SH' => [ 'SHN', 'St. Helena' ],
      'SI' => [ 'SVN', 'Slovenia' ],
      'SJ' => [ 'SJM', 'Svalbard & Jan Mayen' ],
      'SK' => [ 'SVK', 'Slovakia' ],
      'SL' => [ 'SLE', 'Sierra Leone' ],
      'SM' => [ 'SMR', 'San Marino' ],
      'SN' => [ 'SEN', 'Senegal' ],
      'SO' => [ 'SOM', 'Somalia' ],
      'SR' => [ 'SUR', 'Suriname' ],
      'SS' => [ 'SSD', 'South Sudan' ],
      'ST' => [ 'STP', 'São Tomé & Príncipe' ],
      'SV' => [ 'SLV', 'El Salvador' ],
      'SX' => [ 'SXM', 'Sint Maarten' ],
      'SY' => [ 'SYR', 'Syria' ],
      'SZ' => [ 'SWZ', 'Eswatini' ],
      'TC' => [ 'TCA', 'Turks & Caicos Islands' ],
      'TD' => [ 'TCD', 'Chad' ],
      'TF' => [ 'ATF', 'French Southern Territories' ],
      'TG' => [ 'TGO', 'Togo' ],
      'TH' => [ 'THA', 'Thailand' ],
      'TJ' => [ 'TJK', 'Tajikistan' ],
      'TK' => [ 'TKL', 'Tokelau' ],
      'TL' => [ 'TLS', 'Timor-Leste' ],
      'TM' => [ 'TKM', 'Turkmenistan' ],
      'TN' => [ 'TUN', 'Tunisia' ],
      'TO' => [ 'TON', 'Tonga' ],
      'TR' => [ 'TUR', 'Türkiye' ],
      'TT' => [ 'TTO', 'Trinidad & Tobago' ],
      'TV' => [ 'TUV', 'Tuvalu' ],
      'TW' => [ 'TWN', 'Taiwan' ],
      'TZ' => [ 'TZA', 'Tanzania' ],
      'UA' => [ 'UKR', 'Ukraine' ],
      'UG' => [ 'UGA', 'Uganda' ],
      'UM' => [ 'UMI', 'U.S. Outlying Islands' ],
      'US' => [ 'USA', 'United States' ],
      'UY' => [ 'URY', 'Uruguay' ],
      'UZ' => [ 'UZB', 'Uzbekistan' ],
      'VA' => [ 'VAT', 'Vatican City' ],
      'VC' => [ 'VCT', 'St. Vincent & Grenadines' ],
      'VE' => [ 'VEN', 'Venezuela' ],
      'VG' => [ 'VGB', 'British Virgin Islands' ],
      'VI' => [ 'VIR', 'U.S. Virgin Islands' ],
      'VN' => [ 'VNM', 'Vietnam' ],
      'VU' => [ 'VUT', 'Vanuatu' ],
      'WF' => [ 'WLF', 'Wallis & Futuna' ],
      'WS' => [ 'WSM', 'Samoa' ],
      'YE' => [ 'YEM', 'Yemen' ],
      'YT' => [ 'MYT', 'Mayotte' ],
      'ZA' => [ 'ZAF', 'South Africa' ],
      'ZM' => [ 'ZMB', 'Zambia' ],
      'ZW' => [ 'ZWE', 'Zimbabwe' ],

    ];

    return $table;

  }

?>
