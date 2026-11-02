<?php

  // padPalette ( 'viridis', 7 ) - the colours of a palette of pad/subs/themes/palettes/types/, '#rrggbb'
  // each, for a page's PHP: a sequential or diverging palette sampled evenly in $steps steps,
  // a cyclic one evenly round its circle, a categorical one its colours in its order, starting
  // again past its last; without $steps a categorical palette's own colours, twelve of a cyclic
  // one, nine of the others. The name is read in any case, as {widget:palette} and palette= read
  // it - padPalette ( 'romaO' ) is romao. A name that is no palette, or a
  // number of steps that is no whole number from 1 to 256, is reported with padError and
  // answers []. The palettes themselves are the subsystem in pad/subs/themes/palettes/, included when
  // this runs.

  function padPalette ( $name, $steps = NULL ) {

    include_once PAD . 'subs/themes/palettes/_lib/palette.php';

    if ( is_string ( $name ) )
      $name = strtolower ( trim ( $name ) );

    if ( ! is_string ( $name ) or ! padPaletteType ( $name ) )
      return padPaletteHelperError ( "there is no palette named '" . padMakeSafe ( is_scalar ( $name ) ? (string) $name : '', 20 ) . "' - " . implode ( ', ', padPaletteTypes () ) );

    if ( $steps !== NULL ) {
      if ( is_string ( $steps ) and preg_match ( '/^\d+$/D', trim ( $steps ) ) )
        $steps = (int) trim ( $steps );
      if ( ! is_int ( $steps ) or $steps < 1 or $steps > 256 )
        return padPaletteHelperError ( "the steps of '$name' are a whole number from 1 to 256" );
    }

    return padPaletteColors ( $name, $steps );

  }

  function padPaletteHelperError ( $message ) {

    padError ( "padPalette: $message" );

    return [];

  }

?>
