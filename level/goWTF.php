<?php

  $padTagCnt [$pad]++;

  $padTagContent = '';
  $padTagResult  = include pad . "types/" . $padType [$pad] . ".php";

  if ( $padTagResult === NULL or $padTagContent === NULL)
    return;

  if ( ! is_array ($padTagResult) and $padTagResult !== TRUE and $padTagResult !== FALSE ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  padSetTrueFalse ( $padTagContent , $padTagTrue, $padTagFalse );

  if     ( $padTagResult === TRUE       ) $padTagTrueFalse = TRUE;
  elseif ( $padTagResult === FALSE      ) $padTagTrueFalse = FALSE;
  elseif ( ! is_array ( $padTagResult ) ) $padTagTrueFalse = TRUE;
  elseif ( count ( $padTagResult )      ) $padTagTrueFalse = TRUE;
  else                                    $padTagTrueFalse = FALSE;

  if ( $padTagTrueFalse )
    if ( strpos ( $padTagTrue, '@content@' ) !== FALSE )
      $padContent = str_replace ( '@content@', $padContent, $padTagTrue );
    else
      $padContent .= $padTagTrue;
  else
    if ( strpos ( $padTagFalse, '@content@' ) !== FALSE )
      $padFalse [$pad] = str_replace ( '@content@', $padFalse [$pad], $padTagFalse );
    else
      $padFalse [$pad] .= $padTagFalse;

?>