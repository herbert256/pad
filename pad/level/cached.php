<?php

  // A fragment-cache hit, in place of try/level/go.php: the tag handler does not run and the
  // level renders nothing. level/end.php puts the stored rendering where the level's result
  // goes - padFragmentEnd() in lib/fragment.php.

  $padParm       = $padOpt [$pad] [1] ?? '';
  $padContent    = '';
  $padTagContent = '';
  $padTagResult  = TRUE;

  include PAD . 'level/flags.php';

?>
