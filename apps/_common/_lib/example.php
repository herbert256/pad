<?php

  
  function layout ( $file ) {

    $pad = ( file_exists( $file ) ) ? fileGet( $file ) : '';

    if ( strpos($pad, '<!-- PAD: VERTICAL -->') !== false )
      return 'vertical';
    elseif ( strpos($pad, '<!-- PAD: ABOVE -->') !== false )
      return 'above';
    else
      return 'horizontal';

  }


  function onlyResult ( $file ) {

    $pad = ( file_exists( $file ) ) ? fileGet( $file ) : '';

    if ( strpos($pad, '<!-- PAD: ONLYRESULT -->') !== false ) return ',onlyResult';
    if ( strpos($pad, '{demo')                    !== false ) return ',onlyResult';
    if ( str_ends_with($file, 'index.pad')                  ) return ',onlyResult';

    return '';

  }

?>