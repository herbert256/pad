<?php

  // Period views share the original month's Gregorian days and event conversion. All
  // periods are bounded by years 0000..9999; request values are untrusted and may be lists.

  function padCalendarPeriod ( $kind, $value, $sunday = FALSE ) {

    $value = trim ( is_scalar ( $value ) ? (string) $value : '' );
    $utc = new DateTimeZone ( 'UTC' );

    if ( $kind == 'year' and preg_match ( '/^\d{4}$/D', $value ) )
      $value .= '-01';
    if ( $kind == 'quarter' and preg_match ( '/^(\d{4})-Q([1-4])$/Di', $value, $m ) )
      $value = sprintf ( '%04d-%02d', $m [1], 3 * ( $m [2] - 1 ) + 1 );

    if ( in_array ( $kind, [ 'day', 'week' ], TRUE ) ) {
      if ( ! preg_match ( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/D', $value, $m ) )
        return NULL;
      $value = sprintf ( '%04d-%02d-%02d', $m [1], $m [2], $m [3] );
      try { $first = new DateTimeImmutable ( $value, $utc ); }
      catch ( Throwable $e ) { return NULL; }
      if ( $first->format ( 'Y-m-d' ) !== $value )
        return NULL;
    } else {
      $month = padCalendarMonth ( $value );
      if ( $month === NULL )
        return NULL;
      $first = new DateTimeImmutable ( "$month-01", $utc );
    }

    switch ( $kind ) {
      case 'day':
        $last = $first;
        $step = '1 day';
        $format = 'Y-m-d';
        break;
      case 'week':
        $shift = $sunday ? (int) $first->format ( 'w' ) : (int) $first->format ( 'N' ) - 1;
        $first = $first->modify ( "-$shift days" );
        $last = $first->modify ( '+6 days' );
        $step = '7 days';
        $format = 'Y-m-d';
        break;
      case 'year':
        $first = $first->setDate ( (int) $first->format ( 'Y' ), 1, 1 );
        $last = $first->modify ( '+11 months' )->modify ( 'last day of this month' );
        $step = '1 year';
        $format = 'Y';
        break;
      case 'quarter':
        $first = $first->setDate ( (int) $first->format ( 'Y' ), 3 * intdiv ( (int) $first->format ( 'n' ) - 1, 3 ) + 1, 1 );
        $last = $first->modify ( '+2 months' )->modify ( 'last day of this month' );
        $step = '3 months';
        $format = 'Y-m';
        break;
      default:
        $last = $first->modify ( 'last day of this month' );
        $step = '1 month';
        $format = 'Y-m';
    }

    $prev = $first->modify ( "-$step" );
    $next = $first->modify ( "+$step" );
    $min = new DateTimeImmutable ( '0000-01-01', $utc );
    $max = new DateTimeImmutable ( '9999-12-31', $utc );
    $first = max ( $first, $min );
    $last = min ( $last, $max );

    return [ 'first' => $first, 'last' => $last, 'key' => $first->format ( $format ),
             'prev' => $prev >= $min && $prev <= $max ? $prev->format ( $format ) : NULL,
             'next' => $next >= $min && $next <= $max ? $next->format ( $format ) : NULL ];

  }

  // The field guessing of the original month, including DateTimeInterface values.

  function padCalendarFields ( $rows ) {

    [ , $names ] = padChartGuess ( $rows );
    $dates = $texts = $used = [];
    foreach ( padChartRow ( reset ( $rows ) ?: [] ) as $name => $field )
      if ( $field instanceof DateTimeInterface )
        $dates [] = (string) $name;
      elseif ( ! in_array ( (string) $name, $names, TRUE ) )
        continue;
      elseif ( padChartStamp ( $field ) !== FALSE )
        $dates [] = (string) $name;
      else
        $texts [] = (string) $name;

    return [ padChartPick ( 'date', $dates, $used ), padChartPick ( 'title', $texts, $used ), (string) padTagParm ( 'link' ) ];

  }

  function padCalendarView ( $kind, $parameter ) {

    global $pad, $padPrm, $padData, $padPair, $padCheckSyntax;

    $sunday = (bool) padTagParm ( 'sunday', FALSE );
    $query = trim ( (string) padTagParm ( 'query', $kind == 'agenda' ? 'month' : $kind ) );
    $asked = padCalendarPeriod ( $kind, $_GET [$query] ?? '', $sunday );
    $given = ! is_scalar ( $parameter ) || trim ( (string) $parameter ) !== '';
    $period = $given ? padCalendarPeriod ( $kind, $parameter, $sunday ) : $asked;

    if ( $given and $period === NULL and $padCheckSyntax )
      padError ( "the $kind calendar has no period '" . padMakeSafe ( is_scalar ( $parameter ) ? $parameter : 'list', 20 )
               . "' - use " . ( in_array ( $kind, [ 'day', 'week' ], TRUE ) ? 'YYYY-MM-DD' : ( $kind == 'year' ? 'YYYY' : ( $kind == 'quarter' ? 'YYYY-Q1 or YYYY-MM' : 'YYYY-MM' ) ) ) );

    $period ??= padCalendarPeriod ( $kind, padNow ( in_array ( $kind, [ 'day', 'week' ], TRUE ) ? 'Y-m-d' : 'Y-m' ), $sunday );
    $links = ! $given || ( $asked !== NULL && $period ['key'] === $asked ['key'] );
    $events = isset ( $padPrm [$pad] ['data'] ) ? padChartRows ( '', 'calendar' ) : [];
    $padData [$pad] = padDefaultData ();
    [ $dateField, $titleField, $linkField ] = padCalendarFields ( $events );
    $place = padUnprotect ( (string) padTagParm ( 'holidays' ) );
    $empty = $kind == 'agenda' ? (bool) padTagParm ( 'empty', FALSE ) : TRUE;
    $months = $days = [];

    for ( $at = $period ['first']->modify ( 'first day of this month' ); $at <= $period ['last']; $at = $at->modify ( '+1 month' ) ) {
      $month = $at->format ( 'Y-m' );
      $holidays = padCalendarHolidays ( $place, $month, $links );
      $weeks = padCalendarWeeks ( $month, $events, $dateField, $sunday, $holidays );
      $count = 0;
      foreach ( $weeks as $week )
        foreach ( $week ['days'] as $day ) {
          if ( $day ['other'] ) continue;
          $count += $day ['count'];
          if ( $day ['date'] >= $period ['first']->format ( 'Y-m-d' ) && $day ['date'] <= $period ['last']->format ( 'Y-m-d' )
               && ( $empty || $day ['count'] || ( $day ['holiday'] ?? '' ) !== '' ) )
            $days [] = $day;
        }
      $months [] = [ 'month' => $month, 'year' => (int) $at->format ( 'Y' ), 'number' => (int) $at->format ( 'n' ),
                     'name' => padCalendarWord ( $at, 'LLLL uuuu', 'F Y' ), 'count' => $count, 'weeks' => $weeks ];
    }

    $group = in_array ( $kind, [ 'year', 'quarter' ], TRUE );
    if ( $padPair [$pad] )
      return $group ? $months : $days;

    $html = padCalendarViewsStyle () . '<section class="pad-calendar-view pad-calendar-view-' . $kind . '">'
          . padCalendarHeading ( $kind, $period, $query, $links );

    if ( $group ) {
      $html .= '<div class="pad-calendar-months">';
      foreach ( $months as $i => $month )
        $html .= '<div class="pad-calendar-mini">' . padCalendarHtml ( $month ['month'], $month ['weeks'], $titleField, $linkField, $query, FALSE, $i == 0 ) . '</div>';
      return "$html</div></section>";
    }

    $html .= '<div class="pad-calendar-days">';
    foreach ( $days as $day ) {
      $date = new DateTimeImmutable ( $day ['date'], new DateTimeZone ( 'UTC' ) );
      $html .= '<article class="pad-calendar-day ' . padCalendarEscape ( $day ['class'] ) . '"'
             . ( $day ['today'] ? ' aria-current="date"' : '' ) . '><h3><time datetime="' . $day ['date'] . '">'
             . padCalendarEscape ( padCalendarWord ( $date, 'EEE d MMM', 'D j M' ) ) . '</time></h3>';
      if ( ( $day ['holiday'] ?? '' ) !== '' )
        $html .= '<p class="pad-calendar-holiday">' . padCalendarEscape ( $day ['holiday'] ) . '</p>';
      if ( $day ['events'] ) {
        $html .= '<ul>';
        foreach ( $day ['events'] as $event ) {
          $text = padCalendarEscape ( padChartText ( $event, $titleField ) );
          $link = padChartText ( $event, $linkField );
          $html .= '<li>' . ( $link !== '' && padCalendarSafeLink ( $link ) ? '<a href="' . padCalendarEscape ( $link ) . '">' . $text . '</a>' : $text ) . '</li>';
        }
        $html .= '</ul>';
      }
      $html .= '</article>';
    }
    if ( ! $days )
      $html .= '<p class="pad-calendar-empty">' . padCalendarEscape ( padTransCatalog () ['calendar.empty'] ?? 'No events in this period.' ) . '</p>';

    return "$html</div></section>";

  }

  function padCalendarEscape ( $value ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $value ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  function padCalendarHeading ( $kind, $period, $query, $links ) {

    $first = $period ['first'];
    $last = $period ['last'];
    $label = match ( $kind ) {
      'year' => $first->format ( 'Y' ),
      'day' => padCalendarWord ( $first, 'EEEE d MMMM uuuu', 'l j F Y' ),
      'week', 'quarter' => padCalendarWord ( $first, 'd MMM uuuu', 'j M Y' ) . ' – ' . padCalendarWord ( $last, 'd MMM uuuu', 'j M Y' ),
      default => padCalendarWord ( $first, 'LLLL uuuu', 'F Y' )
    };
    $catalog = padTransCatalog ();
    $html = '<header class="pad-calendar-heading">';
    if ( $links && $period ['prev'] !== NULL )
      $html .= '<a class="pad-calendar-prev" rel="prev" href="' . padCalendarEscape ( padPagerHref ( $query, $period ['prev'] ) )
             . '" aria-label="' . padCalendarEscape ( $catalog ['calendar.previousPeriod'] ?? 'Previous period' ) . '">‹</a>';
    $html .= '<h2>' . padCalendarEscape ( $label ) . '</h2>';
    if ( $links && $period ['next'] !== NULL )
      $html .= '<a class="pad-calendar-next" rel="next" href="' . padCalendarEscape ( padPagerHref ( $query, $period ['next'] ) )
             . '" aria-label="' . padCalendarEscape ( $catalog ['calendar.nextPeriod'] ?? 'Next period' ) . '">›</a>';
    return "$html</header>";

  }

  function padCalendarViewsStyle () {

    if ( ! padWidgetOnce ( 'style:calendar-views' ) ) return '';

    return '<style' . padStyleNonce () . '>'
      . ':where(.pad-calendar-view){font-size:14px;color:inherit}'
      . ':where(.pad-calendar-heading){display:flex;align-items:center;justify-content:center;gap:16px;margin:0 0 16px}'
      . ':where(.pad-calendar-heading h2){font-size:18px;margin:0;text-align:center}'
      . ':where(.pad-calendar-heading a){color:inherit;text-decoration:none;font-size:24px;padding:4px 12px}'
      . ':where(.pad-calendar-months){display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,235px),1fr));gap:24px;align-items:start}'
      // The wrapper supplies one class of specificity: a later ordinary month must not
      // restore its 72px cells in an earlier compact calendar. Page rules still override it.
      . '.pad-calendar-mini :where(.pad-calendar){font-size:12px}'
      . '.pad-calendar-mini :where(.pad-calendar td){height:28px;padding:3px}'
      . '.pad-calendar-mini :where(.pad-calendar caption){font-size:14px}'
      . '.pad-calendar-mini :where(.pad-calendar ul){font-size:10px}'
      . ':where(.pad-calendar-days){display:grid;gap:12px}'
      . ':where(.pad-calendar-view-week .pad-calendar-days){grid-template-columns:repeat(7,minmax(0,1fr));gap:0}'
      . ':where(.pad-calendar-day){min-width:0;padding:12px;border:1px solid rgba(128,128,128,.25);overflow-wrap:anywhere}'
      . ':where(.pad-calendar-day h3){font-size:14px;margin:0 0 10px}'
      . ':where(.pad-calendar-day ul){list-style:none;padding:0;margin:0}'
      . ':where(.pad-calendar-day li){padding:5px 0}'
      . ':where(.pad-calendar-day.weekend){background:rgba(128,128,128,.06)}'
      . ':where(.pad-calendar-day.today){border-color:#2a78d6;border-top-width:3px}'
      . ':where(.pad-calendar-day.holiday){background:rgba(214,90,42,.09)}'
      . ':where(.pad-calendar-day .pad-calendar-holiday){font-size:12px;margin:0 0 8px}'
      . ':where(.pad-calendar-empty){padding:16px;text-align:center}'
      . '@media(max-width:700px){:where(.pad-calendar-view-week .pad-calendar-days){grid-template-columns:1fr}}'
      . '</style>';

  }

?>
