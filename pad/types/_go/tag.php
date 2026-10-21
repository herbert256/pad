<?php

  // Shared loader for the file pair behind a tag; the caller sets $padTagGo to the path without
  // extension. Used by types/app.php, types/common.php and types/pad.php.
  //
  // The .php half runs first through call/ob.php: whatever it prints, followed by the text of
  // the .pad half, becomes $padTagContent - the template text the level goes on to process -
  // while the .php file's return value is returned as the tag's own value.

  // What the application's variables were before the tag's PHP ran: in a page run the
  // level loop is at global scope, so the tag's variables are globals too, and the filter
  // below - anything not already a global - dropped every one of them.

  // One snapshot per nesting depth: a tag's PHP can run another tag through this same
  // file in the same scope - {echo $v | code} does - and a single variable was the inner
  // tag's to clear before the outer one had read it.

  // The sifting is padAppVars, the engine's names taken out by one array_diff_key of PHP's
  // own: a call per global, twice per tag, was half the time of a page full of tags (todo
  // #45), and a loop over all some 450 of them in PHP, twice per tag, still a fifth.

  // A tag without a PHP half runs no code that could set a variable, and is not compared.

  $padCall = "$padTagGo.php";

  if ( ! padFileExists ( $padCall ) and ! ( $GLOBALS ['padInfo'] ?? FALSE ) ) {

    include PAD . 'call/ob.php';

  } else {

    $padTagDepth = ( $padTagDepth ?? 0 ) + 1;

    $padTagBefore [$padTagDepth] = padAppVars ( $GLOBALS );

    // The PHP half of an application or _common tag reads its parameters and options - in
    // $padParm, $padOpt and $padPrm - from copies with { } and | as stand-ins where they came
    // from a value, and the engine has its own back afterwards (lib/options.php,
    // padAppHandOver).

    if ( padAppLevel () ) {
      $padAppHand [$padTagDepth] = [ padAppHandOver (), $pad ];
      $padParm                   = $padOpt [$pad] [1] ?? '';
    }

    include PAD . 'call/ob.php';

    if ( isset ( $padAppHand [$padTagDepth] ) ) {
      padAppHandBack ( ...$padAppHand [$padTagDepth] );
      unset ( $padAppHand [$padTagDepth] );
    }

    // The variables the tag's PHP half added or changed are published to $padLvlFunVar,
    // where the function group of the @ subsystem reads them - the store level/function.php
    // fills for a template driven by a PHP function, and the callbacks fill per phase. The
    // storable-name filter keeps the engine's own pad-prefixed names out.
    //
    // At global scope - a page run - the variables here are the globals, and the copy of
    // $GLOBALS is a third of what get_defined_vars took for the same table; inside a pass a
    // PHP function drives (start/pad/function.php) they are that function's locals. A name
    // set here is a global only at global scope - no global of the name is ever left behind
    // for that function to have bound.

    $padTagScope  = TRUE;
    $padTagGlobal = isset ( $GLOBALS ['padTagScope'] );

    unset ( $padTagScope );

    $padTagAfter = padAppVars ( $padTagGlobal ? $GLOBALS : get_defined_vars () );

    foreach ( $padTagAfter as $padK => $padV )
      if ( ! array_key_exists ( $padK, $padTagBefore [$padTagDepth] )
           or $padTagBefore [$padTagDepth] [$padK] !== $padV )
        $GLOBALS ['padLvlFunVar'] [ $GLOBALS ['pad'] ] [$padK] = $padV;

    unset ( $padTagAfter );

    unset ( $padTagBefore [$padTagDepth] );

    $padTagDepth--;

  }

  $padTagContent = $padCallOB . padFileGet ("$padTagGo.pad");

  // A tag with no PHP half answers TRUE: its template is what it shows. The '' a call of a
  // missing file leaves is an answer of nothing, which takes the @else@ half (level/go.php).

  if ( ! padFileExists ( "$padTagGo.php" ) )
    return TRUE;

  return $padCallPHP;

?>
