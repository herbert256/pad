<?php

  // month - a month with events, holidays and links to its neighbours. The month is the first parameter
  // ('2026-10'), else the request value named by query= ('month') - the links to the months
  // around it set that value - else this month. data= gives events - a {format:auto} store, a page
  // array or a _data file, as for {chart:bar} - date= names their date field and title= their
  // text, else the first field that reads as a date and the first other text; link= names a
  // field with a link for the event. sunday starts the weeks on Sunday instead of Monday.
  // holidays= marks the public holidays of a country, 'nl' or with its region 'de-by' (the
  // holidays subsystem, subs/holidays/): the day gets the class holiday - observance for a
  // day that is no day off - and the holiday's name under its number, in the language of
  // the request's locale when the country speaks it, else in English.
  //
  // Written as a single tag it answers a table: the month - with links to its neighbours
  // when no parameter fixes it, or the parameter holds the month the request asked for - a
  // column per weekday, a cell per day with its events. Written as a pair it hands the
  // weeks over as rows instead - week, and days: date, day, weekday, month, other, today,
  // weekend, count, events, holiday (with holidays= only: its name, or '') and class - for
  // markup of the template's own.

  $padCalendarQuery = trim ( (string) padTagParm ( 'query', 'month' ) );
  $padCalendarAsked = padCalendarMonth ( $_GET [$padCalendarQuery] ?? '' );
  $padCalendarGiven = ! is_scalar ( $padParm ) || trim ( (string) $padParm ) !== '';
  $padCalendarMonth = $padCalendarGiven ? padCalendarMonth ( $padParm ) : ( $padCalendarAsked ?? padNow ( 'Y-m' ) );

  if ( $padCalendarMonth === NULL ) {
    if ( $padCheckSyntax )
      padError ( "the calendar has no month '" . padMakeSafe ( is_scalar ( $padParm ) ? $padParm : 'list', 20 ) . "' - write it as 2026-10" );
    $padCalendarMonth = padNow ( 'Y-m' );
  }

  $padCalendarRows = isset ( $padPrm [$pad] ['data'] ) ? padChartRows ( '', 'calendar' ) : [];
  $padData [$pad]  = padDefaultData ();

  [ $padCalendarDate, $padCalendarTitle, $padCalendarLink ] = padCalendarFields ( $padCalendarRows );

  // A month no parameter fixes, or one that holds the month the request asked for, is the
  // visitor's: the links stay for it, and holidays= marks only the days its country's file
  // knows instead of reporting the year.

  $padCalendarAskedFor = ! $padCalendarGiven || $padCalendarMonth === $padCalendarAsked;

  $padCalendarHolidays = padCalendarHolidays ( padUnprotect ( (string) padTagParm ( 'holidays' ) ), $padCalendarMonth, $padCalendarAskedFor );

  $padCalendarWeeks = padCalendarWeeks ( $padCalendarMonth, $padCalendarRows, $padCalendarDate, (bool) padTagParm ( 'sunday', FALSE ), $padCalendarHolidays );

  if ( $padPair [$pad] )
    return $padCalendarWeeks;

  // The links stay for a parameter that holds the month the request asked for - {calendar:month
  // $month}, the request value the links set - as following them changes it: dropped for
  // every parameter, such a calendar could no longer leave the month it showed.

  return padCalendarHtml ( $padCalendarMonth, $padCalendarWeeks, $padCalendarTitle, $padCalendarLink, $padCalendarQuery, $padCalendarAskedFor );

?>
