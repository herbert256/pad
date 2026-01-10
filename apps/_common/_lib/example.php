<?php

  
  function layout ( $file ) {

    $pad = ( file_exists( $file ) ) ? padFileGet( $file ) : '';

    if ( strpos($pad, '<!-- PAD: VERTICAL -->') !== false )
      return 'vertical';
    elseif ( strpos($pad, '<!-- PAD: ABOVE -->') !== false )
      return 'above';
    else
      return 'horizontal';

  }


  function onlyResult ( $file ) {

    $pad = ( file_exists( $file ) ) ? padFileGet( $file ) : '';

    if ( strpos($pad, '<!-- PAD: ONLYRESULT -->') !== false ) return ',onlyResult';
    if ( strpos($pad, '{demo')                    !== false ) return ',onlyResult';

    return '';

  }

?>