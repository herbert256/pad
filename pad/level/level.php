<?php

  if ( $pRestart )
    include PAD . 'inits/restart.php';    
    
  $pEnd [$p] = strpos ( $pHtml [$p], '}' );

  if ( $pEnd [$p] === FALSE )
    return include 'end.php';

  $pStart [$p] = strrpos ( $pHtml [$p], '{', $pEnd [$p] - strlen($pHtml [$p]) );
  
  if ( $pStart [$p] === FALSE ) {
    $pHtml [$p] = substr_replace ( $pHtml [$p], '&close;', $pEnd [$p], 1 );
    return;
  }

  $pBetween = substr ( $pHtml [$p], $pStart [$p] + 1, $pEnd [$p]-$pStart [$p] - 1 ) ;
  $pFirst   = substr ( $pBetween , 0, 1 );

  if     ( $pFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $pFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  include 'setup.php';    
  include 'between.php';

  if     ( ! ctype_alpha ( $pFirst )    ) return pIgnore ('ctype_alpha');
  elseif ( ! pValid      ( $pTag [$p] ) ) return pIgnore ('pValid');

  $pPair  [$p] = include 'pair.php';
  $pType  [$p] = include 'type_get.php';
  $pSplit [$p] = include 'split.php';

  if ( $pPair  [$p] === NULL  ) return pIgnore ('pair');
  if ( $pType  [$p] === FALSE ) return pIgnore ('type_get');
  if ( $pSplit [$p] === FALSE ) return pIgnore ('split');

  include 'start.php';

?>