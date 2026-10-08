<?php

  // {emoji 'rocket'} - an emoji by its shortcode, colons or not - {emoji ':tada:'} - in a
  // span with role="img" and the emoji's name as its aria-label: lib/emoji.php. label=
  // gives another name to say. The shortcodes are GitHub's and Slack's common ones; one
  // that is not there is an error that names the nearest one that is.

  $padEmojiFound = padEmoji ( $padParm );

  if ( $padEmojiFound === NULL ) {

    if ( $padCheckSyntax ) {
      $padEmojiNear = padEmojiSuggest ( $padParm );
      padError ( "there is no emoji ':" . padMakeSafe ( trim ( (string) $padParm, ': ' ), 40 ) . ":'"
               . ( $padEmojiNear !== '' ? " - did you mean ':$padEmojiNear:'?" : '' ) );
    }

    return padChartAttr ( $padParm );

  }

  return padEmojiHtml ( $padEmojiFound [0], (string) padTagParm ( 'label', $padEmojiFound [1] ) );

?>
