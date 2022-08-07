<?php

  if     ( $pNull  ) $pBase [$pad] = '';
  elseif ( $pElse  ) $pBase [$pad] = $pFalse [$pad];    
  elseif ( $pText  ) $pBase [$pad] = $pTag_result;
  else                  $pBase [$pad] = $pTrue [$pad];
    
?>