<?php

  $padBefore [$pad] = 1;

  list ( $padHtml[$pad], $padBase[$pad] ) = explode ( '@start@', $padBase[$pad], 2 );

  $padHtml[$pad] = '{pad}' . $padHtml[$pad] . '{/pad}';
   
?>