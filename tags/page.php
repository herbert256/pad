<?php
  
  $padPagePage    = padTagParm ( 'page',    $padPrm [$pad] [1] );
  $padPageType    = padTagParm ( 'type',    'direct'           );
  $padPageInclude = padTagParm ( 'include', '1'                );

  $padPageParms = [];
  foreach ( $padPrm [$pad] as $padPageKey => $padPageValue )
    if ( padValidVar ( $padPageKey) and ! in_array ( $padPageKey, ['page', 'type'] ) )
      $padPageParms [$padPageKey] = $padPageValue;

  if     ( $padPageType == 'direct'   ) return padPageInclude  ( $padPagePage, $padPageInclude                );
  elseif ( $padPageType == 'function' ) return padPageFunction ( $padPagePage, $padPageParms, $padPageInclude );
  elseif ( $padPageType == 'get'      ) return padPageGet      ( $padPagePage, $padPageParms, $padPageInclude ); 
   
  return NULL;
   
?>