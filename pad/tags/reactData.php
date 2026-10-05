<?php

  // The {reactData} tag: renders the mount point a React component reads its data from.
  //
  // The provider named by provider= (defaulting to id=) is run out of the application's
  // _providers/ directory through call/any.php, and the result is parked in $padProviders
  // under the id - which is what makes it reachable from a later provider and from the
  // @providers at-group. The tag's own value is a <div id="..." data="...">, the JSON
  // html-escaped and then padEscape'd so it survives both the attribute and the rest of the
  // PAD pass; read it in JS with getAttribute('data'), never dataset.data. type='check'
  // collapses the provider's result to 1 or 0.

  $padReactId       = padTagParm ( 'id',       'myReactId' );
  $padReactProvider = padTagParm ( 'provider', $padReactId );
  $padReactType     = strtolower ( padTagParm ( 'type', 'record' ) );

  // The provider is a file of _providers/ by its plain name, and the id an attribute value:
  // provider='../_config/config' ran any PHP file of the tree, and an id with a quote in it
  // broke out of the attribute.

  if ( ! padValidName ( $padReactProvider ) )
    return padError ( "the provider '$padReactProvider' is no plain name of a file in _providers/" );

  $padCall  = APP . "_providers/$padReactProvider.php";
  $padReact = include PAD . 'call/any.php';

  if ( $padReactType == 'check' )
    $padReact = ( $padReact ) ? 1 : 0;

  // The values keep the types they came with: db() answers integer and float columns as
  // numbers (lib/db.php), and a provider's own PHP decides the rest. Every numeric-looking
  // string was made a number here, so "007" became 7 and a username "2024" an int.

  $padProviders [$padReactId] = $padReact;

  return '<div id="' 
       . htmlspecialchars ( (string) $padReactId, ENT_QUOTES, 'UTF-8' )
       . '" data="' 
       . padJsonForHtmlAttr ( $padReact ) 
       . '"></div>';

?>