<?php

  $tagClose = ( padTagParm ('close') ) ? '/' : '';

  return '<b>&open;</b><b>' . $tagClose . $padOpt [$pad] [1] . '</b><b>&close;</b>';

?>