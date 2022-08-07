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

  include 'between.php';

  if     ( $pFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $pFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  if     ( ! ctype_alpha ( $pFirst )    ) return pIgnore ('ctype_alpha');
  elseif ( ! pValid      ( $pTag[$p+1]) ) return pIgnore ('pValid');

  include 'type_get.php';
  include 'pair.php';

  if ( ! $pType[$pT] ) return pIgnore ('type_get');
  if ( ! $pPair[$pT] ) return pIgnore ('pair');

  include 'start.php';

?>