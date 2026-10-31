<?php

  // combobox - a text field that suggests from a list as it is typed: a native text field
  // with a <datalist> of the choices, which the browser offers by itself, and where the script
  // runs the WAI-ARIA combobox - a list under the field of the choices that hold what is
  // typed, the part that matches marked, walked with the arrow keys, Enter taking a choice,
  // Escape closing the list and then emptying the field. It posts what the field holds, a
  // choice of the list or not: rules='in:...' holds it to the list. The choices are options=,
  // a list, or the rows of the store data= names - field= the field of a row they are read
  // from, its first unless given - or the lines between the tags.

  $padInputField   = (string) padInputTake ( 'field', '' );
  $padInputChoices = padInputList ( 'options', $padInputField );

  if ( ! $padInputChoices and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " has no choices - options='a, b', data= or a line each between its tags" );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputChoices ) {

    global $padInputNow;

    $list = padInputEscape ( "$id-choices" );
    $mine = padInputOwn ( $rest, [ 'autocomplete' => 'off' ] );
    $data = "<datalist id=\"$list\">";

    foreach ( $padInputChoices as $one )
      $data .= '<option value="' . padInputEscape ( $one ) . '"></option>';

    return padInputFrame ( 'combobox', $worded,
             $label
           . '<div class="pi-anchor">'
           . "<input type=\"text\" $head value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\" list=\"$list\"$mine$rest>"
           . '</div>' . $data . '</datalist>'
           . '<span class="pi-hide pi-combo-said" aria-live="polite"></span>',
             $error, [ 'choices' => $padInputChoices ] );

  } );

?>
