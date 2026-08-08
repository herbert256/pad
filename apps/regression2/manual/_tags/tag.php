<?php

  $tagClose  = ( padTagParm ('close')  ) ? '/' : '';
  $tagSingle = ( padTagParm ('single') ) ? '/' : '';

  return "<b>&open;$tagClose$padParm$tagSingle&close;</b>";

?>