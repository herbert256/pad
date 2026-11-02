<?php

  // Merge up to 16 requested country/region calendars. Every original occurrence remains
  // a separate row with its source: a shared date is not one worldwide holiday.
  // Returns rows, or a validation message for the caller to report in its own context.
  function padHolidaysMerge ( $places, $from, $to, $lang = '' ) {

    $places = is_string ( $places ) ? explode ( ',', $places ) : $places;
    if ( ! is_array ( $places ) || ! $places || count ( $places ) > 16 )
      return 'merged holidays need one to sixteen country or region codes';
    if ( ! is_int ( $from ) || ! is_int ( $to ) || $from > $to || $to - $from > 36600
         || $from < padHolidaysDay ( padHolidaysFirst, 1, 1 ) || $to > padHolidaysDay ( padHolidaysLast, 12, 31 ) )
      return 'merged holidays need an ordered date span of at most one hundred years, from 1583 to 9999';
    if ( ! is_string ( $lang ) ) return 'the merged holiday language must be text';
    $chosen = [];
    foreach ( $places as $place ) {
      $parsed = padHolidaysPlace ( $place );
      if ( is_string ( $parsed ) ) return $parsed;
      [ $country, $region ] = $parsed;
      $code = $country . ( $region === '' ? '' : '-' . $region );
      $rules = padHolidaysCountry ( $country );
      $known = padHolidaysKnown ( $country, $from, $to );
      if ( $known !== '' ) return $known;
      if ( $lang !== '' && ! in_array ( $lang, $rules ['langs'], TRUE ) )
        return "the local holiday names of $country are in " . implode ( ', ', $rules ['langs'] ) . "; not $lang";
      $chosen [$code] = [ $country, $region, $rules ];
    }
    ksort ( $chosen );
    $rows = [];
    foreach ( $chosen as $code => [ $country, $region, $rules ] ) {
      $language = $lang ?: $rules ['langs'] [0];
      foreach ( padHolidaysRange ( $country, $region, $from, $to, $language ) as $order => $row )
        $rows [] = $row + [ 'country' => $country, 'countryName' => $rules ['name'],
          'place' => $code, 'sourceRegion' => $region, 'sourceRegionName' => $rules ['regions'] [$region] ?? '',
          'language' => $language, '_order' => $order ];
    }
    usort ( $rows, fn ( $a, $b ) => [ $a ['date'], $a ['place'], $a ['_order'] ] <=> [ $b ['date'], $b ['place'], $b ['_order'] ] );
    foreach ( $rows as &$row ) unset ( $row ['_order'] );
    unset ( $row );
    return $rows;

  }

  function padHolidaysMergedTable ( $rows, $caption ) {

    $h = fn ( $text ) => htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    $html = padHolidaysStyle () . '<table class="pad-holidays pad-holidays-merged"><caption>' . $h ( $caption )
      . '</caption><thead><tr><th scope="col">Date</th><th scope="col">Calendar</th><th scope="col">Holiday</th></tr></thead><tbody>';
    foreach ( $rows as $row ) {
      $place = $row ['countryName'] . ( $row ['sourceRegionName'] === '' ? '' : ', ' . $row ['sourceRegionName'] );
      $html .= '<tr' . ( $row ['type'] === 'public' ? '' : ' class="observance"' ) . '><td><time datetime="' . $h ( $row ['date'] ) . '">' . $h ( $row ['date'] )
        . '</time></td><td>' . $h ( $place ) . '</td><td>' . $h ( $row ['name'] )
        . ( $row ['local'] === $row ['name'] ? '' : ' <span lang="' . $h ( $row ['language'] ) . '">' . $h ( $row ['local'] ) . '</span>' )
        . ( $row ['type'] === 'public' ? '' : ' <small>(no day off)</small>' ) . '</td></tr>';
    }
    return $html . '</tbody></table>';

  }

?>
