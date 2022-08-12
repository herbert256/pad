<?php

  if ( $pRestart )
    include PAD . 'inits/restart.php';    
    
  $pEnd [$p] = strpos ( $pHtml [$p], '}' );

  if ( $pEnd [$p] === FALSE )
    return include 'end.php';

  $pStart [$p] = strrpos ( $pHtml [$p], '{', $pEnd [$p] - strlen($pHtml [$p]) );
  
  if ( $pStart [$p] === FALSE ) {
    $pHtml [$p] = substr ( $pHtml [$p], 0, $pEnd [$p] ) . '&close;' . substr ( $pHtml [$p], $pEnd [$p] + 1) ;
    return;
  }

  $pBetween = substr ( $pHtml [$p], $pStart [$p] + 1, $pEnd [$p]-$pStart [$p] - 1) ;

  $pN = $p+1;
    
  include 'between.php';

  if     ( $pFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $pFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  if     ( ! ctype_alpha ( $pFirst )  ) return pIgnore ('ctype_alpha');
  elseif ( ! pValid      ( $pTag[$pN]) ) return pIgnore ('pValid');

  $pPair  [$pN] = include 'pair.php';
  $pType  [$pN] = include 'type_get.php';
  $pSplit [$pN] = include 'split.php';

  if ( $pPair  [$pN] === NULL  ) return pIgnore ('pair');
  if ( $pType  [$pN] === FALSE ) return pIgnore ('type_get');
  if ( $pSplit [$pN] === FALSE ) return pIgnore ('split');

  include 'start.php';

?>