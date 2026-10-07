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
  //
  // The month is the first parameter, else the request value named by query= (month), else
  // the month of today - padNow, so padNowFreeze holds it still. A week starts on Monday,
  // or on Sunday with sunday.

  // 'YYYY-MM' of a text, or NULL.

  function padCalendarMonth ( $text ) {

    $text = trim ( (string) $text );

    if ( ! preg_match ( '/^(\d{4})-(\d{1,2})$/', $text, $m ) or $m [2] < 1 or $m [2] > 12 )
      return NULL;

    return sprintf ( '%04d-%02d', $m [1], $m [2] );

  }

  function padCalendarWeeks ( $month, $rows, $dField, $sunday ) {

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
      $class   = trim ( ( $other ? ' other' : '' ) . ( $date == $today ? ' today' : '' ) . ( $weekend ? ' weekend' : '' ) . ( $list ? ' events' : '' ) );

      $weeks [ count ( $weeks ) - 1 ] ['days'] [] = [
        'date'    => $date,
        'day'     => (int) $day->format ( 'j' ),
        'weekday' => $day->format ( 'D' ),
        'month'   => $day->format ( 'Y-m' ),
        'other'   => $other,
        'today'   => $date == $today,
        'weekend' => $weekend,
        'count'   => count ( $list ),
        'events'  => $list,
        'class'   => $class
      ];

    }

    return $weeks;

  }

  // The table: a caption with the month and links to the months around it, a header of
  // weekdays, a cell per day with its events as a list - their title= field, each a link
  // when link= names a field that holds one.

  function padCalendarHtml ( $month, $weeks, $tField, $lField, $query ) {

    $catalog = padTransCatalog ();
    $h       = fn ( $t ) => htmlspecialchars ( (string) $t, ENT_QUOTES, 'UTF-8' );
    $first   = new DateTimeImmutable ( "$month-01", new DateTimeZone ( 'UTC' ) );
    $prev    = $first->modify ( '-1 month' )->format ( 'Y-m' );
    $next    = $first->modify ( '+1 month' )->format ( 'Y-m' );

    $html = padCalendarStyle () . '<table class="pad-calendar">'
          . '<caption>'
          . '<a class="pad-calendar-prev" href="' . $h ( padPagerHref ( $query, $prev ) ) . '" rel="prev" aria-label="' . $h ( $catalog ['calendar.previous'] ?? 'Previous month' ) . '">‹</a> '
          . '<span>' . $h ( $first->format ( 'F Y' ) ) . '</span>'
          . ' <a class="pad-calendar-next" href="' . $h ( padPagerHref ( $query, $next ) ) . '" rel="next" aria-label="' . $h ( $catalog ['calendar.next'] ?? 'Next month' ) . '">›</a>'
          . '</caption><thead><tr>';

    foreach ( $weeks [0] ['days'] as $day )
      $html .= '<th scope="col">' . $h ( $day ['weekday'] ) . '</th>';

    $html .= '</tr></thead><tbody>';

    foreach ( $weeks as $week ) {

      $html .= '<tr>';

      foreach ( $week ['days'] as $day ) {

        $html .= '<td' . ( $day ['class'] !== '' ? ' class="' . $h ( $day ['class'] ) . '"' : '' ) . ( $day ['today'] ? ' aria-current="date"' : '' ) . '>'
               . '<time datetime="' . $day ['date'] . '">' . $day ['day'] . '</time>';

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

  // A link from the data goes nowhere a script would run: http(s), a page of the site, an
  // anchor - never javascript: or data:.

  function padCalendarSafeLink ( $link ) {

    return (bool) preg_match ( '#^(https?://|/|\?|\#|[\w./-]+(\?|$))#i', $link ) and ! preg_match ( '/^\s*(javascript|data|vbscript):/i', $link );

  }

  // A light default look, every rule under :where() so a page's own CSS wins without a fight.

  function padCalendarStyle () {

    return '<style>'
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
