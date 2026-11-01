<?php

  // Answers on an ordered scale - disagree to agree, never to always - as a likert chart: a
  // bar per question, its answers in their order from left to right, centred on the zero line
  // so the negative answers lie left of it and the positive ones right of it.
  //
  //   {chart 'likert', data='survey', label='question', value='Strongly disagree, Disagree, Neutral, Agree, Strongly agree'}
  //   {chart 'likert', data='ratings', label='item', value='poor, fair, good, great', ranked}
  //
  // label= names the question - else the first field that is no number; value= names the
  // fields of the answers in their order, from the most negative to the most positive - two
  // to eight of them - else every numeric field of the first row in its order. A field holds
  // the number of answers of that kind; a field without a number counts none. Each answer is
  // drawn as its share of the question's total of the answers named, so an answer left out of
  // value= - "no answer", "don't know" - is out of every share as well. The answers below the
  // middle lie left of the zero line, those above it right of it; with an odd count the
  // middle answer is the neutral one and is split by the zero line, half on either side. One
  // horizontal scale of round percentages on both sides takes in the widest side. The answers
  // are coloured as a diverging scale - the first colour on the negative side, the series
  // colour on the positive one, deeper towards the ends, the neutral one grey - which a
  // palette= of the diverging kind replaces; the legend names them in their order. A share
  // with room for it is written on its part; every part has its tooltip - the question, the
  // answer, its count of the total and its share. ranked orders the questions by the share of
  // their positive answers, the largest first. A question without answers keeps its row and
  // draws no bar. A count below zero is an error under the strict check, and counts none
  // without it.

  function padChartLikert ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used    = [];
    $label   = padChartPick ( 'label', $names, $used );
    $answers = padChartFields ( padTagParm ( 'value' ) );

    if ( ! $answers )
      $answers = array_values ( array_filter ( $numbers, fn ( $field ) => $field !== $label ) );

    if ( count ( $answers ) < 2 or count ( $answers ) > 8 ) {
      if ( $padCheckSyntax and $rows )
        padError ( 'a likert chart has two to eight answers in value=, from the most negative to the most positive - it has ' . count ( $answers ) );
      if ( count ( $answers ) < 2 )
        return '';
      $answers = array_slice ( $answers, 0, 8 );
    }

    if ( $padCheckSyntax and $rows )
      foreach ( $answers as $answer )
        if ( ! padChartFieldSomewhere ( $rows, $answer ) )
          padError ( "the likert chart's answer '" . padMakeSafe ( $answer, 30 ) . "' is no field of its rows" );

    $n    = count ( $answers );
    $side = intdiv ( $n, 2 );

    // Per question: its name, the count of each answer and their total.

    $bars  = [];
    $index = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;

      $name   = padChartText ( $row, $label );
      $name   = ( $name !== '' ) ? $name : "Row $index";
      $counts = [];

      foreach ( $answers as $answer ) {

        $count = $row [$answer] ?? NULL;
        $count = padChartFinite ( $count ) ? $count + 0 : 0;

        if ( $count < 0 ) {
          if ( $padCheckSyntax )
            padError ( "the likert chart's row $index, '" . padMakeSafe ( $name, 30 ) . "', has " . padChartNumber ( $count )
                     . " answers '" . padMakeSafe ( $answer, 30 ) . "' - a count is 0 or more" );
          $count = 0;
        }

        $counts [] = $count;

      }

      $bars [] = [ $name, $counts, array_sum ( $counts ) ];

    }

    if ( ! array_filter ( $bars, fn ( $bar ) => $bar [2] > 0 ) )
      return '';

    // ranked: the largest share of positive answers on top - the questions without answers
    // after them, all in their own order where they compare equal.

    $ranked   = padTagParm ( 'ranked', FALSE );
    $positive = fn ( $bar ) => $bar [2] > 0 ? array_sum ( array_slice ( $bar [1], $n - $side ) ) / $bar [2] : -1;

    if ( $ranked !== FALSE and $ranked !== '' and $ranked !== 0 and $ranked !== '0' )
      usort ( $bars, fn ( $p, $q ) => $positive ( $q ) <=> $positive ( $p ) );

    $share = fn ( $count, $total ) => padChartNumber ( round ( $count / $total * 100, 1 ) ) . '%';
    $desc  = [];

    foreach ( $bars as list ( $name, $counts, $total ) ) {

      if ( $total <= 0 ) {
        $desc [] = "$name: no answers";
        continue;
      }

      $parts = [];
      foreach ( $answers as $i => $answer )
        $parts [] = "$answer " . padChartNumber ( $counts [$i] ) . ' (' . $share ( $counts [$i], $total ) . ')';

      $desc [] = "$name: " . padChartNumber ( $total ) . ' answers - ' . implode ( ', ', $parts );

    }

    $title = (string) padTagParm ( 'title', 'Answers' . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'likert', [ $bars, $answers ], $desc, $title, $width, $height );

    $svg = padChartLikertColors ( $svg, $n );

    // The extent of each side: the answers below the middle and half the neutral one left,
    // the rest right - the scale takes in the widest side of any question, on both sides.

    $ext = 0;

    foreach ( $bars as list ( , $counts, $total ) )
      if ( $total > 0 ) {
        $half = ( $n % 2 ) ? $counts [$side] / 2 : 0;
        $ext  = max ( $ext, ( array_sum ( array_slice ( $counts, 0, $side ) ) + $half ) / $total,
                            ( array_sum ( array_slice ( $counts, $n - $side ) ) + $half ) / $total );
      }

    $labelW = 0;
    foreach ( $bars as $bar )
      $labelW = max ( $labelW, padChartWidth ( $bar [0] ) );

    $left  = 12 + min ( $labelW, $width / 3 ) + 12;
    $right = 16;
    $plotW = max ( 20, $width - $left - $right );
    $mid   = $left + $plotW / 2;
    $ticks = padChartTicks ( 0, $ext * 100, max ( 1, min ( 5, (int) ( $plotW / 2 / 60 ) ) ) );
    $reach = end ( $ticks ) / 100;
    $at    = fn ( $p ) => $mid + $p / $reach * $plotW / 2;

    list ( $legend, $legendH ) = padChartLegend ( $answers, $left, 6, $plotW );

    $svg   .= $legend;
    $top    = 10 + $legendH;
    $bottom = 24;
    $plotH  = max ( 1, $height - $top - $bottom );
    $band   = $plotH / count ( $bars );
    $barH   = max ( 2, min ( 28, $band * 0.7 ) );
    $floor  = padChartXY ( $top + $plotH );

    foreach ( $ticks as $tick )
      foreach ( ( $tick > 0 ) ? [ -1, 1 ] : [ 1 ] as $sign ) {
        $p    = padChartXY ( $at ( $sign * $tick / 100 ) );
        $svg .= "<line class=\"pc-grid\" x1=\"$p\" x2=\"$p\" y1=\"" . padChartXY ( $top ) . "\" y2=\"$floor\"/>"
              . "<text x=\"$p\" y=\"" . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '%</text>';
      }

    // The zero line, under the bars: the neutral answer's share is written across it.

    $svg .= '<line class="pc-axis" x1="' . padChartXY ( $mid ) . '" x2="' . padChartXY ( $mid ) . '" y1="' . padChartXY ( $top ) . "\" y2=\"$floor\"/>";

    // A question: its name, and its answers from left to right as parts of one bar, the
    // first starting as far left of the zero line as the negative side reaches.

    foreach ( $bars as $b => list ( $name, $counts, $total ) ) {

      $y    = $top + ( $b + 0.5 ) * $band;
      $svg .= '<text x="' . padChartXY ( $left - 12 ) . '" y="' . padChartXY ( $y + 4 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $left - 24 ) ) . '</text>';

      if ( $total <= 0 )
        continue;

      $from = -( array_sum ( array_slice ( $counts, 0, $side ) ) + ( ( $n % 2 ) ? $counts [$side] / 2 : 0 ) ) / $total;

      foreach ( $answers as $i => $answer ) {

        $to = $from + $counts [$i] / $total;

        if ( $counts [$i] > 0 ) {

          $x0   = $at ( $from );
          $w    = $at ( $to ) - $x0;
          $text = padChartNumber ( round ( $counts [$i] / $total * 100 ) ) . '%';

          $svg .= '<rect class="pc-mark pc-gap ' . padChartSlot ( $i ) . '" x="' . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y - $barH / 2 ) . '" width="' . padChartXY ( $w ) . '" height="' . padChartXY ( $barH ) . '">'
                . '<title>' . padChartAttr ( "$name · $answer: " . padChartNumber ( $counts [$i] ) . ' of ' . padChartNumber ( $total ) . ', ' . $share ( $counts [$i], $total ) ) . '</title></rect>';

          if ( $barH >= 12 and padChartWidth ( $text ) + 6 <= $w )
            $svg .= '<text class="pc-in" x="' . padChartXY ( $x0 + $w / 2 ) . '" y="' . padChartXY ( $y + 4 ) . '" text-anchor="middle" pointer-events="none">' . $text . '</text>';

        }

        $from = $to;

      }

    }

    return "$svg</svg>";

  }

  // The default colours of a likert chart of $n answers, as a diverging scale: the negative
  // answers in the first colour (--pad-chart-2), the positive ones in the series colour, each
  // side deeper towards its end - mixed with the surface, 40% next to the middle to 100% at
  // the end - and the neutral middle a grey; the light and the dark steps as the chart's own
  // colours have them. The svg gets the class pc-lk<n> and the rules for it, which beat the
  // defaults every chart's style writes and lose to a palette's (padChartPaletteRule), whose
  // selector has one class more.

  function padChartLikertColors ( $svg, $n ) {

    $ends  = [ [ '#eb6834', '#d95926' ], [ '#2a78d6', '#3987e5' ] ];
    $grey  = [ '#898781', '#898781' ];
    $face  = [ '#fcfcfb', '#1a1a19' ];
    $side  = intdiv ( $n, 2 );
    $light = $both = '';

    for ( $i = 0; $i < $n; $i++ ) {

      if ( $i < $side )
        list ( $end, $d ) = [ $ends [0], $side - $i ];
      elseif ( $i >= $n - $side )
        list ( $end, $d ) = [ $ends [1], $i - ( $n - $side ) + 1 ];
      else
        list ( $end, $d ) = [ $grey, 0 ];

      $w    = ( $d == 0 ) ? 0.45 : ( ( $side > 1 ) ? 0.4 + 0.6 * ( $d - 1 ) / ( $side - 1 ) : 1 );
      $day  = padChartMix ( $end [0], $face [0], $w );
      $name = '--pad-chart-' . ( $i ? $i + 1 : 'series' );

      $light .= "$name:$day;";
      $both  .= "$name:light-dark($day," . padChartMix ( $end [1], $face [1], $w ) . ');';

    }

    $rules = ".pc-lk$n{{$light}}@supports (color:light-dark(#000,#fff)){.pc-lk$n{{$both}}}";
    $svg   = preg_replace ( '/^(<svg [^>]*class="pad-chart pad-chart-likert)"/', "\\1 pc-lk$n\"", $svg );
    $close = strpos ( $svg, '</style>' );

    return substr ( $svg, 0, $close ) . $rules . substr ( $svg, $close );

  }

  // Two colours mixed, '#rrggbb' each: $w of the first, the rest of the second, each channel
  // rounded.

  function padChartMix ( $one, $two, $w ) {

    $hex = '#';

    for ( $c = 1; $c <= 5; $c += 2 ) {
      $a    = hexdec ( substr ( $one, $c, 2 ) );
      $b    = hexdec ( substr ( $two, $c, 2 ) );
      $hex .= sprintf ( '%02x', (int) round ( $b + ( $a - $b ) * $w ) );
    }

    return $hex;

  }

?>
