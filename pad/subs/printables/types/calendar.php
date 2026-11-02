<?php

  // {printable:calendar year=2027, holidays='nl'/} - a wall calendar, a month to a sheet: the
  // month and its year over a grid of its weeks, a column a weekday, a box to write in for
  // every day, its number in the corner - the public holidays of holidays= marked, their
  // names under the number, Sundays in the colour of the month. year= its twelve months, the
  // year this is the default; month='2027-09' from that month on, one month unless months=
  // (1 to 60) says how many - an academic year is month='2026-09', months=12. sunday starts
  // the weeks on Sunday instead of Monday, weeks puts the ISO 8601 week numbers in a column
  // before them; holidays= a country of the holidays subsystem, 'nl', or with its region,
  // 'de-by' - a public holiday red, an observance grey - the names in the language of the
  // page's locale when the country speaks it, as the month and the weekdays are; color= the
  // month and the marked days (#c0392b); paper= a4, a3, a5 or letter, landscape. The content
  // between the tags stands over every month - a picture, a line of text - with padding=,
  // size= (12 pt) and align= (center); with data= a row is a month, the rows taken in turn.
  //
  //   {printable:calendar year=2027, holidays='nl', weeks/}
  //   {printable:calendar month='2026-09', months=12, data='photos'}<h2>{$caption}</h2>{/printable:calendar}

  include_once PAD . 'subs/calendars/_lib/calendar.php';

  $padCalYear   = padTagParm ( 'year', NULL );
  $padCalMonth  = padTagParm ( 'month', NULL );
  $padCalText   = fn ( $value ) => is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';

  if ( $padCalYear !== NULL and $padCalMonth !== NULL and $GLOBALS ['padCheckSyntax'] )
    padError ( 'the ' . padPrintableWhat () . ' has year= and month= - the year for its twelve months, month= for the month it starts with' );

  if ( $padCalMonth !== NULL ) {
    $padCalFirst = padCalendarMonth ( $padCalText ( $padCalMonth ) );
    if ( $padCalFirst === NULL and $GLOBALS ['padCheckSyntax'] )
      padError ( 'the ' . padPrintableWhat () . " has no month '" . padMakeSafe ( $padCalText ( $padCalMonth ), 20 ) . "' - a month written 2027-03" );
  } else {
    $padCalFirst = preg_match ( '/^\d{4}$/D', $padCalText ( $padCalYear ?? padNow ( 'Y' ) ) ) ? $padCalText ( $padCalYear ?? padNow ( 'Y' ) ) . '-01' : NULL;
    if ( $padCalFirst === NULL and $GLOBALS ['padCheckSyntax'] )
      padError ( 'the ' . padPrintableWhat () . " has no year '" . padMakeSafe ( $padCalText ( $padCalYear ), 20 ) . "' - a year written 2027" );
  }

  $padCalFirst ??= padNow ( 'Y-m' );
  $padCalCount   = padPrintableInt ( 'months', $padCalMonth !== NULL ? 1 : 12, 1, 60 );
  $padCalCopies  = padPrintableInt ( 'copies', 1, 1, 1000 );
  $padCalSunday  = padPrintableFlag ( 'sunday' );
  $padCalWeeks   = padPrintableFlag ( 'weeks' );
  $padCalColor   = padPrintableColor ( 'color', '#c0392b' );
  $padCalBox     = padPrintableBox ( [ 'pad' => 2, 'size' => 12, 'align' => 'center', 'valign' => 'middle' ] );

  list ( $padCalName, $padCalPaper ) = padPrintablePaper ( [ 'a4' => PAD_PRINTABLE_PAPERS ['a4'], 'a3' => PAD_PRINTABLE_PAPERS ['a3'],
                                                             'a5' => PAD_PRINTABLE_PAPERS ['a5'], 'letter' => PAD_PRINTABLE_PAPERS ['letter'] ], 'a4' );

  // The months, from the first: [ 'YYYY-MM', its first day ] - and the holidays of all of them,
  // the week before the first and after the last too, a grid shows days of those.

  $padCalUtc    = new DateTimeZone ( 'UTC' );
  $padCalMonths = [];

  for ( $i = 0; $i < $padCalCount; $i++ ) {
    $padCalDay = ( new DateTimeImmutable ( "$padCalFirst-01", $padCalUtc ) )->modify ( "+$i month" );
    $padCalMonths [] = [ $padCalDay->format ( 'Y-m' ), $padCalDay ];
  }

  // The holidays of holidays=, as the holidays subsystem has them (subs/holidays/): a day of
  // more than one gets their names together, and is public when one of them is. The names are
  // the local ones when the page's locale is in a language of the country - Koningsdag - as
  // {calendar:month} has them, else the English ones. A month year= or month= fixes in a year the
  // country's file does not know (New Zealand's after 2052) is an error under the strict
  // check, as for {calendar:month} and {pad:holidays}; this year, which the template does not fix, is
  // shown with only the days inside those years marked - where the rules' rows were marked
  // without a word, New Zealand's 2060 with King's Birthday and no Matariki.

  $padCalHolidays = [];
  $padCalPlace    = padPrintableText ( 'holidays', '' );

  if ( $padCalPlace !== '' ) {

    include_once PAD . 'subs/holidays/_lib/holidays.php';

    $padCalWhere = padHolidaysPlace ( $padCalPlace );

    if ( is_string ( $padCalWhere ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( 'the ' . padPrintableWhat () . "'s holidays= - $padCalWhere" );
    } else {

      $padCalLang  = strtolower ( substr ( padLocaleOf ( '' ), 0, 2 ) );
      $padCalLocal = in_array ( $padCalLang, padHolidaysCountry ( $padCalWhere [0] ) ['langs'], TRUE );
      $padCalEnd   = end ( $padCalMonths ) [1]->modify ( 'last day of this month' );
      $padCalStart = padHolidaysDay ( ...array_map ( 'intval', explode ( '-', $padCalMonths [0] [1]->format ( 'Y-n-j' ) ) ) );
      $padCalLast  = padHolidaysDay ( ...array_map ( 'intval', explode ( '-', $padCalEnd->format ( 'Y-n-j' ) ) ) );
      $padCalFrom  = max ( padHolidaysDay ( padHolidaysFirst, 1, 1 ), $padCalStart - 7 );
      $padCalTo    = min ( padHolidaysDay ( padHolidaysLast, 12, 31 ), $padCalLast + 7 );
      $padCalKnown = padHolidaysKnown ( $padCalWhere [0], $padCalStart, $padCalLast );
      $padCalFixed = ( $padCalYear !== NULL or $padCalMonth !== NULL );

      if ( $padCalKnown !== '' and $padCalFixed and $GLOBALS ['padCheckSyntax'] )
        padError ( 'the ' . padPrintableWhat () . "'s holidays= - $padCalKnown" );

      if ( $padCalKnown !== '' and ! $padCalFixed ) {
        [ $padCalA, $padCalB ] = padHolidaysCountry ( $padCalWhere [0] ) ['years'];
        $padCalFrom = max ( $padCalFrom, padHolidaysDay ( $padCalA, 1, 1 ) );
        $padCalTo   = min ( $padCalTo,   padHolidaysDay ( $padCalB, 12, 31 ) );
      }

      foreach ( ( $padCalFrom <= $padCalTo ) ? padHolidaysRange ( $padCalWhere [0], $padCalWhere [1], $padCalFrom, $padCalTo, $padCalLang ) : [] as $padCalRow ) {
        $padCalDay = $padCalHolidays [ $padCalRow ['date'] ] ?? [ 'names' => [], 'public' => FALSE ];
        $padCalDay ['names'] [] = $padCalLocal ? $padCalRow ['local'] : $padCalRow ['name'];
        $padCalDay ['public']   = $padCalDay ['public'] || $padCalRow ['type'] == 'public';
        $padCalHolidays [ $padCalRow ['date'] ] = $padCalDay;
      }

      foreach ( $padCalHolidays as $padCalDate => $padCalDay )
        $padCalHolidays [$padCalDate] = [ 'name' => implode ( ', ', $padCalDay ['names'] ), 'public' => $padCalDay ['public'] ];

    }

  }

  return padPrintablePlan ( [

    'name'      => [ 'calendar month', 'calendar months' ],
    'paper'     => $padCalPaper,
    'paperName' => $padCalName,
    'count'     => $padCalCount,
    'exact'     => TRUE,

    'item' => function ( $cell, $parts, $n ) use ( $padCalPaper, $padCalMonths, $padCalCopies, $padCalSunday, $padCalWeeks, $padCalColor, $padCalBox, $padCalHolidays ) {

      list ( $month, $first ) = $padCalMonths [ intdiv ( max ( 0, $n ), $padCalCopies ) % count ( $padCalMonths ) ];
      list ( $w, $h ) = $padCalPaper;

      // Every size from the paper's short side, A4 the measure: a margin of 12 mm on A4, the
      // month in 22 pt, the number of a day in 12 - on A5 no text smaller than can be read,
      // a name of a holiday 5.5 pt at the least.

      $k      = min ( $w, $h ) / 210;
      $m      = round ( 12 * $k, 3 );
      $text   = $parts [''] ?? '';
      $top    = ( $text !== '' ) ? round ( ( $h - 2 * $m ) * ( $h > $w ? 0.38 : 0.26 ), 3 ) : 0;
      $title  = round ( 14 * $k, 3 );
      $head   = round ( 7 * $k, 3 );
      $wcol   = $padCalWeeks ? round ( 8 * $k, 3 ) : 0;
      $x0     = $m + $wcol;
      $y0     = round ( $m + $top + $title + $head, 3 );
      $cw     = ( $w - 2 * $m - $wcol ) / 7;
      $shift  = $padCalSunday ? (int) $first->format ( 'w' ) : (int) $first->format ( 'N' ) - 1;
      $rows   = (int) ceil ( ( $shift + (int) $first->format ( 't' ) ) / 7 );
      $ch     = ( $h - $m - $y0 ) / $rows;
      $start  = $first->modify ( "-$shift days" );
      $line   = [ 'stroke' => '#8a8984', 'width' => 0.5 ];
      $at     = fn ( $n ) => round ( $n, 3 );
      $draw   = [];

      if ( $text !== '' )
        $draw [] = [ 'box', $m, $m, $at ( $w - 2 * $m ), $at ( $top - 2 * $k ), $text, $padCalBox ];

      $draw [] = [ 'box', $m, $at ( $m + $top ), $at ( $w - 2 * $m ), $title, '<b>' . padWidgetAttr ( padCalendarWord ( $first, 'LLLL uuuu', 'F Y' ) ) . '</b>',
                   [ 'pad' => 0, 'size' => $at ( max ( 14, 22 * $k ) ), 'valign' => 'middle', 'color' => $padCalColor ] ];

      // The weekdays over the columns, the week numbers before the rows.

      for ( $c = 0; $c < 7; $c++ )
        $draw [] = [ 'box', $at ( $x0 + $c * $cw ), $at ( $m + $top + $title ), $at ( $cw ), $head,
                     padWidgetAttr ( padCalendarWord ( $start->modify ( "+$c day" ), 'EEE', 'D' ) ),
                     [ 'pad' => 0.5, 'size' => $at ( max ( 7, 9 * $k ) ), 'align' => 'center', 'valign' => 'middle', 'color' => '#555555' ] ];

      for ( $r = 0; $r < $rows; $r++ ) {

        if ( $padCalWeeks )
          $draw [] = [ 'box', $m, $at ( $y0 + $r * $ch ), $wcol, $at ( $ch ),
                       (string) (int) $start->modify ( '+' . ( $r * 7 + ( $padCalSunday ? 1 : 0 ) ) . ' day' )->format ( 'W' ),
                       [ 'pad' => 0.5, 'size' => $at ( max ( 6, 7.5 * $k ) ), 'align' => 'center', 'valign' => 'top', 'color' => '#8a8984' ] ];

        for ( $c = 0; $c < 7; $c++ ) {

          $day  = $start->modify ( '+' . ( $r * 7 + $c ) . ' day' );
          $x    = $at ( $x0 + $c * $cw );
          $y    = $at ( $y0 + $r * $ch );

          if ( $day->format ( 'Y-m' ) != $month ) {
            $draw [] = [ 'rect', $x, $y, $at ( $cw ), $at ( $ch ), [ 'fill' => '#f1f0eb' ] ];
            continue;
          }

          $mark = $padCalHolidays [ $day->format ( 'Y-m-d' ) ] ?? NULL;
          $red  = ( $day->format ( 'N' ) == 7 or ( $mark ['public'] ?? FALSE ) );

          $draw [] = [ 'box', $x, $y, $at ( $cw ), $at ( 8 * $k ), '<b>' . $day->format ( 'j' ) . '</b>',
                       [ 'pad' => $at ( 1.4 * $k ), 'size' => $at ( max ( 9, 12 * $k ) ), 'color' => $red ? $padCalColor : '#1f1f1d' ] ];

          if ( $mark )
            $draw [] = [ 'box', $x, $at ( $y + 7.5 * $k ), $at ( $cw ), $at ( $ch - 7.5 * $k ), padWidgetAttr ( $mark ['name'] ),
                         [ 'pad' => $at ( 1.4 * $k ), 'size' => $at ( max ( 5.5, 6.5 * $k ) ), 'color' => $mark ['public'] ? $padCalColor : '#6f6e69' ] ];

        }

      }

      // The grid: a line round it and between the rows and the columns.

      for ( $c = 0; $c <= 7; $c++ )
        $draw [] = [ 'line', $at ( $x0 + $c * $cw ), $y0, $at ( $x0 + $c * $cw ), $at ( $h - $m ), $line ];

      for ( $r = 0; $r <= $rows; $r++ )
        $draw [] = [ 'line', $x0, $at ( $y0 + $r * $ch ), $at ( $w - $m ), $at ( $y0 + $r * $ch ), $line ];

      return $draw;

    },

  ] );

?>
