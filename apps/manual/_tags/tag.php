<?php

  $tagClose  = ( padTagParm ('close')  ) ? '/' : '';
  $tagSingle = ( padTagParm ('single') ) ? '/' : '';
  $tagName = $padParm === 'tag' ? 'type:tag' : ( str_contains ( $padParm, ':' ) ? $padParm : "pad:$padParm" );

  return "<b>&open;$tagClose$tagName$tagSingle&close;</b>";

?>
