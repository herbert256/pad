<?php

  // colorpicker - a colour picked from a row of swatches, typed as a code or chosen in the
  // browser's own picker: a native <input type="color">, which posts #rrggbb, and - where the
  // script runs - the swatches and a text field for the hexadecimal code, each kept in step
  // with the colour. swatches= the colours of the row, a list of #rgb or
  // #rrggbb, eight of the palette unless given; value= the colour before a post, #000000 as a
  // colour field has unless given. #rgb is written #rrggbb, which is all a colour field takes.

  $padInputSwatches = [];

  foreach ( padInputList ( 'swatches' ) ?: [ '#2a78d6', '#1a8a5a', '#e8a317', '#eb6834', '#d43c3b', '#8e44ad', '#1a1a19', '#ffffff' ] as $padInputOne )
    if ( preg_match ( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/iD', $padInputOne ) )
      $padInputSwatches [] = padInputColour ( $padInputOne );
    elseif ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padInputWhat () . " has a swatch '" . padMakeSafe ( $padInputOne, 20 ) . "' - a colour is #rgb or #rrggbb" );

  $padInputNow ['value'] = padInputValue ( '#000000' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputSwatches ) {

    global $padInputNow;

    $value = padInputColour ( $padInputNow ['value'] );

    return padInputFrame ( 'colorpicker', $worded,
             $label
           . '<div class="pi-row pi-color-row">'
           . "<input type=\"color\" $head value=\"" . padInputEscape ( $value ) . "\"$rest>"
           . '<input type="text" class="pi-color-code" value="' . padInputEscape ( $value ) . '" size="8" maxlength="7" spellcheck="false" autocomplete="off"'
           . ' aria-label="' . padInputEscape ( "$worded, hexadecimal code" ) . '" hidden>'
           . '</div>'
           . '<div class="pi-swatches" role="group" aria-label="' . padInputEscape ( "Swatches for $worded" ) . '" hidden></div>',
             $error, [ 'swatches' => $padInputSwatches ] );

  } );

?>
