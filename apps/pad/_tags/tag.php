<?php

  $tagClose = ( padTagParm ('close') ) ? '/' : '';

  return '<b>&open;</b><b>' . $tagClose . $padParm . '</b><b>&close;</b>';

?>