<?php

  if     ( $opr == 'LT'  ) $now = ($left <   $right) ? 1 : '';
  elseif ( $opr == 'LE'  ) $now = ($left <=  $right) ? 1 : '';
  elseif ( $opr == 'EQ'  ) $now = ($left ==  $right) ? 1 : '';

?>