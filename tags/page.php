<?php
  
  $padPagePage = padTagParm ( 'page', $padPrm [$pad] [1] );
  $padPageType = padTagParm ( 'type', 'include'          );

  $padPageParms = [];
  foreach ( $padPrm [$pad] as $padPageKey => $padPageValue )
    if ( padValidVar ( $padPageKey) and ! in_array ( $padPageKey, ['page', 'type'] ) )
      $padPageParms [$padPageKey] = $padPageValue;

  if     ( $padPageType == 'include'  ) return padPageInclude  ( $padPagePage                );
  elseif ( $padPageType == 'function' ) return padPageFunction ( $padPagePage, $padPageParms );
  elseif ( $padPageType == 'get'      ) return padPageGet      ( $padPagePage, $padPageParms ); 
   
  return $padPageContent;

?>