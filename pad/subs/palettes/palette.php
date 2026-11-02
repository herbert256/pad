<?php

  // The body of {pad:palette 'viridis', steps=7/} - the colours of a palette as a row of swatches,
  // each with its hex value written on it in black or white, whichever reads better
  // (padPaletteInk). The palette is a file of subs/palettes/types/ (padPaletteTypes,
  // _lib/palette.php); steps= is how many colours, a whole number from 1 to 256 - without it
  // a categorical palette shows its own colours, a cyclic one twelve, the others nine; title=
  // names the row for a screen reader, else the palette's name and the count do. The row is a
  // list - role="list", a swatch an item - so a screen reader counts the colours and reads
  // their values.
  //
  // The swatches' colours are the values' own, a class of each value with its rule; the rule
  // and the rest of the look are styles once per page, with the nonce.

  include_once PAD . 'subs/palettes/_lib/palette.php';

  $padPaletteName = is_string ( $padParm ) ? strtolower ( trim ( padParmText ( $padParm ) ) ) : '';

  if ( ! padPaletteType ( $padPaletteName ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no palette named '" . padMakeSafe ( $padPaletteName, 20 ) . "' - " . implode ( ', ', padPaletteTypes () ) );
    return '';
  }

  if ( $padPair [$pad] and $padCheckSyntax )
    padError ( "the {pad:palette} takes no content - it is written single, {pad:palette 'viridis'/}" );

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'palette', $padPaletteName );

  $padPaletteSteps = padTagParm ( 'steps', NULL );

  if ( $padPaletteSteps !== NULL ) {

    $padPaletteText = is_scalar ( $padPaletteSteps ) ? trim ( padParmText ( (string) $padPaletteSteps ) ) : '';

    if ( preg_match ( '/^\d+$/D', $padPaletteText ) and (int) $padPaletteText >= 1 and (int) $padPaletteText <= 256 )
      $padPaletteSteps = (int) $padPaletteText;
    else {
      if ( $padCheckSyntax )
        padError ( "the {pad:palette} has no steps '" . padMakeSafe ( $padPaletteText, 20 ) . "' - a whole number from 1 to 256" );
      $padPaletteSteps = NULL;
    }

  }

  $padPaletteData   = padPaletteData ( $padPaletteName );
  $padPaletteColors = padPaletteColors ( $padPaletteName, $padPaletteSteps );
  $padPaletteTitle  = trim ( padParmText ( (string) padTagParm ( 'title', '' ) ) );
  $padPaletteCount  = count ( $padPaletteColors );

  if ( $padPaletteTitle === '' )
    $padPaletteTitle = $padPaletteData ['name'] . ', ' . $padPaletteCount . ( $padPaletteCount == 1 ? ' colour' : ' colours' );

  // A swatch's colours are a class of its hex value - pp-c-440154 - whose rule, the colour and
  // its ink, is written once per page with the nonce: in a style attribute the style-src
  // nonce dropped them, and every swatch was blank.

  $padPaletteHtml  = '';
  $padPaletteRules = '';

  foreach ( $padPaletteColors as $padPaletteOne ) {

    $padPaletteClass = 'pp-c-' . strtolower ( ltrim ( $padPaletteOne, '#' ) );

    if ( padWidgetOnce ( "style:$padPaletteClass" ) )
      $padPaletteRules .= ".pad-palette .$padPaletteClass{background:$padPaletteOne;color:" . padPaletteInk ( $padPaletteOne ) . '}';

    $padPaletteHtml .= "<span class=\"pp-swatch $padPaletteClass\" role=\"listitem\"><code>$padPaletteOne</code></span>";

  }

  $padPaletteStyle = '';

  if ( padWidgetOnce ( 'style:palette' ) )
    $padPaletteStyle = padUnprotect ( padWidgetSheet ( padWidgetFile ( 'palettes/palette.css' ) ) );

  if ( $padPaletteRules !== '' )
    $padPaletteStyle .= '<style' . padStyleNonce () . ">$padPaletteRules</style>";

  return padProtect ( $padPaletteStyle
       . '<div class="pad-palette pad-palette-' . $padPaletteName . '" role="list" aria-label="' . padWidgetAttr ( $padPaletteTitle ) . '">'
       . $padPaletteHtml . '</div>' );

?>
