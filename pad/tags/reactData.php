<?php

  $padReactId       = padTagParm ( 'id',       'myReactId' );
  $padReactProvider = padTagParm ( 'provider', $padReactId );
  $padReactType     = strtolower ( padTagParm ( 'type', 'record' ) );

  $padCall  = APP . "_providers/$padReactProvider.php";
  $padReact = include PAD . 'call/any.php';

  if ( $padReactType == 'check' )
    $padReact = ( $padReact ) ? 1 : 0;

  padArrayNumericValues ( $padReact );

  $padProviders    [$padReactId] = $padReact;
  $padProvidersLvl [$padReactId] = $pad;

  return '<div id="' 
       . $padReactId
       . '" data="' 
       . padJsonForHtmlAttr ( $padReact ) 
       . '"></div>';

?>