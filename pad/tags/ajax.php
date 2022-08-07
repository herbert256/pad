<?php

  if ( ! isset($pAjax) )
    $pAjax = 0;
  
  $pAjax++;

  $pUrl = "{$pad}{$pParm}&pInclude=$pAjax";

  foreach($pPrms_tag as $pPair_key => $pPair_value)
    if ( substr($pPair_key, 0, 4) <> 'pad_' and ! is_array($pPair_key) )
      if ( $pPair_key )
        $pUrl .= '&' . $pPair_key . '=' . urlencode($pPair_value);

  return TRUE;

?>