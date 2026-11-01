<?php

  // A month as a calendar - the {calendar} tag.
  //
  //   {calendar}                                            this month, a table
  //   {calendar '2026-10', data='events', date='when', title='what'}
  //   {calendar data='events'} <tr>{days}<td>{$day}</td>{/days}</tr> {/calendar}
  //
  // padCalendarWeeks   the weeks of a month as rows - week and days - each day a row of its
  //                    own: date, day, weekday, month, other, today, weekend, count, events
  //                    and class; the events are the data rows of that date
  // padCalendarHtml    those weeks as a table with the month and its neighbours above
  // padCalendarHolidays the public holidays of holidays= around the month, by date
  //
  // The month is the first parameter, else the request value named by query= (month), else
  // the month of today - padNow, so padNowFreeze holds it still. A week starts on Monday,
  // or on Sunday with sunday. padCalendarMonth, which reads a month and which a page's PHP
  // uses as well, is in lib/calendar.php.

  // The rows and their dates are read as {chart} reads them, subs/charts/_lib/chart.php.

  include_once PAD . 'subs/charts/_lib/chart.php';

  // A day has the field holiday only on a calendar of holidays= ($holidays an array, maybe
  // empty): handed to every day of every calendar it hid a page's own $holiday inside
  // {days} - a calendar without holidays= gave it '' on every day.

  function padCalendarWeeks ( $month, $rows, $dField, $sunday, $holidays = NULL ) {

    $utc    = new DateTimeZone ( 'UTC' );
    $first  = new DateTimeImmutable ( "$month-01", $utc );
    $last   = $first->modify ( 'last day of this month' );
    $shift  = $sunday ? (int) $first->format ( 'w' ) : (int) $first->format ( 'N' ) - 1;
    $start  = $first->modify ( "-$shift days" );
    $today  = padNow ( 'Y-m-d' );
    $events = [];

    foreach ( (array) $rows as $row ) {
      $row   = padChartRow ( $row );
      $stamp = padChartStamp ( padChartText ( $row, $dField ) );
      if ( $stamp !== FALSE )
        $events [ gmdate ( 'Y-m-d', $stamp ) ] [] = $row;
    }

    $weeks = [];

    for ( $day = $start; $day <= $last or count ( end ( $weeks ) ['days'] ?? [] ) < 7; $day = $day->modify ( '+1 day' ) ) {

      if ( ! $weeks or count ( end ( $weeks ) ['days'] ) == 7 )
        $weeks [] = [ 'week' => (int) $day->modify ( $sunday ? '+1 day' : '+0 day' )->format ( 'W' ), 'days' => [] ];

      $date    = $day->format ( 'Y-m-d' );
      $other   = $day->format ( 'Y-m' ) != $month;
      $weekend = $day->format ( 'N' ) >= 6;
      $list    = $events [$date] ?? [];
      $holiday = $holidays [$date] ?? NULL;
      $class   = trim ( ( $other ? ' other' : '' ) . ( $date == $today ? ' today' : '' ) . ( $weekend ? ' weekend' : '' ) . ( $list ? ' events' : '' )
                        . ( $holiday ? ' ' . $holiday ['class'] : '' ) );

      $weeks [ count ( $weeks ) - 1 ] ['days'] [] = [
        'date'    => $date,
        'day'     => (int) $day->format ( 'j' ),
        'weekday' => padCalendarWord ( $day, 'EEE', 'D' ),
        'month'   => $day->format ( 'Y-m' ),
        'other'   => $other,
        'today'   => $date == $today,
        'weekend' => $weekend,
        'count'   => count ( $list ),
        'events'  => $list
      ] + ( is_array ( $holidays ) ? [ 'holiday' => $holiday ['name'] ?? '' ] : [] ) + [
        'class'   => $class
      ];

    }

    return $weeks;

  }

  // The table: a caption with the month and links to the months around it, a header of
  // weekdays, a cell per day with its events as a list - their title= field, each a link
  // when link= names a field that holds one.

  // A month or a weekday in the locale of the request, as localDate writes it - the link
  // labels came from the locale's catalog while a Dutch page said "October 2026" and "Mon
  // Tue". An English locale, and a server without intl, write PHP's own English names.
  //
  // The calendar is Gregorian back to year 0, as PHP's dates and the grid are, and the year
  // is the grid's (uuuu): ICU's Gregorian calendar turns Julian before October 1582, and a
  // grid of 1582-10 was captioned september 1582, one of 0000-01 januari 1 - the year 1 BC.

  function padCalendarWord ( $day, $pattern, $format ) {

    static $formats = [];

    $locale = padLocaleOf ( '' );

    if ( ! class_exists ( 'IntlDateFormatter' ) or preg_match ( '/^en(?:[_-]|$)/i', $locale ) )
      return $day->format ( $format );

    if ( ! array_key_exists ( "$locale $pattern", $formats ) )
      try {
        $calendar = new IntlGregorianCalendar ( 'UTC', $locale );
        $calendar->setGregorianChange ( -1.0E300 );
        $formats ["$locale $pattern"] = new IntlDateFormatter ( $locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE,
                                                                'UTC', $calendar, $pattern );
      } catch ( Throwable $e ) {
        $formats ["$locale $pattern"] = NULL;
      }

    $word = $formats ["$locale $pattern"] ? $formats ["$locale $pattern"]->format ( $day ) : FALSE;

    return ( $word === FALSE ) ? $day->format ( $format ) : $word;

  }

  // A calendar whose month the parameter fixes has no links to the months around it: the
  // parameter wins over the request value, and following them showed the same month again.
  // Nor is there a link past the months it can show - 10000-01, -0001-12 - which it refused
  // and showed this month instead.

  function padCalendarHtml ( $month, $weeks, $tField, $lField, $query, $links = TRUE ) {

    $catalog = padTransCatalog ();
    $h       = fn ( $t ) => htmlspecialchars ( padUnprotectQuotes ( (string) $t ), ENT_QUOTES, 'UTF-8' );
    $first   = new DateTimeImmutable ( "$month-01", new DateTimeZone ( 'UTC' ) );
    $prev    = $first->modify ( '-1 month' )->format ( 'Y-m' );
    $next    = $first->modify ( '+1 month' )->format ( 'Y-m' );

    $marks = FALSE;

    foreach ( $weeks as $week )
      foreach ( $week ['days'] as $day )
        $marks = $marks || ( $day ['holiday'] ?? '' ) !== '';

    $html = padCalendarStyle () . ( $marks ? padCalendarHolidayStyle () : '' ) . '<table class="pad-calendar">'
          . '<caption>'
          . ( ( $links and padCalendarMonth ( $prev ) !== NULL ) ? '<a class="pad-calendar-prev" href="' . $h ( padPagerHref ( $query, $prev ) ) . '" rel="prev" aria-label="' . $h ( $catalog ['calendar.previous'] ?? 'Previous month' ) . '">‹</a> ' : '' )
          . '<span>' . $h ( padCalendarWord ( $first, 'LLLL uuuu', 'F Y' ) ) . '</span>'
          . ( ( $links and padCalendarMonth ( $next ) !== NULL ) ? ' <a class="pad-calendar-next" href="' . $h ( padPagerHref ( $query, $next ) ) . '" rel="next" aria-label="' . $h ( $catalog ['calendar.next'] ?? 'Next month' ) . '">›</a>' : '' )
          . '</caption><thead><tr>';

    foreach ( $weeks [0] ['days'] as $day )
      $html .= '<th scope="col">' . $h ( $day ['weekday'] ) . '</th>';

    $html .= '</tr></thead><tbody>';

    foreach ( $weeks as $week ) {

      $html .= '<tr>';

      foreach ( $week ['days'] as $day ) {

        $html .= '<td' . ( $day ['class'] !== '' ? ' class="' . $h ( $day ['class'] ) . '"' : '' ) . ( $day ['today'] ? ' aria-current="date"' : '' ) . '>'
               . '<time datetime="' . $day ['date'] . '">' . $day ['day'] . '</time>'
               . ( ( $day ['holiday'] ?? '' ) !== '' ? '<small class="pad-calendar-holiday">' . $h ( $day ['holiday'] ) . '</small>' : '' );

        if ( $day ['events'] ) {
          $html .= '<ul>';
          foreach ( $day ['events'] as $event ) {
            $text  = $h ( padChartText ( $event, $tField ) );
            $link  = padChartText ( $event, $lField );
            $html .= '<li>' . ( $link !== '' && padCalendarSafeLink ( $link ) ? '<a href="' . $h ( $link ) . "\">$text</a>" : $text ) . '</li>';
          }
          $html .= '</ul>';
        }

        $html .= '</td>';

      }

      $html .= '</tr>';

    }

    return "$html</tbody></table>";

  }

  // The holidays of holidays= - 'nl', 'de-by' - on the days a grid of the month shows, the
  // week before and after it too, as [ date => [ name, class ] ]: the names of a day of two
  // holidays joined, class holiday when one of them is a public holiday, else observance.
  // The name is the local one when the request's locale is in a language of the country
  // (padLocale nl_NL and the Netherlands: Koningsdag), else the English one. A country
  // without a file, a region it does not have or a month the template fixes in a year its
  // file does not know (New Zealand's after 2052) is an error under the strict check. No
  // day outside the years the file knows is marked, the neighbouring weeks of the grid
  // neither: a month the visitor asked for ($asked: the request value, which the calendar's
  // own links set, or this month) outside them is no fault of the template and shows only
  // the days inside them - it answered an error page, the link to the month before South
  // Africa's 2014 leading there - and the last days of December 2052 on New Zealand's grid
  // are marked as January 2053 shows them. NULL for a calendar without holidays=: its days
  // get no field holiday.

  function padCalendarHolidays ( $place, $month, $asked = FALSE ) {

    if ( trim ( $place ) === '' )
      return NULL;

    include_once PAD . 'subs/holidays/_lib/holidays.php';

    $where = padHolidaysPlace ( $place );

    if ( is_string ( $where ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the calendar's holidays= - $where" );
      return [];
    }

    [ $y, $m ] = array_map ( 'intval', explode ( '-', $month ) );

    $known = padHolidaysKnown ( $where [0], padHolidaysDay ( $y, $m, 1 ), padHolidaysDay ( $y, $m, 1 ) );

    if ( $known !== '' and ! $asked and $GLOBALS ['padCheckSyntax'] )
      padError ( "the calendar's holidays= - $known" );

    $lang  = strtolower ( substr ( padLocaleOf ( '' ), 0, 2 ) );
    $local = in_array ( $lang, padHolidaysCountry ( $where [0] ) ['langs'], TRUE );
    $days  = [];

    [ $first, $last ] = padHolidaysCountry ( $where [0] ) ['years'];

    $from = max ( padHolidaysDay ( $first, 1, 1 ), padHolidaysDay ( $y, $m, 1 ) - 7 );
    $to   = min ( padHolidaysDay ( $last, 12, 31 ), padHolidaysDay ( $y, $m + 1, 1 ) + 6 );

    if ( $from > $to )
      return $days;

    foreach ( padHolidaysRange ( $where [0], $where [1], $from, $to, $lang ) as $row ) {
      $day   = $days [ $row ['date'] ] ?? [ 'names' => [], 'class' => 'observance' ];
      $day ['names'] [] = $local ? $row ['local'] : $row ['name'];
      if ( $row ['type'] == 'public' )
        $day ['class'] = 'holiday';
      $days [ $row ['date'] ] = $day;
    }

    foreach ( $days as $date => $day )
      $days [$date] = [ 'name' => implode ( ', ', $day ['names'] ), 'class' => $day ['class'] ];

    return $days;

  }

  // The look of the holidays, once per page and only on a page whose calendar marks them:
  // the calendar's own style stays as it was for every other.

  function padCalendarHolidayStyle () {

    if ( ! padWidgetOnce ( 'style:calendar-holidays' ) )
      return '';

    return '<style' . padStyleNonce () . '>'
         . ':where(.pad-calendar td.holiday){background:rgba(214,90,42,.09)}'
         . ':where(.pad-calendar .pad-calendar-holiday){display:block;font-size:11px;line-height:1.25;opacity:.75;overflow:hidden;text-overflow:ellipsis}'
         . '</style>';

  }

  // A link from the data goes nowhere a script would run: http(s), a page of the site, an
  // anchor - never javascript: or data:.

  function padCalendarSafeLink ( $link ) {

    return (bool) preg_match ( '#^(https?://|/|\?|\#|[\w./-]+(\?|$))#i', $link ) and ! preg_match ( '/^\s*(javascript|data|vbscript):/i', $link );

  }

  // A light default look, every rule under :where() so a page's own CSS wins without a fight.

  function padCalendarStyle () {

    return '<style' . padStyleNonce () . '>'
         . ':where(.pad-calendar){border-collapse:collapse;width:100%;table-layout:fixed;font-size:14px}'
         . ':where(.pad-calendar caption){padding:6px;font-weight:600;font-size:16px}'
         . ':where(.pad-calendar caption a){display:inline-block;padding:0 10px;text-decoration:none;color:inherit}'
         . ':where(.pad-calendar th){padding:4px;font-weight:600;font-size:12px;text-align:center;opacity:.7}'
         . ':where(.pad-calendar td){vertical-align:top;height:72px;padding:4px;border:1px solid rgba(128,128,128,.25)}'
         . ':where(.pad-calendar td.other){opacity:.4}'
         . ':where(.pad-calendar td.weekend){background:rgba(128,128,128,.06)}'
         . ':where(.pad-calendar td.today time){display:inline-block;min-width:1.6em;text-align:center;border-radius:1em;background:#2a78d6;color:#fff}'
         . ':where(.pad-calendar ul){margin:4px 0 0;padding:0;list-style:none;font-size:12px}'
         . ':where(.pad-calendar li){margin:2px 0;padding:1px 4px;border-radius:3px;background:rgba(42,120,214,.12);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}'
         . '</style>';

  }

?>
