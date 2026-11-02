<?php

  // dropzone - a file dropped on a frame or chosen, its name and size listed: a native
  // <input type="file"> in a frame a file can be dropped on - the whole frame its label, so a
  // click opens the browser's file chooser as well. The form that holds it posts multipart by
  // itself ({input:form} sees the file field), and the page's PHP takes the file with padUpload (
  // 'name', $types, $max ), whose refusal - a file too large, of a type the page does not take
  // - the field shows as its message. accept= is the file field's own and says which files the
  // chooser offers; a file dropped that it does not take is refused in the page, and so is one
  // larger than limit= - '2M', '500K' - the size padUpload is handed too. multiple takes more
  // than one, and then needs a name of a list - files[] - or PHP keeps the last file alone. A
  // file field cannot be refilled, and its rules are padUpload's.

  padInputNoRules ( 'a file field is checked by padUpload' );

  $padInputLimit = padInputTake ( 'limit', '' );
  $padInputBytes = ( $padInputLimit === '' or $padInputLimit === NULL ) ? 0 : padUploadBytes ( $padInputLimit );

  if ( $padInputLimit !== '' and $padInputLimit !== NULL and ! $padInputBytes and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " has no usable limit '" . padMakeSafe ( (string) $padInputLimit, 20 ) . "' - a size, '2M' or '500K'" );

  if ( padAttrsTrue ( padInputPeek ( 'multiple', FALSE ) ) and ! str_ends_with ( padInputName (), '[]' ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " takes multiple files under a name of a list - '" . padMakeSafe ( padInputName (), 30 ) . "[]'" );

  padInputTake ( 'value' );

  $padInputMany = padAttrsTrue ( padInputPeek ( 'multiple', FALSE ) );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputBytes, $padInputMany ) {

    $own  = padInputEscape ( $id );
    $text = preg_match ( '/^<label[^>]*>(.*)<\/label>$/s', $label, $match ) ? $match [1] : '';

    return padInputFrame ( 'dropzone', $worded,
             ( $text !== '' ? "<span class=\"pi-label\" id=\"$own-label\">$text</span>" : '' )
           . "<label class=\"pi-drop\" for=\"$own\">"
           . "<input type=\"file\" $head$rest" . ( $text !== '' && ! str_contains ( $rest, 'aria-labelledby' ) ? " aria-labelledby=\"$own-label $own-hint\"" : '' ) . '>'
           . "<span class=\"pi-drop-hint\" id=\"$own-hint\"><strong>" . ( $padInputMany ? 'Drop files here' : 'Drop a file here' ) . '</strong> or click to choose</span>'
           . '</label>'
           . '<ul class="pi-drop-files" hidden></ul>'
           . '<p class="pi-drop-said" role="status" aria-live="polite"></p>',
             $error, [ 'limit' => $padInputBytes, 'size' => $padInputBytes ? padUploadSize ( $padInputBytes ) : '' ] );

  } );

?>
