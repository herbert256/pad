<?php

  // The body of {palette 'viridis', steps=7/} - the colours of a palette as a row of swatches,
  // each with its hex value written on it in black or white, whichever reads better
  // (padPaletteInk). The palette is a file of subs/palettes/types/ (padPaletteTypes,
  // _lib/palette.php); steps= is how many colours, a whole number from 1 to 256 - without it
  // a categorical palette shows its own colours, a sequential or diverging one nine; title=
  // names the row for a screen reader, else the palette's name and the count do. The row is a
  // list - role="list", a swatch an item - so a screen reader counts the colours and reads
  // their values.
  //
  // The swatches' colours are the values' own, written on each swatch (style="background:...")
  // as the poll's bars are; the rest of the look is a style once per page, with the nonce.

  include_once PAD . 'subs/palettes/_lib/palette.php';

  $padPaletteName = is_string ( $padParm ) ? strtolower ( trim ( padParmText ( $padParm ) ) ) : '';

  if ( ! padPaletteType ( $padPaletteName ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no palette named '" . padMakeSafe ( $padPaletteName, 20 ) . "' - " . implode ( ', ', padPaletteTypes () ) );
    return '';
  }

  if ( $padPair [$pad] and $padCheckSyntax )
    padError ( "the {palette} takes no content - it is written single, {palette 'viridis'/}" );

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'palette', $padPaletteName );

  $padPaletteSteps = padTagParm ( 'steps', NULL );

  if ( $padPaletteSteps !== NULL ) {

    $padPaletteText = is_scalar ( $padPaletteSteps ) ? trim ( padParmText ( (string) $padPaletteSteps ) ) : '';

    if ( preg_match ( '/^\d+$/D', $padPaletteText ) and (int) $padPaletteText >= 1 and (int) $padPaletteText <= 256 )
      $padPaletteSteps = (int) $padPaletteText;
    else {
      if ( $padCheckSyntax )
        padError ( "the {palette} has no steps '" . padMakeSafe ( $padPaletteText, 20 ) . "' - a whole number from 1 to 256" );
      $padPaletteSteps = NULL;
    }

  }

  $padPaletteData   = padPaletteData ( $padPaletteName );
  $padPaletteColors = padPaletteColors ( $padPaletteName, $padPaletteSteps );
  $padPaletteTitle  = trim ( padParmText ( (string) padTagParm ( 'title', '' ) ) );
  $padPaletteCount  = count ( $padPaletteColors );

  if ( $padPaletteTitle === '' )
    $padPaletteTitle = $padPaletteData ['name'] . ', ' . $padPaletteCount . ( $padPaletteCount == 1 ? ' colour' : ' colours' );

  $padPaletteHtml = '';

  foreach ( $padPaletteColors as $padPaletteOne )
    $padPaletteHtml .= '<span class="pp-swatch" role="listitem" style="background:' . $padPaletteOne . ';color:' . padPaletteInk ( $padPaletteOne ) . '">'
                     . "<code>$padPaletteOne</code></span>";

  $padPaletteStyle = '';

  if ( padWidgetOnce ( 'style:palette' ) )
    $padPaletteStyle = '<style' . padStyleNonce () . '>'
                     . preg_replace ( '/\R[ \t]*/', '', trim ( (string) padFileGet ( PAD . 'subs/palettes/assets/palette.css' ) ) )
                     . '</style>';

  return padProtect ( $padPaletteStyle
       . '<div class="pad-palette pad-palette-' . $padPaletteName . '" role="list" aria-label="' . padWidgetAttr ( $padPaletteTitle ) . '">'
       . $padPaletteHtml . '</div>' );

?>
