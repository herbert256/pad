<?php

  $padBefore [$pad] = 1;

  list ( $padHtml[$pad], $padBase[$pad] ) = explode ( '@end_header@', $padBase[$pad], 2 );

  $padHtml[$pad] = '{before}' . $padHtml[$pad] . '{/before}';

?>