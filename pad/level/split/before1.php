<?php

  $padBefore [$pad] = 1;

  list ( $padPad [$pad], $padBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );

  $padPad [$pad] = '{splitBefore}' . $padPad [$pad] . '{/splitBefore}';
   
?>