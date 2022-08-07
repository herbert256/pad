<?php

  if     ( $pNull  ) $pBase [$p] = '';
  elseif ( $pElse  ) $pBase [$p] = $pFalse [$p];    
  elseif ( $pText  ) $pBase [$p] = $pTagResult;
  else               $pBase [$p] = $pTrue [$p];
    
?>