<?php

  // {copy $command} or {copy}npm install {$package}{/copy} - the text in a <pre><code> that
  // selects as a whole with a click, and a button that puts it on the clipboard: a small
  // script, once per page with the CSP nonce, says 'Copied' on the button and to a screen
  // reader. Without scripting there is no button, and the text is selected and copied by
  // hand. label= is the button's text, 'Copy' when not given. lib/copy.php.
  //
  // The text of the single tag is a value, escaped; the content of the pair is template,
  // rendered first - like {live}, the pair asks for the end walk and frames what rendered.

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padCopyLabel = (string) padTagParm ( 'label', 'Copy' );

  if ( $padPair [$pad] ) {

    $padCopyText = trim ( $padContent );
    $padContent  = padCopy ( $padCopyText, $padCopyText, $padCopyLabel );

    return TRUE;

  }

  if ( is_array ( $padParm ) or trim ( (string) $padParm ) === '' ) {
    if ( $padCheckSyntax )
      padError ( "the {copy} needs its text - {copy 'npm install pad'} or {copy}...{/copy}" );
    return '';
  }

  return padCopy ( padWidgetAttr ( $padParm ), (string) $padParm, $padCopyLabel );

?>
