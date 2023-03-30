<?php
  
  $padPageApp = padTagParm ( 'app',     $padApp );
  $padPagePag = padTagParm ( 'page',    $padOpt [$pad] [1] );
  $padPageTyp = padTagParm ( 'type',    'direct'           );
  $padPageInc = padTagParm ( 'include', '1'                );

  if     ( $padPageTyp == 'direct'   ) return padPageInclude  ( $padPagePag, $padPageInc                              );
  elseif ( $padPageTyp == 'function' ) return padPageFunction ( $padPagePag, $padSet [$pad], $padPageInc              );
  elseif ( $padPageTyp == 'get'      ) return padPageGet      ( $padPagePag, $padSet [$pad], $padPageInc, $padPageApp ); 
  elseif ( $padPageTyp == 'ajax'     ) return padPageAjax     ( $padPagePag, $padSet [$pad], $padPageInc, $padPageApp ); 
   
  return NULL;
   
?>