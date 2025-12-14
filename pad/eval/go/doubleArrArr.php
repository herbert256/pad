<?php

  if     ( $opr == 'EQ'  ) $now = ($left == $right) ? 1 : '';
  elseif ( $opr == 'NE'  ) $now = ($left <> $right) ? 1 : '';

  else padError ( 'ToDo' );

?>