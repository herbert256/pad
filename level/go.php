<?php

  $padTagCnt [$pad]++;

  $padTagContent = '';
  $padTagResult  = include pad . "types/" . $padType [$pad] . ".php";

  if     ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( $padTagResult === INF         ) $padTagResult = NULL;
  elseif ( $padTagResult === NAN         ) $padTagResult = NULL;

  if ( $padTagResult === NULL )
    return;

  if ( ! is_array ($padTagResult) and $padTagResult !== TRUE and $padTagResult !== FALSE ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  padGetTrueFalse ( $padTagContent , $padTagTrue, $padTagFalse );

  if     ( $padTagResult === TRUE       ) $padTagTrueFalse = TRUE;
  elseif ( $padTagResult === FALSE      ) $padTagTrueFalse = FALSE;
  elseif ( ! is_array ( $padTagResult ) ) $padTagTrueFalse = TRUE;
  elseif ( count ( $padTagResult )      ) $padTagTrueFalse = TRUE;
  else                                    $padTagTrueFalse = FALSE;

  if ( $padTagTrueFalse and $padTagTrue)
    if ( strpos ( $padTagTrue, '@content@' ) !== FALSE )
      $padContent = str_replace ( '@content@', $padContent, $padTagTrue );
    else
      $padContent = $padTagTrue;

  if ( ! $padTagTrueFalse and $padTagFalse )
    if ( strpos ( $padTagFalse, '@content@' ) !== FALSE )
      $padFalse [$pad] = str_replace ( '@content@', $padFalse [$pad], $padTagFalse );
    else
      $padFalse [$pad] = $padTagFalse;

  return $padTagResult;

?>