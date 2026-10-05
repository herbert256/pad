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

  return padTrans ( (string) $padTransKey, $padTransVars );

?>
