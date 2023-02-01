<?php

  if ( ! isset($padAjax) )
    $padAjax = 0;

  $padAjax++;

  $padAjaxApp  = padTagParm ( 'app',  $app );
  $padAjaxPage = padTagParm ( 'page', $padPrm [$pad] [1] );
  $padAjaxUrl  = $padApp . $padAjaxApp . '&page=' . $padAjaxPage; 

  if ( ! isset ( $padPrm [$pad] ['noInclude'] ) )
    $padAjaxUrl .= '&padInclude=1';

  foreach($padPrm [$pad] as $padPairKey => $padPairValue)
    if ( padValid ($padPairKey) )
      if ( $padPairKey <> 'app' and $padPairKey <> 'page' ) 
        $padAjaxUrl  .= '&' . $padPairKey . '=' . urlencode($padPairValue);

  return TRUE;

?>