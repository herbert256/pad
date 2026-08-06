<?php

  // The {reactData} tag: renders the mount point a React component reads its data from.
  //
  // The provider named by provider= (defaulting to id=) is run out of the application's
  // _providers/ directory through call/any.php, its numeric strings are turned into real
  // numbers, and the result is parked in $padProviders under the id - which is what makes it
  // reachable from a later provider and from the @providers at-group. The tag's own value is
  // a <div id="..." data="...">, the JSON html-escaped and then padEscape'd so it survives
  // both the attribute and the rest of the PAD pass; read it in JS with getAttribute('data'),
  // never dataset.data. type='check' collapses the provider's result to 1 or 0.

  $padReactId       = padTagParm ( 'id',       'myReactId' );
  $padReactProvider = padTagParm ( 'provider', $padReactId );
  $padReactType     = strtolower ( padTagParm ( 'type', 'record' ) );

  $padCall  = APP . "_providers/$padReactProvider.php";
  $padReact = include PAD . 'call/any.php';

  if ( $padReactType == 'check' )
    $padReact = ( $padReact ) ? 1 : 0;

  padArrayNumericValues ( $padReact );

  $padProviders [$padReactId] = $padReact;

  return '<div id="' 
       . $padReactId
       . '" data="' 
       . padJsonForHtmlAttr ( $padReact ) 
       . '"></div>';

?>