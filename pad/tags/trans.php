<?php

  // {trans 'cart.items', count=$n, name=$user}: a key of the _lang/ catalogs in the
  // request's locale - padTrans in lib/locale.php. count picks the plural form and replaces
  // %d, every other name replaces :name. The items are read raw, as {attrs} reads them, so
  // a substitution may be called name, content or data without meeting an engine option.

  $padTransKey  = NULL;
  $padTransVars = [];

  foreach ( padAttrsItems () as [ $padTransName, $padTransExpr ] )
    if ( $padTransName === '' and $padTransKey === NULL )
      $padTransKey = padEval ( $padTransExpr );
    elseif ( $padTransName !== '' )
      $padTransVars [$padTransName] = padEval ( $padTransExpr );

  if ( $padTransKey === NULL or $padTransKey === '' ) {
    if ( $padCheckSyntax )
      padError ( "the {trans} needs a key - {trans 'cart.items'}" );
    return '';
  }

  // The catalog's text is the application's own, markup and all; what replaces a :name is
  // data, and goes in as {$name} would write it - through the data chain, sanitize by
  // default - and so does a key no catalog knows, which is answered as itself: {trans
  // 'greeting', name=$user} put a visitor's name into the page as live HTML.

  foreach ( $padTransVars as $padTransName => $padTransValue )
    if ( is_string ( $padTransValue ) )
      foreach ( $padDataDefaultEnd as $padTransOne )
        $padTransVars [$padTransName] = padEval ( $padTransOne, $padTransVars [$padTransName] );

  if ( ! array_key_exists ( (string) $padTransKey, padTransCatalog () ) )
    foreach ( $padDataDefaultEnd as $padTransOne )
      $padTransKey = padEval ( $padTransOne, (string) $padTransKey );

  return padTrans ( (string) $padTransKey, $padTransVars );

?>
