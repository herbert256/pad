<?php

  if ( ! isset($padAjax) )
    $padAjax = 0;
  
  $padAjax++;

  $padUrl = "{$padGo}{$padPrm[$pad]}&pInclude=$padAjax";

  foreach($padPrmsTag [$pad] as $padPair_key => $padPair_value)
    if ( substr($padPair_key, 0, 4) <> 'pad_' and ! is_array($padPair_key) )
      if ( $padPair_key )
        $padUrl .= '&' . $padPair_key . '=' . urlencode($padPair_value);

  return TRUE;

?>