<?php


  $padTagGo .= "_tags/". $padTag [$pad];

  $padCall   = "$padTagGo.php";
  $padTagPhp = include pad . 'call/anyNoOne.php';

  $padTagContent = $padCallOB . padFileGetContents ("$padTagGo.html");

  return $padTagPhp;








  $padTagCnt [$pad]++;

  $padTagContent = '';
  $padTagResult  = include pad . "types/" . $padType [$pad] . ".php";

  if ( $padTagResult === NULL or $padTagContent === NULL)
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

  if     ( $padTagResult === TRUE       ) $padTagTrueFalse = TRUE;
  elseif ( $padTagResult === FALSE      ) $padTagTrueFalse = FALSE;
  elseif ( ! is_array ( $padTagResult ) ) $padTagTrueFalse = TRUE;
  elseif ( count ( $padTagResult )      ) $padTagTrueFalse = TRUE;
  else                                    $padTagTrueFalse = FALSE;

  include pad . 'level/flags.php';

  padGetTrueFalse ( $padTagContent, $padTagTrue, $padTagFalse );


  if ( $padTagTrueFalse === FALSE )
    $padFalse [$pad] .= $padTagFalse;

if ( is_array ( $padTagResult ) ) {
  $padData [$pad] = $padTagResult;
  
}

  if ( $padTagResult === TRUE AND $padTagTrue <> '' )
    return $padTagTrue;

  if ( is_scalar($padTagResult) and strpos($padTagResult , '@content@') !== FALSE )
    $padTagResult = str_replace('@content@', $padTrue [$pad], $padTagResult);

?>  

?>