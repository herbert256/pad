<?php

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

?>