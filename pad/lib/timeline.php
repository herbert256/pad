<?php

  // Events in time - the {timeline} tag. Inline SVG drawn on the server, as {chart} draws:
  // no JavaScript, so a timeline works in print and in an e-mail.
  //
  //   {timeline data='releases', date='when', label='what', text='detail'}
  //   {timeline data='eras', from='begin', to='end', label='name', color='kind'}
  //   {timeline data='history', vertical}
  //
  // padTimelineDate    a date as [ moment, precision ]: a year (1969), a month (2026-10) or
  //                    anything strtotime reads
  // padTimelineEvents  the rows as events: a moment, or a span from one to another
  // padTimeline        the horizontal timeline: a time axis with round ticks, the events
  //                    on cards above and below it, the spans as bars under it
  // padTimelineColumn  the vertical timeline: dates on the left, cards on the right, one
  //                    under the other - for a long list
  //
  // The colours are the chart's categorical slots for the dots and bars (color= groups the
  // events, with a legend) and custom properties with light-dark() defaults on
  // .pad-chart-timeline for the rest: --pad-timeline-card, --pad-timeline-border,
  // --pad-timeline-line and --pad-timeline-muted.

  // A date as [ moment, precision ] - 'year', 'month', 'day' or 'time' - else FALSE. A
  // number of one to four digits is a year: 1969 is a year, not 1969 seconds into 1970.

  function padTimelineDate ( $value ) {

    $value = trim ( (string) $value );

    if ( $value === '' )
      return FALSE;

    if ( preg_match ( '/^\d{1,4}$/', $value ) and (int) $value > 0 )
      return [ ( new DateTimeImmutable ( sprintf ( '%04d-01-01', (int) $value ), new DateTimeZone ( 'UTC' ) ) )->getTimestamp (), 'year' ];

    if ( preg_match ( '/^(\d{4})-(\d{1,2})$/', $value, $match ) and $match [2] >= 1 and $match [2] <= 12 )
      return [ gmmktime ( 0, 0, 0, (int) $match [2], 1, (int) $match [1] ), 'month' ];

    if ( ctype_digit ( $value ) )
      return [ (int) $value, 'time' ];

    $stamp = strtotime ( "$value UTC" );

    if ( $stamp === FALSE )
      return FALSE;

    return [ $stamp, ( gmdate ( 'His', $stamp ) === '000000' ) ? 'day' : 'time' ];

  }

  // A moment as its precision writes it - 1969, Oct 2026, Oct 8, 2026 - or in the format
  // given.

  function padTimelineShow ( $stamp, $precision, $format = '' ) {

    if ( $format !== '' )
      return gmdate ( $format, $stamp );

    return gmdate ( [ 'year' => 'Y', 'month' => 'M Y', 'day' => 'M j, Y' ] [$precision] ?? 'M j, Y H:i', $stamp );

  }

  // The rows as events, by their start: [ start, end, precision, label, text, group, when ]
  // - end is NULL for a moment, when the date as it is written on the card. A row without
  // a date is left out.

  function padTimelineEvents ( $rows, $dField, $fField, $tField, $lField, $xField, $cField, $format ) {

    $events = [];

    foreach ( (array) $rows as $i => $row ) {

      $row  = padChartRow ( $row );
      $from = ( $fField !== '' ) ? padTimelineDate ( padChartText ( $row, $fField ) ) : FALSE;
      $to   = ( $tField !== '' ) ? padTimelineDate ( padChartText ( $row, $tField ) ) : FALSE;
      $at   = ( $dField !== '' ) ? padTimelineDate ( padChartText ( $row, $dField ) ) : FALSE;

      if ( $from !== FALSE and $to !== FALSE ) {
        list ( $a, $b ) = ( $to [0] < $from [0] ) ? [ $to, $from ] : [ $from, $to ];
        $start = $a [0];
        $end   = $b [0];
        $prec  = $a [1];
        $when  = padTimelineShow ( $a [0], $a [1], $format ) . ' – ' . padTimelineShow ( $b [0], $b [1], $format );
      } elseif ( ( $one = ( $at !== FALSE ) ? $at : $from ) !== FALSE ) {
        list ( $start, $prec ) = $one;
        $end  = NULL;
        $when = padTimelineShow ( $start, $prec, $format );
      } else
        continue;

      $label = padChartText ( $row, $lField );

      $events [] = [ $start, $end, $prec, $label !== '' ? $label : 'Event ' . ( count ( $events ) + 1 ),
                     padChartText ( $row, $xField ), padChartText ( $row, $cField ), $when, $i ];

    }

    usort ( $events, fn ( $a, $b ) => [ $a [0], $a [7] ] <=> [ $b [0], $b [7] ] );

    return $events;

  }

  // Round ticks for the time axis, moment => text: whole years in steps of 1, 2, 5, 10, 25,
  // 50 or 100; else months in steps of 1, 3 or 6; else days in steps of 1, 2, 7 or 14 -
  // the smallest step that leaves each tick its room.

  function padTimelineTicks ( $low, $high, $plotW ) {

    $room  = max ( 2, (int) floor ( $plotW / 72 ) );
    $days  = ( $high - $low ) / 86400;
    $start = ( new DateTimeImmutable ( '@' . (int) floor ( $low ) ) )->setTime ( 0, 0 );
    $ticks = [];

    if ( $days > 3 * 365 ) {

      $years = $days / 365.25;
      $step  = 100;
      foreach ( [ 1, 2, 5, 10, 25, 50, 100 ] as $try )
        if ( $years / $try <= $room ) {
          $step = $try;
          break;
        }

      $year = (int) ( ceil ( (int) $start->format ( 'Y' ) / $step ) * $step );
      $t    = $start->setDate ( $year, 1, 1 );
      $next = "+$step years";
      $form = 'Y';

    } elseif ( $days > 60 ) {

      $months = $days / 30.44;
      $step   = ( $months <= $room ) ? 1 : ( ( $months / 3 <= $room ) ? 3 : 6 );
      $month  = (int) $start->format ( 'n' );
      $t      = $start->setDate ( (int) $start->format ( 'Y' ), $month, 1 );
      if ( $t->getTimestamp () < $low )
        $t = $t->modify ( '+1 month' );
      while ( ( (int) $t->format ( 'n' ) - 1 ) % $step )
        $t = $t->modify ( '+1 month' );
      $next = "+$step months";
      $form = 'M Y';

    } else {

      $step = 14;
      foreach ( [ 1, 2, 7, 14 ] as $try )
        if ( $days / $try <= $room ) {
          $step = $try;
          break;
        }

      $t    = ( $step == 7 or $step == 14 ) ? $start->modify ( 'monday this week' ) : $start;
      $next = "+$step days";
      $form = 'M j';

    }

    for ( ; $t->getTimestamp () <= $high; $t = $t->modify ( $next ) )
      if ( $t->getTimestamp () >= $low )
        $ticks [ $t->getTimestamp () ] = $t->format ( $form );

    return $ticks;

  }

  // The colours of the cards and the line, as {chart} writes its own.

  function padTimelineStyle () {

    $roles = [ 'card'  => [ '#ffffff', '#232322' ], 'border' => [ '#dcdbd6', '#3f3f3c' ],
               'line'  => [ '#9a9993', '#6f6e69' ], 'muted'  => [ '#787670', '#a3a29a' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-timeline-$role:$day;";
      $both  .= "--pad-timeline-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-chart-timeline){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chart-timeline){{$both}}}"
         . '.pad-chart-timeline .pt-card{fill:var(--pad-timeline-card);stroke:var(--pad-timeline-border);stroke-width:1}'
         . '.pad-chart-timeline .pt-line{stroke:var(--pad-timeline-line);stroke-width:2}'
         . '.pad-chart-timeline .pt-leader{stroke:var(--pad-timeline-line);stroke-width:1;stroke-dasharray:2 3}'
         . '.pad-chart-timeline .pt-tick{stroke:var(--pad-timeline-line);stroke-width:1}'
         . '.pad-chart-timeline .pt-dot{stroke:var(--pad-chart-surface);stroke-width:2}'
         . '.pad-chart-timeline .pt-date{fill:var(--pad-timeline-muted);font-size:11px}'
         . '.pad-chart-timeline .pt-label{font-size:12px;font-weight:600}'
         . '.pad-chart-timeline .pt-text{font-size:11.5px}'
         . '</style>';

  }

  // A card's lines: the label, cut to the width, and the text in at most $most lines of
  // $chars characters, an ellipsis where it was cut.

  function padTimelineLines ( $text, $chars, $most ) {

    if ( $text === '' )
      return [];

    $lines = [];
    $line  = '';

    foreach ( preg_split ( '/\s+/u', trim ( $text ) ) as $word ) {
      if ( $line !== '' and mb_strlen ( "$line $word" ) > $chars ) {
        $lines [] = $line;
        $line     = $word;
      } else
        $line = ( $line === '' ) ? $word : "$line $word";
    }

    $lines [] = $line;

    if ( count ( $lines ) > $most ) {
      $lines = array_slice ( $lines, 0, $most );
      $lines [ $most - 1 ] = mb_substr ( $lines [ $most - 1 ], 0, max ( 1, $chars - 1 ) ) . '…';
    }

    foreach ( $lines as $k => $one )
      if ( mb_strlen ( $one ) > $chars )
        $lines [$k] = mb_substr ( $one, 0, $chars - 1 ) . '…';

    return $lines;

  }

  // A card at x, y: the date - unless the vertical timeline writes it beside the card -
  // the label and the text, a stripe of its colour on the left.

  function padTimelineCard ( $x, $y, $w, $h, $event, $lines, $slot, $dated = TRUE ) {

    list ( , , , $label, , , $when ) = $event;

    $at  = $dated ? 16 : 2;
    $svg = '<rect class="pt-card" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $h ) . '" rx="5"/>'
         . "<rect class=\"$slot\" x=\"" . padChartXY ( $x ) . '" y="' . padChartXY ( $y + 5 ) . '" width="3" height="' . padChartXY ( $h - 10 ) . '" rx="1.5"/>'
         . ( $dated ? '<text class="pt-date" x="' . padChartXY ( $x + 11 ) . '" y="' . padChartXY ( $y + 16 ) . '">' . padChartAttr ( padChartFit ( $when, $w - 16 ) ) . '</text>' : '' )
         . '<text class="pt-label" x="' . padChartXY ( $x + 11 ) . '" y="' . padChartXY ( $y + $at + 16 ) . '">' . padChartAttr ( padChartFit ( $label, ( $w - 16 ) * 0.92 ) ) . '</text>';

    foreach ( $lines as $k => $line )
      $svg .= '<text class="pt-text" x="' . padChartXY ( $x + 11 ) . '" y="' . padChartXY ( $y + $at + 32 + 15 * $k ) . '">' . padChartAttr ( $line ) . '</text>';

    return $svg;

  }

  // The description of an event, for the <desc> and its tooltip.

  function padTimelineSay ( $event ) {

    return $event [6] . ': ' . $event [3] . ( $event [4] !== '' ? ' - ' . $event [4] : '' ) . ( $event [5] !== '' ? ' (' . $event [5] . ')' : '' );

  }

  // The horizontal timeline. The axis runs across the middle; every moment gets a dot on
  // it and a card above or below - alternating, and moved one lane further out where it
  // would cover a card or the leader line of another; the spans lie as bars under the
  // axis, packed in lanes of their own.

  function padTimeline ( $events, $groups, $title, $width, $height ) {

    $left  = 16;
    $right = 16;
    $top   = 8;

    if ( count ( $groups ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_keys ( $groups ), $left, 8, $width - $left - $right );
      $top += $legendH + 4;
    } else
      $legend = '';

    $low  = min ( array_column ( $events, 0 ) );
    $high = $low;

    foreach ( $events as $event )
      $high = max ( $high, $event [1] ?? $event [0] );

    if ( $high == $low ) {
      $low  -= 86400 * 15;
      $high += 86400 * 15;
    }

    $plotW = $width - $left - $right;
    $edge  = ( $high - $low ) * 0.04;
    $low  -= $edge;
    $high += $edge;
    $x     = fn ( $t ) => $left + ( $t - $low ) / ( $high - $low ) * $plotW;

    // The spans in lanes: a bar with its label inside when it fits, else beside it.

    $spans = $spanLanes = [];

    foreach ( $events as $k => $event ) {

      if ( $event [1] === NULL )
        continue;

      $x0     = $x ( $event [0] );
      $x1     = max ( $x0 + 4, $x ( $event [1] ) );
      $inside = padChartWidth ( $event [3] ) + 12 <= $x1 - $x0;
      $reach  = $inside ? $x1 : $x1 + 6 + padChartWidth ( $event [3] );

      for ( $lane = 0; isset ( $spanLanes [$lane] ) and $spanLanes [$lane] > $x0 - 6; $lane++ );

      $spanLanes [$lane] = $reach;
      $spans [$k]        = [ $x0, $x1, $lane, $inside ];

    }

    // The cards: as wide as their text, at most 176; as high as the highest on their side.

    $cards = [];

    foreach ( $events as $k => $event )
      if ( $event [1] === NULL ) {
        $lines = padTimelineLines ( $event [4], 24, 3 );
        $w     = 22 + max ( padChartWidth ( $event [6] ), padChartWidth ( $event [3] ) * 1.08 );
        foreach ( $lines as $line )
          $w = max ( $w, 22 + padChartWidth ( $line ) );
        $cards [$k] = [ min ( 176, max ( 90, $w ) ), 40 + 15 * count ( $lines ), $lines ];
      }

    // The places: the preferred side first - alternating - then the other, lane by lane
    // outward, the card right or left of its leader; the first place where it covers no
    // card and no leader line, and its own leader crosses no card nearer the axis. Where
    // every lane breaks the rule of the leaders, the first lane free of cards: the leader
    // then passes behind a card.

    $placed = [];
    $turn   = 0;

    foreach ( $cards as $k => list ( $w, $h ) ) {

      $at    = $x ( $events [$k] [0] );
      $sides = ( $turn++ % 2 == 0 ) ? [ 'up', 'down' ] : [ 'down', 'up' ];
      $spot  = NULL;

      // A card hangs right of its leader, else left of it.

      $hangs = [];
      foreach ( [ $at - 14, $at - $w + 14 ] as $from )
        $hangs [] = max ( $left, min ( $width - $right - $w, $from ) );

      foreach ( [ TRUE, FALSE ] as $strict )
        for ( $lane = 0; $spot === NULL and $lane < count ( $cards ); $lane++ )
          foreach ( $sides as $side ) {

            foreach ( $hangs as $x0 ) {

              $x1   = $x0 + $w;
              $free = TRUE;

              foreach ( $placed as $other )
                if ( $other [0] == $side ) {
                  if ( $other [1] == $lane and $x0 < $other [3] + 10 and $x1 > $other [2] - 10 )
                    $free = FALSE;
                  if ( $strict and $other [1] > $lane and $other [4] >= $x0 - 4 and $other [4] <= $x1 + 4 )
                    $free = FALSE;
                  if ( $strict and $other [1] < $lane and $at >= $other [2] - 4 and $at <= $other [3] + 4 )
                    $free = FALSE;
                }

              if ( $free ) {
                $spot = [ $side, $lane, $x0, $x1 ];
                break 2;
              }

            }

          }

      $placed [$k] = [ $spot [0], $spot [1], $spot [2], $spot [3], $at ];

    }

    $laneH = [ 'up' => 0, 'down' => 0 ];
    $lanes = [ 'up' => 0, 'down' => 0 ];

    foreach ( $placed as $k => list ( $side, $lane ) ) {
      $laneH [$side] = max ( $laneH [$side], $cards [$k] [1] + 10 );
      $lanes [$side] = max ( $lanes [$side], $lane + 1 );
    }

    $axisY     = $top + $lanes ['up'] * $laneH ['up'] + 14;
    $spanTop   = $axisY + 26;
    $spanCount = count ( $spanLanes );
    $belowTop  = $spanTop + $spanCount * 24 + ( $spanCount ? 8 : 0 ) + 6;
    $natural   = (int) ceil ( $belowTop + $lanes ['down'] * $laneH ['down'] + 4 );

    $desc = array_map ( 'padTimelineSay', $events );

    list ( $id, $svg ) = padChartOpen ( 'timeline', $events, $desc, $title, $width, $height > 0 ? $height : $natural );

    $svg .= padTimelineStyle () . $legend;

    // The axis and its ticks.

    $svg .= '<line class="pt-line" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $axisY ) . '" y2="' . padChartXY ( $axisY ) . '"/>';

    foreach ( padTimelineTicks ( $low, $high, $plotW ) as $tick => $text ) {
      $p    = padChartXY ( $x ( $tick ) );
      $svg .= "<line class=\"pt-tick\" x1=\"$p\" x2=\"$p\" y1=\"" . padChartXY ( $axisY ) . '" y2="' . padChartXY ( $axisY + 6 ) . '"/>'
            . "<text class=\"pt-date\" x=\"$p\" y=\"" . padChartXY ( $axisY + 18 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';
    }

    // The leader lines first, the cards and the dots over them.

    $marks = '';

    foreach ( $placed as $k => list ( $side, $lane, $x0, $x1, $at ) ) {

      $event = $events [$k];
      list ( $w, $h, $lines ) = $cards [$k];

      $slot = padChartSlot ( $groups [ $event [5] ] ?? 0 );

      if ( $side == 'up' ) {
        $y    = $axisY - 14 - $lane * $laneH ['up'] - $h;
        $from = $y + $h;
      } else {
        $y    = $belowTop + $lane * $laneH ['down'];
        $from = $y;
      }

      $svg   .= '<line class="pt-leader" x1="' . padChartXY ( $at ) . '" x2="' . padChartXY ( $at ) . '" y1="' . padChartXY ( $axisY ) . '" y2="' . padChartXY ( $from ) . '"/>';
      $marks .= '<g class="pc-mark"><title>' . padChartAttr ( padTimelineSay ( $event ) ) . '</title>'
              . padTimelineCard ( $x0, $y, $w, $h, $event, $lines, $slot )
              . "<circle class=\"pt-dot $slot\" cx=\"" . padChartXY ( $at ) . '" cy="' . padChartXY ( $axisY ) . '" r="5"/></g>';

    }

    foreach ( $spans as $k => list ( $x0, $x1, $lane, $inside ) ) {

      $event = $events [$k];
      $slot  = padChartSlot ( $groups [ $event [5] ] ?? 0 );
      $y     = $spanTop + $lane * 24;

      $marks .= '<g class="pc-mark"><title>' . padChartAttr ( padTimelineSay ( $event ) ) . '</title>'
              . "<rect class=\"$slot\" x=\"" . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $x1 - $x0 ) . '" height="18" rx="4"/>'
              . '<text' . ( $inside ? ' class="pc-in"' : '' ) . ' x="' . padChartXY ( $inside ? $x0 + 6 : $x1 + 6 ) . '" y="' . padChartXY ( $y + 13 ) . '">' . padChartAttr ( $event [3] ) . '</text></g>';

    }

    return "$svg$marks</svg>";

  }

  // The vertical timeline: a line down the left, the date beside each dot, the card to the
  // right of it; a span is a bar along the line as high as its card, its dates both
  // written. The events follow each other at the height their cards take, not to scale.

  function padTimelineColumn ( $events, $groups, $title, $width ) {

    $dateW = 0;
    foreach ( $events as $event )
      $dateW = max ( $dateW, padChartWidth ( $event [1] === NULL ? $event [6] : explode ( ' – ', $event [6] ) [0] ) );

    $dateW = min ( 170, max ( 50, $dateW ) ) + 16;
    $lineX = $dateW + 12;
    $cardX = $lineX + 18;
    $cardW = max ( 120, $width - $cardX - 8 );
    $chars = max ( 12, (int) floor ( ( $cardW - 22 ) / 6.2 ) );
    $top   = 10;

    if ( count ( $groups ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_keys ( $groups ), $cardX, 8, $cardW );
      $top += $legendH + 6;
    } else
      $legend = '';

    $rows = [];
    $y    = $top;

    foreach ( $events as $k => $event ) {
      $lines      = padTimelineLines ( $event [4], $chars, 4 );
      $h          = max ( 28 + 15 * count ( $lines ), 2 * 14 + 12 * ( $event [1] !== NULL ) );
      $rows [$k]  = [ $y, $h, $lines ];
      $y         += $h + 12;
    }

    $height = (int) ceil ( $y );
    $desc   = array_map ( 'padTimelineSay', $events );

    list ( $id, $svg ) = padChartOpen ( 'timeline', [ $events, 'vertical' ], $desc, $title, $width, $height );

    $svg .= padTimelineStyle () . $legend
          . '<line class="pt-line" x1="' . $lineX . '" x2="' . $lineX . '" y1="' . $top . '" y2="' . ( $height - 10 ) . '"/>';

    foreach ( $events as $k => $event ) {

      list ( $y, $h, $lines ) = $rows [$k];

      $slot  = padChartSlot ( $groups [ $event [5] ] ?? 0 );
      $dates = ( $event [1] === NULL ) ? [ $event [6] ] : explode ( ' – ', $event [6] );

      $svg .= '<g class="pc-mark"><title>' . padChartAttr ( padTimelineSay ( $event ) ) . '</title>';

      foreach ( $dates as $i => $date )
        $svg .= '<text class="pt-date" x="' . ( $lineX - 14 ) . '" y="' . padChartXY ( $y + 18 + $i * 14 ) . '" text-anchor="end">' . ( $i ? '– ' : '' ) . padChartAttr ( $date ) . '</text>';

      if ( $event [1] === NULL )
        $svg .= "<circle class=\"pt-dot $slot\" cx=\"$lineX\" cy=\"" . padChartXY ( $y + 14 ) . '" r="6"/>';
      else
        $svg .= "<rect class=\"pt-dot $slot\" x=\"" . ( $lineX - 5 ) . '" y="' . padChartXY ( $y + 6 ) . '" width="10" height="' . padChartXY ( $h - 12 ) . '" rx="5"/>';

      $svg .= padTimelineCard ( $cardX, $y, $cardW, $h, $event, $lines, $slot, FALSE ) . '</g>';

    }

    return "$svg</svg>";

  }

?>
