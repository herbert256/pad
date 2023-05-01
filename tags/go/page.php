<?php
  
  if     ( $padPageTyp == 'direct'   ) return padPageInclude  ( $padPagePag, $padPageInc                 );
  elseif ( $padPageTyp == 'function' ) return padPageFunction ( $padPagePag, $padSet [$pad], $padPageInc );
  elseif ( $padPageTyp == 'get'      ) return padPageGet      ( $padPagePag, $padSet [$pad], $padPageInc ); 
  elseif ( $padPageTyp == 'ajax'     ) return padPageAjax     ( $padPagePag, $padSet [$pad], $padPageInc ); 
   
  return NULL;
   
?>