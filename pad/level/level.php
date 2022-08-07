<?php
    
  if ( $pRestart )
    return include PAD . 'inits/restart.php';    
    
  $pEnd[$p] = strpos($pHtml[$p], '}');

  if ( $pEnd[$p] === FALSE )
    return include 'end.php';

  $pStart[$p] = strrpos ( $pHtml[$p], '{', $pEnd[$p] - strlen($pHtml[$p]) );
  
  if ( $pStart[$p] === FALSE ) {
    $pHtml[$p] = substr($pHtml[$p], 0, $pEnd[$p])
                         . '&close;'
                         . substr($pHtml[$p], $pEnd[$p]+1);
    return;
  }

  $pBetween = substr($pHtml[$p], $pStart[$p]+1, $pEnd[$p]-$pStart[$p]-1);
  $pFirst   = substr($pBetween, 0, 1);

  if     ( $pFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $pFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  $pWords = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag[$p]  = trim($pWords[0] ?? '');
  $pPrms[$p] = trim($pWords[1] ?? '');

  $pPrmsType = ( $pPrms[$p]) ? 'open' : 'none';

  if     ( ! ctype_alpha ( $pFirst )  ) return pIgnore ('ctype_alpha');
  elseif ( ! pValid      ( $pTag[$p]) ) return pIgnore ('pValid');

  include 'type_get.php';
  if ( $pType[$p] === NULL )
    return pIgnore ('type_get');

  include 'pair.php';
  if ( $pPair[$p] === NULL ) 
    return pIgnore ('pair');

  include 'start.php';

?>