<?php

  // A price day by day: a candle per row, its body from the open to the close, its wick
  // from the low to the high.
  //
  //   {chart 'candlestick', data='share', label='date'}
  //   {chart 'candlestick', data='share', label='day', open='o', high='h', low='l', close='c', volume='traded'}
  //
  // label= names the day - else the first field that is no number; open=, high=, low= and
  // close= name the four prices - else the fields of those names. A day that closed at or
  // above its open is green, one that closed lower red. volume= adds the turnover of the
  // day as light bars in the lower fifth, on a scale of its own. A row without four prices
  // is left out.

  function padChartCandlestick ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $label  = padChartPick ( 'label', $names, $used );
    $fields = [];

    foreach ( [ 'open', 'high', 'low', 'close' ] as $price )
      $fields [$price] = (string) padTagParm ( $price, $price );

    $volume = (string) padTagParm ( 'volume' );
    $days   = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $day = [];

      foreach ( $fields as $price => $field ) {
        if ( ! padChartFinite ( $row [$field] ?? NULL ) )
          continue 2;
        $day [$price] = $row [$field] + 0;
      }

      $day ['high']   = max ( $day );
      $day ['low']    = min ( $day );
      $day ['volume'] = ( $volume !== '' and padChartFinite ( $row [$volume] ?? NULL ) ) ? max ( 0, $row [$volume] + 0 ) : NULL;
      $text           = padChartText ( $row, $label );
      $day ['label']  = ( $text !== '' ) ? $text : (string) $index;
      $days []        = $day;

    }

    if ( ! $days ) {
      if ( $padCheckSyntax and $rows )
        padError ( "the candlestick chart has no row with four prices - open='" . $fields ['open'] . "', high='" . $fields ['high']
                 . "', low='" . $fields ['low'] . "', close='" . $fields ['close'] . "'" );
      return '';
    }

    $change = fn ( $day ) => $day ['close'] - $day ['open'];
    $tip    = fn ( $day ) => $day ['label'] . ': open ' . padChartNumber ( $day ['open'] ) . ', high ' . padChartNumber ( $day ['high'] )
                           . ', low ' . padChartNumber ( $day ['low'] ) . ', close ' . padChartNumber ( $day ['close'] )
                           . ( $day ['volume'] !== NULL ? ', volume ' . padChartNumber ( $day ['volume'] ) : '' );
    $desc   = array_map ( $tip, $days );

    $title = (string) padTagParm ( 'title', 'Open, high, low and close' . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'candlestick', $days, $desc, $title, $width, $height );

    // The price axis takes in the lowest low and the highest high - no zero: a price is
    // read against its neighbours.

    $ticks = padChartTicks ( min ( array_column ( $days, 'low' ) ), max ( array_column ( $days, 'high' ) ), $height < 200 ? 3 : 5 );
    $low   = $ticks [0];
    $high  = end ( $ticks );

    if ( $high == $low )
      $high = $low + 1;

    $tickW = 0;
    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );

    $volumes = array_filter ( array_column ( $days, 'volume' ), fn ( $v ) => $v !== NULL );
    $most    = $volumes ? max ( $volumes ) : 0;
    $mostTxt = ( $most > 0 ) ? padNumberAbbreviate ( $most, 1 ) : '';
    $tickW   = max ( $tickW, padChartWidth ( $mostTxt ) );

    $left   = max ( 24, (int) ceil ( $tickW ) + 10 );
    $right  = 10;
    $top    = 10;
    $bottom = 24;
    $plotW  = max ( 1, $width  - $left - $right );
    $plotH  = max ( 1, $height - $top  - $bottom );
    $priceH = ( $most > 0 ) ? $plotH * 0.78 : $plotH;
    $n      = count ( $days );
    $band   = $plotW / $n;
    $bodyW  = max ( 1, min ( 14, $band * 0.6 ) );
    $mid    = fn ( $i ) => $left + ( $i + 0.5 ) * $band;
    $y      = fn ( $v ) => $top + ( $high - $v ) / ( $high - $low ) * $priceH;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    // The days under the plot, thinned out when they would overlap.

    $labelW = 0;
    foreach ( $days as $day )
      $labelW = max ( $labelW, padChartWidth ( $day ['label'] ) + 8 );

    $every = max ( 1, (int) ceil ( $labelW / max ( 1, $band ) ) );

    foreach ( $days as $i => $day )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $mid ( $i ) ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( $day ['label'] ) . '</text>';

    // The volume: light bars in the lower fifth, the highest one a fifth of the plot high,
    // its number - shortened, 1.2M - at the left.

    if ( $most > 0 ) {

      $floor = $top + $plotH;
      $volH  = $plotH * 0.18;

      foreach ( $days as $i => $day )
        if ( $day ['volume'] !== NULL ) {
          $h = $day ['volume'] / $most * $volH;
          $svg .= '<rect class="pc-mark ' . ( $change ( $day ) >= 0 ? 'pc-c3' : 'pc-c8' ) . '" fill-opacity="0.3" x="' . padChartXY ( $mid ( $i ) - $bodyW / 2 ) . '"'
                . ' y="' . padChartXY ( $floor - $h ) . '" width="' . padChartXY ( $bodyW ) . '" height="' . padChartXY ( $h ) . '">'
                . '<title>' . padChartAttr ( $day ['label'] . ': volume ' . padChartNumber ( $day ['volume'] ) ) . '</title></rect>';
        }

      $svg .= '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $floor - $volH + 4 ) . '" text-anchor="end" opacity="0.75">' . padChartAttr ( $mostTxt ) . '</text>'
            . '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - $right ) . '" y1="' . padChartXY ( $floor ) . '" y2="' . padChartXY ( $floor ) . '"/>';

    }

    // A candle per day: the wick from low to high, the body from open to close over it -
    // at least a pixel high, a day that closed where it opened is a line.

    foreach ( $days as $i => $day ) {

      $class = ( $change ( $day ) >= 0 ) ? 3 : 8;
      $top0  = $y ( max ( $day ['open'], $day ['close'] ) );
      $h     = max ( 1, abs ( $y ( $day ['open'] ) - $y ( $day ['close'] ) ) );

      $svg .= '<g class="pc-mark"><title>' . padChartAttr ( $tip ( $day ) ) . '</title>'
            . "<line class=\"pc-l$class\" x1=\"" . padChartXY ( $mid ( $i ) ) . '" x2="' . padChartXY ( $mid ( $i ) ) . '"'
            . ' y1="' . padChartXY ( $y ( $day ['high'] ) ) . '" y2="' . padChartXY ( $y ( $day ['low'] ) ) . '"/>'
            . "<rect class=\"pc-c$class\" x=\"" . padChartXY ( $mid ( $i ) - $bodyW / 2 ) . '" y="' . padChartXY ( $top0 ) . '"'
            . ' width="' . padChartXY ( $bodyW ) . '" height="' . padChartXY ( $h ) . '" rx="1"/></g>';

    }

    return "$svg</svg>";

  }

?>
