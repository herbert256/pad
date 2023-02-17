<?php
  
  $padPageApp = padTagParm ( 'app',     $app );
  $padPagePag = padTagParm ( 'page',    $padPrm [$pad] [1] );
  $padPageTyp = padTagParm ( 'type',    'direct'           );
  $padPageInc = padTagParm ( 'include', '1'                );

  $padPagePrm = [];
  foreach ( $padPrm [$pad] as $padPageKey => $padPageValue )
    if ( padValidVar ( $padPageKey) and ! in_array ( $padPageKey, ['page', 'type'] ) )
      $padPagePrm [$padPageKey] = $padPageValue;

  if     ( $padPageTyp == 'direct'   ) return padPageInclude  ( $padPagePag, $padPageInc                           );
  elseif ( $padPageTyp == 'function' ) return padPageFunction ( $padPagePag, $padPagePrm, $padPageInc              );
  elseif ( $padPageTyp == 'get'      ) return padPageGet      ( $padPagePag, $padPagePrm, $padPageInc, $padPageApp ); 
  elseif ( $padPageTyp == 'ajax'     ) return padPageAjax     ( $padPagePag, $padPagePrm, $padPageInc, $padPageApp ); 
   
  return NULL;
   
?>