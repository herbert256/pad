<?php

  // {calendar} - a month as a calendar, lib/calendar.php. The month is the first parameter
  // ('2026-10'), else the request value named by query= ('month') - the links to the months
  // around it set that value - else this month. data= gives events - a {data} store, a page
  // array or a _data file, as for {chart} - date= names their date field and title= their
  // text, else the first field that reads as a date and the first other text; link= names a
  // field with a link for the event. sunday starts the weeks on Sunday instead of Monday.
  //
  // Written as a single tag it answers a table: the month with links to its neighbours, a
  // column per weekday, a cell per day with its events. Written as a pair it hands the
  // weeks over as rows instead - week, and days: date, day, weekday, month, other, today,
  // weekend, count, events and class - for markup of the template's own.

  $padCalendarQuery = trim ( (string) padTagParm ( 'query', 'month' ) );
  $padCalendarAsked = padCalendarMonth ( $_GET [$padCalendarQuery] ?? '' );
  $padCalendarMonth = trim ( (string) $padParm ) !== '' ? padCalendarMonth ( $padParm ) : ( $padCalendarAsked ?? padNow ( 'Y-m' ) );

  if ( $padCalendarMonth === NULL ) {
    if ( $padCheckSyntax )
      padError ( "the calendar has no month '" . padMakeSafe ( $padParm, 20 ) . "' - write it as 2026-10" );
    $padCalendarMonth = padNow ( 'Y-m' );
  }

  $padCalendarRows = isset ( $padPrm [$pad] ['data'] ) ? padChartRows ( '', 'calendar' ) : [];
  $padData [$pad]  = padDefaultData ();

  list ( $padCalendarNumbers, $padCalendarNames ) = padChartGuess ( $padCalendarRows );

  $padCalendarDates = $padCalendarTexts = [];

  foreach ( $padCalendarNames as $padCalendarName ) {
    $padCalendarFirst = padChartRow ( reset ( $padCalendarRows ) );
    if ( padChartStamp ( $padCalendarFirst [$padCalendarName] ) !== FALSE )
      $padCalendarDates [] = $padCalendarName;
    else
      $padCalendarTexts [] = $padCalendarName;
  }

  $padCalendarUsed  = [];
  $padCalendarDate  = padChartPick ( 'date',  $padCalendarDates, $padCalendarUsed );
  $padCalendarTitle = padChartPick ( 'title', $padCalendarTexts, $padCalendarUsed );
  $padCalendarLink  = (string) padTagParm ( 'link' );

  $padCalendarWeeks = padCalendarWeeks ( $padCalendarMonth, $padCalendarRows, $padCalendarDate, (bool) padTagParm ( 'sunday', FALSE ) );

  if ( $padPair [$pad] )
    return $padCalendarWeeks;

  return padCalendarHtml ( $padCalendarMonth, $padCalendarWeeks, $padCalendarTitle, $padCalendarLink, $padCalendarQuery );

?>
