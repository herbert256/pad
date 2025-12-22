<?php

  if ( ! padTagParm ( 'htmlAttrJson' ) )
    return padSelect ( $padTag [$pad] );

  $padSelectData = padSelect ( $padTag [$pad] );

  padArrayNumericValues ( $padSelectData );

  return padJsonForHtmlAttr ( $padSelectData ) ;

?>