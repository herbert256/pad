<?php

  // The body of a rich form field, written {input:slider 'volume'/} or - when nothing else has
  // the name - bare, {input:slider 'volume'/}. The caller sets $padInputKind; a kind is a file of
  // subs/inputs/types/ - its script and its style are www/assets/inputs/<kind>.js and .css - and the
  // files there are all the kinds there are (padInputTypes, subs/inputs/_lib/input.php).
  //
  // The content between a field's tags is taken before the level walks it, as written - the
  // choices of a combobox, a line each, or the text a markdown editor starts with - so it may
  // hold braces; the field is the tag's value, written once.

  include_once PAD . 'subs/inputs/_lib/input.php';

  if ( ! padInputType ( $padInputKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no input named '" . padMakeSafe ( $padInputKind, 20 ) . "' - " . implode ( ', ', padInputTypes () ) );
    return '';
  }

  // The kind used goes on the xref record, which the reference application shows: the pages
  // that use each kind.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'input', $padInputKind );

  $padInputSource = $padContent;
  $padContent     = '';

  $padInputHtml = padInputTag ( $padInputKind, $padInputSource );

  $padData [$pad] = padDefaultData ();

  return ( $padInputHtml === '' ) ? FALSE : $padInputHtml;

?>
