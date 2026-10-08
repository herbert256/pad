<?php

  // {country 'NL'} - a country's flag, from its ISO 3166-1 code: lib/country.php. The code
  // is alpha-2 (NL) or alpha-3 (NLD), in any case, or the English name. Alone the flag is
  // an image named by the country for a screen reader; name writes the name beside it, and
  // the flag is then only decoration. A code that is no country is an error.

  $padCountryFound = padCountry ( $padParm );

  if ( $padCountryFound === NULL ) {

    if ( $padCheckSyntax ) {
      $padCountryNear = padCountrySuggest ( $padParm );
      padError ( "there is no country '" . padMakeSafe ( $padParm, 40 ) . "' - an ISO 3166 code like NL or NLD, or the English name"
               . ( $padCountryNear !== '' ? " - did you mean '$padCountryNear', " . padCountryTable () [$padCountryNear] [1] . '?' : '' ) );
    }

    return padChartAttr ( $padParm );

  }

  list ( $padCountryTwo, $padCountryThree, $padCountryName ) = $padCountryFound;

  if ( padTagParm ( 'name', FALSE ) )
    return '<span class="pad-country"><span class="pad-country-flag" aria-hidden="true">' . padCountryFlag ( $padCountryTwo )
         . '</span> <span class="pad-country-name">' . padChartAttr ( $padCountryName ) . '</span></span>';

  return '<span class="pad-country" role="img" aria-label="' . padChartAttr ( $padCountryName ) . '" title="'
       . padChartAttr ( $padCountryName ) . '">' . padCountryFlag ( $padCountryTwo ) . '</span>';

?>
