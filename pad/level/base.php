<?php

  if     ( $pNull [$p]  ) $pBase [$p] = '';
  elseif ( $pElse [$p]  ) $pBase [$p] = $pFalse [$p];    
  elseif ( $pText [$p]  ) $pBase [$p] = $pTagResult [$p];
  else                    $pBase [$p] = $pTrue [$p];
    
?>