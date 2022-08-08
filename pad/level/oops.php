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

  include PAD . 'level/setup.php';

  include 'between.php';

  if     ( $pFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $pFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  if     ( ! ctype_alpha ( $pFirst )  ) return pIgnore ('ctype_alpha');
  elseif ( ! pValid      ( $pTag[$p]) ) return pIgnore ('pValid');

  $pTrue  [$p] = include 'true.php';
  $pClose [$p] = include 'close.php';
  $pFalse [$p] = include 'else.php';
  $pType  [$p] = include 'type_get.php';

  if ( $pTrue  [$p] === NULL ) return pIgnore ('true');
  if ( $pClose [$p] === NULL ) return pIgnore ('close');
  if ( $pFalse [$p] === NULL ) return pIgnore ('false');
  if ( $pType  [$p] === NULL ) return pIgnore ('type_get');

  include 'start.php';

?>
