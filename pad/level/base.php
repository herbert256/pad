<?php

  if     ( $pNull  ) $pBase [$p] = '';
  elseif ( $pElse  ) $pBase [$p] = $pFalse [$p];    
  elseif ( $pText  ) $pBase [$p] = $pTag_result;
  else                  $pBase [$p] = $pTrue [$p];
    
?>