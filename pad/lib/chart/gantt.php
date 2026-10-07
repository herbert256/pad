<?php

  // Tasks in time: a row per task, a bar from its start to its end.
  //
  //   {chart 'gantt', data='plan', label='task', from='begin', to='until'}
  //   {chart 'gantt', data='plan', color='team', progress='done', mark='2026-10-07'}
  //
  // label= names the task - else the first field that is no date; from= and to= name the
  // fields of its start and end - else the first two fields that read as dates. Dates are
  // anything strtotime reads; when every start and end is a plain number the axis counts
  // those instead (week 1 to 12). color= colours the bars by a field, with a legend;
  // progress= names a field of 0 to 100, the part of the bar that is done drawn full and
  // the rest light; mark= draws a dashed line at a date - today, a deadline.

  function padChartGantt ( $rows, $width, $height ) {

    global $padCheckSyntax;

    $rows  = array_map ( 'padChartRow', array_values ( (array) $rows ) );
    $first = $rows [0] ?? [];
    $dates = $other = [];

    foreach ( $first as $key => $field )
      if ( is_scalar ( $field ) and ! is_numeric ( $field ) and padChartStamp ( $field ) !== FALSE )
        $dates [] = (string) $key;
      elseif ( is_scalar ( $field ) and ! is_numeric ( $field ) )
        $other [] = (string) $key;

    $used   = [];
    $fField = padChartPick ( 'from',  $dates, $used );
    $tField = padChartPick ( 'to',    $dates, $used );
    $label  = padChartPick ( 'label', $other, $used );
    $cField = (string) padTagParm ( 'color' );
    $pField = (string) padTagParm ( 'progress' );

    $numeric = TRUE;
    foreach ( $rows as $row )
      if ( ! is_numeric ( $row [$fField] ?? '' ) or ! is_numeric ( $row [$tField] ?? '' ) )
        $numeric = FALSE;

    $at    = fn ( $v ) => $numeric ? ( is_numeric ( $v ) ? $v + 0 : FALSE ) : padChartStamp ( $v );
    $tasks = $groups = [];

    foreach ( $rows as $i => $row ) {

      $a = $at ( $row [$fField] ?? '' );
      $b = $at ( $row [$tField] ?? '' );

      if ( $a === FALSE or $b === FALSE )
        continue;

      if ( $b < $a )
        list ( $a, $b ) = [ $b, $a ];

      $group = ( $cField !== '' ) ? padChartText ( $row, $cField ) : '';
      $done  = ( $pField !== '' and is_numeric ( $row [$pField] ?? '' ) ) ? max ( 0, min ( 100, $row [$pField] + 0 ) ) : NULL;

      if ( ! isset ( $groups [$group] ) )
        $groups [$group] = count ( $groups );

      $name     = padChartText ( $row, $label );
      $tasks [] = [ $name !== '' ? $name : 'Task ' . ( $i + 1 ), $a, $b, $group, $done ];

    }

    if ( ! $tasks ) {
      if ( $padCheckSyntax and $rows )
        padError ( "the gantt chart has no tasks with a start and an end - from='field' and to='field'" );
      return '';
    }

    $show = fn ( $v ) => $numeric ? padChartNumber ( $v ) : gmdate ( 'M j, Y', $v );
    $desc = [];

    foreach ( $tasks as list ( $name, $a, $b, $group, $done ) )
      $desc [] = "$name: " . $show ( $a ) . ' – ' . $show ( $b ) . ( $group !== '' ? " ($group)" : '' ) . ( $done !== NULL ? ', ' . padChartNumber ( $done ) . '% done' : '' );

    $title = (string) padTagParm ( 'title', 'Plan' );

    list ( $id, $svg ) = padChartOpen ( 'gantt', $tasks, $desc, $title, $width, $height );

    $low  = min ( array_column ( $tasks, 1 ) );
    $high = max ( array_column ( $tasks, 2 ) );
    $mark = padTagParm ( 'mark' );
    $mark = ( $mark !== '' and $mark !== TRUE ) ? $at ( $mark ) : FALSE;

    if ( $high == $low )
      $high = $low + ( $numeric ? 1 : 86400 );

    $labelW = 0;
    foreach ( $tasks as $task )
      $labelW = max ( $labelW, padChartWidth ( $task [0] ) + 20 );

    $left   = (int) max ( 40, min ( $width * 0.3, $labelW ) );
    $right  = 14;
    $top    = 8;

    if ( count ( $groups ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_keys ( $groups ), $left, 6, $width - $left - $right );
      $svg .= $legend;
      $top += $legendH;
    }

    $bottom = 22;
    $plotW  = $width - $left - $right;
    $band   = ( $height - $top - $bottom ) / count ( $tasks );
    $barH   = max ( 3, min ( 22, $band * 0.65 ) );
    $x      = fn ( $v ) => $left + ( $v - $low ) / ( $high - $low ) * $plotW;

    // The time axis: round numbers, or the first days of months, weeks or days.

    foreach ( padChartGanttTicks ( $low, $high, $numeric, $plotW ) as $tick => $text ) {
      $p    = padChartXY ( $x ( $tick ) );
      $svg .= "<line class=\"pc-grid\" x1=\"$p\" x2=\"$p\" y1=\"$top\" y2=\"" . padChartXY ( $height - $bottom ) . '"/>'
            . "<text x=\"$p\" y=\"" . ( $height - 7 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';
    }

    foreach ( $tasks as $i => list ( $name, $a, $b, $group, $done ) ) {

      $y    = $top + ( $i + 0.5 ) * $band;
      $x0   = $x ( $a );
      $w    = max ( 2, $x ( $b ) - $x0 );
      $slot = padChartSlot ( $groups [$group] );
      $tip  = '<title>' . padChartAttr ( $desc [$i] ) . '</title>';

      $svg .= '<text x="' . ( $left - 8 ) . '" y="' . padChartXY ( $y + 4 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $left - 12 ) ) . '</text>';

      if ( $done === NULL )
        $svg .= "<rect class=\"pc-mark $slot\" x=\"" . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y - $barH / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $barH ) . "\" rx=\"3\">$tip</rect>";
      else
        $svg .= "<g class=\"pc-mark\">$tip<rect class=\"$slot pc-todo\" x=\"" . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y - $barH / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $barH ) . '" rx="3"/>'
              . "<rect class=\"$slot\" x=\"" . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y - $barH / 2 ) . '" width="' . padChartXY ( $w * $done / 100 ) . '" height="' . padChartXY ( $barH ) . '" rx="3"/></g>';

    }

    if ( $mark !== FALSE and $mark >= $low and $mark <= $high ) {
      $p    = padChartXY ( $x ( $mark ) );
      $svg .= "<line class=\"pc-target\" stroke-dasharray=\"4 3\" x1=\"$p\" x2=\"$p\" y1=\"" . padChartXY ( $top - 2 ) . '" y2="' . padChartXY ( $height - $bottom ) . '">'
            . '<title>' . padChartAttr ( $show ( $mark ) ) . '</title></line>';
    }

    return "$svg</svg>";

  }

  // The ticks of the time axis, value => text: round numbers on a numeric axis; else the
  // first of every month, every week or every day - whichever leaves room for its text -
  // and of every quarter on a span of years.

  function padChartGanttTicks ( $low, $high, $numeric, $plotW ) {

    $ticks = [];

    if ( $numeric ) {
      foreach ( padChartTicks ( $low, $high, max ( 2, (int) ( $plotW / 70 ) ) ) as $tick )
        if ( $tick >= $low and $tick <= $high )
          $ticks [ (string) $tick ] = padChartNumber ( $tick );
      return $ticks;
    }

    $days  = ( $high - $low ) / 86400;
    $room  = max ( 1, $plotW / 48 );
    $start = new DateTimeImmutable ( '@' . (int) $low );

    if ( $days <= $room ) {
      $step = max ( 1, (int) ceil ( $days / $room ) );
      $t    = $start->setTime ( 0, 0 );
      $form = 'M j';
      $next = "+$step days";
    } elseif ( $days / 7 <= $room ) {
      $t    = $start->setTime ( 0, 0 )->modify ( 'monday this week' );
      $form = 'M j';
      $next = '+' . max ( 1, (int) ceil ( $days / 7 / $room ) ) . ' weeks';
    } else {
      $months = max ( 1, (int) ceil ( $days / 30.4 / $room ) );
      $months = $months <= 1 ? 1 : ( $months <= 3 ? 3 : ( $months <= 6 ? 6 : 12 ) );
      $t      = $start->setDate ( (int) $start->format ( 'Y' ), 1, 1 )->setTime ( 0, 0 );
      $form   = $days > 400 ? 'M Y' : 'M';
      $next   = "+$months months";
    }

    for ( ; $t->getTimestamp () <= $high; $t = $t->modify ( $next ) )
      if ( $t->getTimestamp () >= $low )
        $ticks [ $t->getTimestamp () ] = $t->format ( $form );

    return $ticks;

  }

?>
