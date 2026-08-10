<?php

  // Records the closing tag that level/tag.php has just located and takes the level's
  // content from between the two.
  //
  // $padBaseSet becomes the text between open and close, and $padEnd [$pad] is pushed out
  // to the closing tag's '}' - stepping over any '}' that would leave the braces in between
  // unbalanced. level/pipes/end.php then peels a closing pipe off that text, and if the
  // closing tag still carries options they replace the opening tag's ($padPrmTypeSet =
  // 'close', which level/parms/parms.php acts on).

  $padBaseSet = substr ( $padBaseBase, 0, $padPos );
  $padPairSet = TRUE;

  $padEnd [$pad] = $padPos;

  do {

    $padEnd [$pad] = strpos ( $padOut [$pad], '}', $padEnd [$pad] + 1 );

    // Running out of text is reported under the strict syntax check; the lenient walk
    // takes everything to the end as the level's span, and stepping stops either way -
    // searching on from FALSE was an endless loop with the error action set to continue.

    if ( $padEnd [$pad] === FALSE ) {

      if ( $padCheckSyntax )
        padError ("Closing } not found");

      $padEnd [$pad] = strlen ( $padOut [$pad] ) - 1;
      break;

    }

    $padBetweenCheck = substr ($padOut [$pad], $padPos+1, $padEnd [$pad]-$padPos-1);

  } while ( substr_count($padBetweenCheck, '{') != substr_count($padBetweenCheck, '}') );

  include PAD . 'level/pipes/end.php';

  $padWordsCheck = preg_split ( "/[\s]+/", $padBetweenCheck, 2, PREG_SPLIT_NO_EMPTY );

  if ( count($padWordsCheck) > 1 ) {
    $padPrmTypeSet = 'close';
    $padBetween    = $padBetweenCheck;
    include PAD . 'level/between.php';
  }

?>