<?php

  // A fragment-cache hit, in place of try/level/go.php: the tag handler does not run and the
  // level renders nothing. level/end.php puts the stored rendering where the level's result
  // goes - padFragmentEnd() in lib/fragment.php.
  //
  // A rendering with {nocache} parts is rendered instead: the stored text as a template
  // that holds the parts again (lib/nocache.php), so they render here, on every hit.

  $padParm       = $padOpt [$pad] [1] ?? '';
  $padContent    = '';
  $padTagContent = '';
  $padTagResult  = TRUE;

  if ( $padFragment [$pad] ['nocache'] )
    $padContent = padNocacheTemplate ( $padFragment [$pad] ['body'], $padFragment [$pad] ['nocache'] );

  include PAD . 'level/flags.php';

?>
