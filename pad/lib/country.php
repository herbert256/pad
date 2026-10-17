<?php

  // padCountry - [ alpha-2, alpha-3, name ] of an ISO 3166-1 alpha-2 code, an alpha-3 code
  // or an English name, any case; NULL when it is none - for a page's PHP as well as for
  // the {country} tag. The table of the countries and the rest of the tag are
  // tags/_lib/country.php, which the call includes when no {country} of the page has yet.

  function padCountry ( $code ) {

    include_once PAD . 'tags/_lib/country.php';

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

?>
