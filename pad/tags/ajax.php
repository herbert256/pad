<?php

  if ( ! isset($padAjax) )
    $padAjax = 0;
  
  $padAjax++;

  $padUrl = "{$padPage}{$padPrm[$pad]}&padInclude=$padAjax";

  foreach($padPrmsTag [$pad] as $padPairKey => $padPairValue)
    if ( substr($padPairKey, 0, 4) <> 'pad_' and ! is_array($padPairKey) )
      if ( $padPairKey )
        $padUrl .= '&' . $padPairKey . '=' . urlencode($padPairValue);

  return TRUE;

?>