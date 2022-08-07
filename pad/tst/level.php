<?php
    
  if ( $pRestart )
    return include PAD . 'inits/restart.php';    
    
  $pEnd [$pad] = strpos($pHtml[$pad], '}');

  if ( $pEnd[$pad] === FALSE )
    return include 'end.php';

  $pStart [$pad] = strrpos ( $pHtml[$pad], '{', $pEnd[$pad] - strlen($pHtml[$pad]) );
  
  if ( $pStart [$pad] === FALSE ) {
    $pHtml [$pad] = substr($pHtml[$pad], 0, $pEnd[$pad])
                         . '&close;'
                         . substr($pHtml[$pad], $pEnd[$pad]+1);
    return;
  }

  $pBetween = substr($pHtml[$pad], $pStart[$pad]+1, $pEnd[$pad]-$pStart[$pad]-1);
  $pFirst   = substr($pBetween, 0, 1);

  if     ( $pFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $pFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  $pad_words = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag   = trim($pad_words[0] ?? '');
  $pPrms  = trim($pad_words[1] ?? '');

  $pPrms_type = ( $pPrms ) ? 'open' : 'none';

  if     ( ! ctype_alpha ( $pFirst )  ) return pIgnore ('ctype_alpha');
  elseif ( ! pad_valid   ( $pTag )    ) return pIgnore ('pad_valid');

  $pPair_result = include 'pair.php';
  if ( $pPair_result === NULL ) 
    return pIgnore ('pair');

  $pType = include 'type_get.php';
  if ( $pType === NULL )
    return pIgnore ('type_get');

  include 'start.php';

?>