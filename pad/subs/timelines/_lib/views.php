<?php

  // Extra views reuse the established event/date parser and chart scale helpers.
  // Lane and milestone fields are looked up in the original row using the preserved key.
  function padTimelineLane ( $event, $rows, $field ) {
    $name = $field !== '' ? padChartText ( padChartRow ( $rows [ $event [7] ] ?? [] ), $field ) : $event [5];
    return $name !== '' ? $name : 'Events';
  }

  function padTimelineMilestone ( $event, $rows, $field ) {
    if ( $field === '' )
      return $event [1] === NULL;
    $value = strtolower ( trim ( padChartText ( padChartRow ( $rows [ $event [7] ] ?? [] ), $field ) ) );
    return in_array ( $value, [ '1', 'true', 'yes', 'on' ], TRUE );
  }

  function padTimelineRoadmap ( $events, $rows, $title, $period, $lane, $milestone ) {
    $periods = [];
    foreach ( $events as $event ) {
      $year = gmdate ( 'Y', $event [0] );
      $key = match ( $period ) {
        'month' => gmdate ( 'Y-m', $event [0] ),
        'quarter' => $year . '-Q' . (intdiv ( (int) gmdate ( 'n', $event [0] ) - 1, 3 ) + 1),
        default => $year
      };
      $periods [$key] [ padTimelineLane ( $event, $rows, $lane ) ] [] = $event;
    }
    $html = '<section class="pad-timeline-roadmap" aria-label="' . padChartAttr ( $title ) . '">'
          . '<style>.pad-timeline-roadmap{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr));gap:1rem}'
          . '.pad-timeline-roadmap section{padding:1rem;border:1px solid currentColor;border-radius:.5rem}'
          . '.pad-timeline-roadmap h3,.pad-timeline-roadmap h4{margin:0 0 .65rem}.pad-timeline-roadmap h4{margin-top:1rem}'
          . '.pad-timeline-roadmap ol{list-style:none;padding:0;margin:0}.pad-timeline-roadmap li{margin:.75rem 0}'
          . '.pad-timeline-roadmap time,.pad-timeline-roadmap small{display:block}.pad-timeline-roadmap p{margin:.25rem 0}'
          . '.pad-timeline-roadmap .pt-milestone{font-size:.8em;font-weight:600}</style>';
    foreach ( $periods as $key => $lanes ) {
      $html .= '<section data-period="' . padChartAttr ( $key ) . '"><h3>' . padChartAttr ( $key ) . '</h3>';
      foreach ( $lanes as $name => $items ) {
        $html .= '<h4>' . padChartAttr ( $name ) . '</h4><ol>';
        foreach ( $items as $event ) {
          $html .= '<li><time datetime="' . gmdate ( 'Y-m-d', $event [0] ) . '">' . padChartAttr ( $event [6] ) . '</time>';
          if ( padTimelineMilestone ( $event, $rows, $milestone ) )
            $html .= '<span class="pt-milestone">◆ Milestone</span> ';
          $html .= '<strong>' . padChartAttr ( $event [3] ) . '</strong>';
          if ( $event [4] !== '' )
            $html .= '<p>' . padChartAttr ( $event [4] ) . '</p>';
          $html .= '</li>';
        }
        $html .= '</ol>';
      }
      $html .= '</section>';
    }
    return $html . '</section>';
  }

  function padTimelineSwimlane ( $events, $rows, $groups, $title, $lane, $milestone, $width ) {
    $lanes = [];
    foreach ( $events as $event )
      $lanes [ padTimelineLane ( $event, $rows, $lane ) ] [] = $event;
    $left = min ( 180, max ( 90, $width * .2 ) );
    $plotW = $width - $left - 24;
    $low = min ( array_column ( $events, 0 ) );
    $high = $low;
    foreach ( $events as $event )
      $high = max ( $high, $event [1] ?? $event [0] );
    if ( $high === $low ) { $low -= 86400; $high += 86400; }
    $padding = ( $high - $low ) * .06;
    $low -= $padding; $high += $padding;
    $x = fn ( $stamp ) => $left + ( $stamp - $low ) / ( $high - $low ) * $plotW;
    $height = 50 + count ( $events ) * 48 + count ( $lanes ) * 26;
    [ $id, $svg ] = padChartOpen ( 'timeline', [ $events, $lanes, 'swimlane' ], array_map ( 'padTimelineSay', $events ), $title, $width, $height );
    $svg .= padTimelineStyle ();
    foreach ( padTimelineTicks ( $low, $high, $plotW ) as $tick => $label ) {
      $at = padChartXY ( $x ( $tick ) );
      $svg .= '<line class="pt-leader" x1="' . $at . '" x2="' . $at . '" y1="30" y2="' . ( $height - 8 ) . '"/>'
            . '<text class="pt-date" text-anchor="middle" x="' . $at . '" y="18">' . padChartAttr ( $label ) . '</text>';
    }
    $y = 40;
    foreach ( $lanes as $name => $items ) {
      $svg .= '<g class="pt-lane" aria-label="' . padChartAttr ( $name ) . '"><title>' . padChartAttr ( $name ) . '</title>'
            . '<text class="pt-label" x="8" y="' . ( $y + 16 ) . '">' . padChartAttr ( padChartFit ( $name, $left - 20 ) ) . '</text>';
      $y += 26;
      foreach ( $items as $event ) {
        $x0 = $x ( $event [0] ); $x1 = $x ( $event [1] ?? $event [0] );
        $slot = padChartSlot ( $groups [ $event [5] ] ?? 0 );
        $svg .= '<g class="pc-mark"><title>' . padChartAttr ( padTimelineSay ( $event ) ) . '</title>';
        if ( $event [1] !== NULL )
          $svg .= '<rect class="' . $slot . '" x="' . padChartXY ( $x0 ) . '" y="' . $y . '" width="' . padChartXY ( max ( 2, $x1 - $x0 ) ) . '" height="12" rx="3"/>';
        if ( padTimelineMilestone ( $event, $rows, $milestone ) )
          $svg .= '<path class="pt-milestone ' . $slot . '" d="M' . padChartXY ( $x0 ) . ' ' . ( $y - 2 ) . 'l8 8 -8 8 -8 -8z"/>';
        elseif ( $event [1] === NULL )
          $svg .= '<circle class="' . $slot . '" cx="' . padChartXY ( $x0 ) . '" cy="' . ( $y + 6 ) . '" r="5"/>';
        // Labels sit inside the plot, even at its right boundary; full text is in title/desc.
        $labelX = min ( $x0, $left + max ( 0, $plotW - padChartWidth ( $event [3] ) ) );
        $svg .= '<text class="pt-label" x="' . padChartXY ( $labelX ) . '" y="' . ( $y + 30 ) . '">'
              . padChartAttr ( padChartFit ( $event [3], $width - 16 - $labelX ) ) . '</text></g>';
        $y += 48;
      }
      $svg .= '</g>';
    }
    return $svg . '</svg>';
  }

?>
