<?php

  // The body of {holidays}: the public holidays of a country, a file of subs/holidays/types/
  // (_lib/holidays.php reads it). The first parameter is the country, its ISO 3166-1 alpha-2
  // code, with a region after it as ISO 3166-2 writes it - 'nl', 'de-by', 'gb-sct' - or the
  // region as region='by'. year= is the year (this year by default); from= and to= ask for
  // the days between two dates instead - from today and a year on when only one is given.
  // lang= picks the language of the local names of a country of more than one (be: nl, fr,
  // de). As a pair it hands over the rows - date, name, local, type, region, substitute -
  // and the handling options work on them; as a single tag it answers them as a table.
  //
  //   {holidays 'nl', year=2026}{$date} {$name}{/holidays}
  //   {holidays 'de-by', year=2026, where='$type eq "public"'/}

  include_once PAD . 'subs/holidays/_lib/holidays.php';

  $padHolidaysPlace = padHolidaysPlace ( padUnprotect ( (string) $padParm ), padUnprotect ( (string) padTagParm ( 'region' ) ) );

  if ( is_string ( $padHolidaysPlace ) ) {
    if ( $padCheckSyntax )
      padError ( "{holidays}: $padHolidaysPlace" );
    return [];
  }

  [ $padHolidaysCountry, $padHolidaysRegion ] = $padHolidaysPlace;

  // The kind used goes on the xref record, which the reference application shows: the pages
  // that list the holidays of each country.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'holidays', $padHolidaysCountry );

  $padHolidaysRules = padHolidaysCountry ( $padHolidaysCountry );
  $padHolidaysYear  = padTagParm ( 'year', NULL );
  $padHolidaysFrom  = padTagParm ( 'from', NULL );
  $padHolidaysTo    = padTagParm ( 'to', NULL );
  $padHolidaysLang  = strtolower ( trim ( (string) padTagParm ( 'lang', $padHolidaysRules ['langs'] [0] ) ) );

  if ( ! in_array ( $padHolidaysLang, $padHolidaysRules ['langs'], TRUE ) ) {
    if ( $padCheckSyntax )
      padError ( "{holidays}: the names of $padHolidaysCountry are in " . implode ( ', ', $padHolidaysRules ['langs'] ) . " - not lang='" . padMakeSafe ( $padHolidaysLang, 20 ) . "'" );
    $padHolidaysLang = $padHolidaysRules ['langs'] [0];
  }

  if ( $padHolidaysFrom !== NULL or $padHolidaysTo !== NULL ) {

    if ( $padHolidaysYear !== NULL and $padCheckSyntax )
      padError ( "{holidays}: year= or from= and to=, not both" );

    $padHolidaysA = ( $padHolidaysFrom === NULL ) ? padHolidaysValue ( padNow ( 'Y-m-d' ) ) : padHolidaysValue ( padUnprotect ( $padHolidaysFrom ) );
    $padHolidaysB = ( $padHolidaysTo   === NULL ) ? NULL : padHolidaysValue ( padUnprotect ( $padHolidaysTo ) );

    if ( $padHolidaysA !== NULL and $padHolidaysB === NULL and $padHolidaysTo === NULL ) {
      [ $padHolidaysY, $padHolidaysM, $padHolidaysD ] = padHolidaysCivil ( $padHolidaysA );
      $padHolidaysB = padHolidaysDay ( $padHolidaysY + 1, $padHolidaysM, $padHolidaysD ) - 1;
    }

    if ( $padHolidaysA === NULL or $padHolidaysB === NULL ) {
      if ( $padCheckSyntax )
        padError ( "{holidays}: from= and to= are dates - 2026-01-01, today, +3 months" );
      return [];
    }

    if ( $padHolidaysB - $padHolidaysA > 366 * 100 or $padHolidaysA < padHolidaysDay ( padHolidaysFirst, 1, 1 ) or $padHolidaysB > padHolidaysDay ( padHolidaysLast, 12, 31 ) ) {
      if ( $padCheckSyntax )
        padError ( "{holidays}: from= and to= span a hundred years at most, from 1583 to 9999" );
      return [];
    }

    // A span the wrong way round is a wrong span as the others are: it answered no rows
    // without a word, and the single tag a table captioned with the dates reversed.

    if ( $padHolidaysB < $padHolidaysA ) {
      if ( $padCheckSyntax )
        padError ( '{holidays}: to= ' . padHolidaysDate ( $padHolidaysB ) . ' comes before from= ' . padHolidaysDate ( $padHolidaysA )
                   . ( $padHolidaysFrom === NULL ? ' - today, when from= is not given' : '' ) );
      return [];
    }

    $padHolidaysCaption = $padHolidaysRules ['name'] . ' ' . padHolidaysDate ( $padHolidaysA ) . ' - ' . padHolidaysDate ( $padHolidaysB );

  } else {

    $padHolidaysYear = $padHolidaysYear ?? padNow ( 'Y' );

    if ( ! ctype_digit ( trim ( (string) $padHolidaysYear ) ) or $padHolidaysYear < padHolidaysFirst or $padHolidaysYear > padHolidaysLast ) {
      if ( $padCheckSyntax )
        padError ( "{holidays}: year='" . padMakeSafe ( $padHolidaysYear, 20 ) . "' - a year from 1583 to 9999" );
      $padHolidaysYear = padNow ( 'Y' );
    }

    $padHolidaysA       = padHolidaysDay ( (int) $padHolidaysYear, 1, 1 );
    $padHolidaysB       = padHolidaysDay ( (int) $padHolidaysYear, 12, 31 );
    $padHolidaysCaption = $padHolidaysRules ['name'] . ' ' . (int) $padHolidaysYear;

  }

  if ( $padHolidaysRegion !== '' )
    $padHolidaysCaption = $padHolidaysRules ['name'] . ', ' . $padHolidaysRules ['regions'] [$padHolidaysRegion] . substr ( $padHolidaysCaption, strlen ( $padHolidaysRules ['name'] ) );

  $padHolidaysRows = padHolidaysRange ( $padHolidaysCountry, $padHolidaysRegion, $padHolidaysA, $padHolidaysB, $padHolidaysLang );

  if ( $padPair [$pad] )
    return $padHolidaysRows;

  $padData [$pad] = padDefaultData ();

  return padHolidaysTable ( $padHolidaysRows, $padHolidaysCaption, $padHolidaysLang );

?>
