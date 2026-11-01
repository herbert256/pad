<?php

  // GS1 DataBar Omnidirectional (ISO/IEC 24724), the small barcode of fresh food and coupons: a
  // GTIN-14 in 96 modules, read from any direction. The 13 digits before the check digit are a
  // number cut into four data characters - two outside of 16 modules, two inside of 15 - each
  // written as four bars and four spaces whose widths the combinatorial algorithm of the
  // standard's annex B gives for its value and group (tables 1 and 2), between two finder
  // patterns (table 3) that also carry the checksum of the widths, modulo 79 with the weights
  // 3^n modulo 79 (table 5). A space and a bar of one module stand at both ends; the symbol
  // needs no quiet zone, two modules are drawn.
  //
  //   {databar '09501101530003'/}          the GTIN-14, (01) in front or not
  //   {databar '4006381333931'/}           a GTIN-13, 12 or 8 is made 14 digits with zeros
  //
  // padCodeDatabar   the bars, or the message why the text is no GTIN

  // The groups of the outside characters (0 - 4) and the inside ones (5 - 8): the first value
  // of the group, the number of values of its even (outside) or odd (inside) widths, the
  // modules and the widest element of its odd and its even widths.

  const PAD_DATABAR_GROUPS = [
    [    0,   1, 12, 4, 8, 1 ], [  161,  10, 10, 6, 6, 3 ], [  961,  34, 8, 8, 4, 5 ], [ 2015,  70, 6, 10, 3, 6 ],
    [ 2715, 126,  4, 12, 1, 8 ], [    0,   4,  5, 10, 2, 7 ], [  336,  20, 7, 8, 4, 5 ], [ 1036,  48, 9, 6, 6, 3 ],
    [ 1516,  81, 11, 4, 8, 1 ] ];

  const PAD_DATABAR_FINDERS = [
    [ 3, 8, 2, 1, 1 ], [ 3, 5, 5, 1, 1 ], [ 3, 3, 7, 1, 1 ], [ 3, 1, 9, 1, 1 ], [ 2, 7, 4, 1, 1 ],
    [ 2, 5, 6, 1, 1 ], [ 2, 3, 8, 1, 1 ], [ 1, 5, 7, 1, 1 ], [ 1, 3, 9, 1, 1 ] ];

  function padCodeDatabar ( $text ) {

    $gtin = preg_replace ( '/^\(01\)\s*/', '', trim ( $text ) );

    if ( ! ctype_digit ( $gtin ) or ! in_array ( strlen ( $gtin ), [ 8, 12, 13, 14 ] ) )
      return 'a databar barcode is a GTIN - 8, 12, 13 or 14 digits with the check digit, (01) in front or not';

    $gtin  = str_pad ( $gtin, 14, '0', STR_PAD_LEFT );
    $check = padBarcodeCheck ( substr ( $gtin, 0, 13 ) );

    if ( $gtin [13] != $check )
      return 'the check digit of the GTIN ' . ltrim ( substr ( $gtin, 0, 13 ), '0' ) . $gtin [13] . " is $check, not " . $gtin [13];

    // The four data characters: the left and right halves of the number, each an outside and
    // an inside character.

    $value = (int) substr ( $gtin, 0, 13 );
    $left  = intdiv ( $value, 4537077 );
    $right = $value % 4537077;
    $chars = [ intdiv ( $left, 1597 ), $left % 1597, intdiv ( $right, 1597 ), $right % 1597 ];
    $width = [];

    foreach ( $chars as $c => $char ) {

      $outside = ( $c % 2 == 0 );
      $group   = $outside ? ( $char < 161 ? 0 : ( $char < 961 ? 1 : ( $char < 2015 ? 2 : ( $char < 2715 ? 3 : 4 ) ) ) )
                          : ( $char < 336 ? 5 : ( $char < 1036 ? 6 : ( $char < 1516 ? 7 : 8 ) ) );

      list ( $first, $count, $oddModules, $evenModules, $oddWidest, $evenWidest ) = PAD_DATABAR_GROUPS [$group];

      $odd  = $outside ? intdiv ( $char - $first, $count ) : ( $char - $first ) % $count;
      $even = $outside ? ( $char - $first ) % $count : intdiv ( $char - $first, $count );

      $oddWidths  = padCodeDatabarWidths ( $odd,  $oddModules,  4, $oddWidest,  $outside ? 1 : 0 );
      $evenWidths = padCodeDatabarWidths ( $even, $evenModules, 4, $evenWidest, $outside ? 0 : 1 );

      for ( $k = 0; $k < 4; $k++ ) {
        $width [$c] [2 * $k]     = $oddWidths [$k];
        $width [$c] [2 * $k + 1] = $evenWidths [$k];
      }

    }

    // The checksum: every width times 3^n modulo 79, n its place in the four characters.

    $sum = 0;

    foreach ( $width as $c => $elements )
      foreach ( $elements as $k => $w )
        $sum += $w * padCodeDatabarWeight ( 8 * $c + $k );

    $sum %= 79;

    if ( $sum >= 8 )
      $sum++;

    if ( $sum >= 72 )
      $sum++;

    // The elements from left to right: guard, left outside character, left finder, left inside
    // character from right to left, right inside character, right finder reversed, right
    // outside character from right to left, guard - a space first.

    $elements = array_merge (
      [ 1, 1 ],
      $width [0],
      PAD_DATABAR_FINDERS [ intdiv ( $sum, 9 ) ],
      array_reverse ( $width [1] ),
      $width [3],
      array_reverse ( PAD_DATABAR_FINDERS [ $sum % 9 ] ),
      array_reverse ( $width [2] ),
      [ 1, 1 ] );

    $bars = '';

    foreach ( $elements as $k => $w )
      $bars .= str_repeat ( $k % 2 ? '1' : '0', $w );

    return [ $bars, [ [ strlen ( $bars ) / 2, "(01)$gtin" ] ], [], 2 ];

  }

  // The widths of the elements of a value - the standard's getRSSwidths: $elements widths
  // adding up to $modules, none wider than $widest, at least one of them narrow unless
  // $noNarrow, numbered in the standard's order.

  function padCodeDatabarWidths ( $value, $modules, $elements, $widest, $noNarrow ) {

    $widths = [];
    $narrow = 0;

    for ( $bar = 0; $bar < $elements - 1; $bar++ ) {

      for ( $width = 1, $narrow |= 1 << $bar; ; $width++, $narrow &= ~ ( 1 << $bar ) ) {

        $sub = padCodeDatabarCombins ( $modules - $width - 1, $elements - $bar - 2 );

        if ( ! $noNarrow and ! $narrow and $modules - $width - ( $elements - $bar - 1 ) >= $elements - $bar - 1 )
          $sub -= padCodeDatabarCombins ( $modules - $width - ( $elements - $bar ), $elements - $bar - 2 );

        if ( $elements - $bar - 1 > 1 ) {
          $less = 0;
          for ( $max = $modules - $width - ( $elements - $bar - 2 ); $max > $widest; $max-- )
            $less += padCodeDatabarCombins ( $modules - $width - $max - 1, $elements - $bar - 3 );
          $sub -= $less * ( $elements - 1 - $bar );
        } elseif ( $modules - $width > $widest )
          $sub--;

        $value -= $sub;

        if ( $value < 0 )
          break;

      }

      $value   += $sub;
      $modules -= $width;
      $widths [] = $width;

    }

    $widths [] = $modules;

    return $widths;

  }

  // The number of combinations of $r out of $n, computed as the annex computes it - which
  // answers 1 where $r is more than $n.

  function padCodeDatabarCombins ( $n, $r ) {

    if ( $n - $r > $r ) {
      $min = $r;
      $max = $n - $r;
    } else {
      $min = $n - $r;
      $max = $r;
    }

    $value = 1;
    $j     = 1;

    for ( $i = $n; $i > $max; $i-- ) {
      $value *= $i;
      if ( $j <= $min )
        $value = intdiv ( $value, $j++ );
    }

    for ( ; $j <= $min; $j++ )
      $value = intdiv ( $value, $j );

    return $value;

  }

  function padCodeDatabarWeight ( $n ) {

    $weight = 1;

    for ( $k = 0; $k < $n; $k++ )
      $weight = $weight * 3 % 79;

    return $weight;

  }

?>
