<?php

  // The add-ons of EAN-13, UPC-A and UPC-E (ISO/IEC 15420, GS1 General Specifications 5.2.2.6):
  // two or five more digits right of the main symbol - the issue of a magazine, the price of a
  // book - in a small symbol of their own, nine modules after it. It starts with the guard
  // 1011, its digits written with the odd (L) and even (G) sets of the EAN bars, 01 between
  // them; the sets of two digits follow their value modulo 4, those of five the check of
  // weights 3 and 9, which is not written. Its digits stand above its bars.
  //
  //   {barcode:ean13 '9780306406157', addon='51995'/}
  //   {barcode:upce '0425261', addon='07'/}
  //
  // padCodeAddon   the coded main symbol with the add-on behind it, or the message why not

  const PAD_ADDON_TWO  = [ 'LL', 'LG', 'GL', 'GG' ];
  const PAD_ADDON_FIVE = [ 'GGLLL', 'GLGLL', 'GLLGL', 'GLLLG', 'LGGLL', 'LLGGL', 'LLLGG', 'LGLGL', 'LGLLG', 'LLGLG' ];

  function padCodeAddon ( $coded, $addon ) {

    if ( $addon === NULL or $addon === FALSE or $addon === '' or ! is_array ( $coded ) )
      return $coded;

    $addon = trim ( padParmText ( $addon === TRUE ? '' : $addon ) );

    if ( ! ctype_digit ( $addon ) or ( strlen ( $addon ) != 2 and strlen ( $addon ) != 5 ) )
      return "an add-on is 2 or 5 digits, not '" . padMakeSafe ( $addon, 10 ) . "'";

    if ( strlen ( $addon ) == 2 )
      $sets = PAD_ADDON_TWO [ (int) $addon % 4 ];
    else
      $sets = PAD_ADDON_FIVE [ ( 3 * ( $addon [0] + $addon [2] + $addon [4] ) + 9 * ( $addon [1] + $addon [3] ) ) % 10 ];

    $main = strlen ( $coded [0] );
    $bars = '1011';

    foreach ( str_split ( $addon ) as $k => $digit )
      $bars .= ( $k ? '01' : '' ) . ( $sets [$k] == 'L' ? PAD_EAN_L [$digit] : PAD_EAN_G [$digit] );

    $coded [0] .= str_repeat ( '0', 9 ) . $bars;
    $coded [5]  = [ $main + 9, $addon, $main ];

    return $coded;

  }

?>
