<?php

  // Calls a plain PHP function - the handler behind the php: prefix.
  //
  // With no arguments written in the template the piped-in $value becomes the single argument,
  // so {echo $s | php:strtoupper} works; otherwise $parm is passed through as the argument
  // list and $value is ignored unless the template placed it there with @.

  if ( ! padPhpAllowed ( $name ) )
    return padError ( "the PHP function '$name' is not allowed by \$padPhpFunctions" );

  // An empty piped value is still the argument of a function that needs one: {$title |
  // ucfirst} with an empty $title called ucfirst() with nothing and ended the request. A
  // function that takes none - {echo php:time()} - is still called bare.

  // A field inside a quoted literal of the call - php:str_contains('{$c}', ',') - or a value
  // piped in from one comes as a string holding its syntax characters as the stand-ins of
  // padProtect (lib/encoding.php), which the page's exit puts back: the comma of "Paris, TX"
  // was not found and strlen counted its three bytes. The function is handed the characters,
  // and what it answers is protected again, as eval/parms/pad.php does for the built-in
  // pipes, so nothing a value brought becomes code on the way. Not over an opening pipe's
  // template text, its tags masked by padPipeMarks, whose own quotes would come back as
  // stand-ins. The piped value too, so the word on it below still finds it among the
  // arguments when @ put it there.

  $padPhpStand = FALSE;
  $padPhpText  = function ( $one ) use ( &$padPhpStand ) {
    if ( ! is_string ( $one ) or ! str_contains ( $one, "\xEE" ) or str_contains ( $one, padPipeMarks () [0] ) )
      return $one;
    $text = padUnprotect ( $one );
    if ( $text !== $one )
      $padPhpStand = TRUE;
    return $text;
  };

  $value = $padPhpText ( $value );

  foreach ( $parm as $padPhpKey => $padPhpOne )
    if ( is_array ( $padPhpOne ) )
      array_walk_recursive ( $parm [$padPhpKey], function ( &$one ) use ( $padPhpText ) { $one = $padPhpText ( $one ); } );
    else
      $parm [$padPhpKey] = $padPhpText ( $padPhpOne );

  // An empty piped value is still the argument of a function that needs one: {$title |
  // ucfirst} with an empty $title called ucfirst() with nothing and ended the request. A
  // function that takes none - {echo php:time()} - is still called bare.

  if ( ! count ($parm) and ( $value !== '' or ( new ReflectionFunction ( $name ) ) -> getNumberOfRequiredParameters () ) )
    $parm [0] = $value;

  // A callable among the arguments is a call of its own, held to the same list.

  if ( $padPhpRefused = padPhpCallables ( $name, $parm ) )
    return padError ( $padPhpRefused );

  // Arguments the function will not take - the wrong type or number, a value out of its
  // range, a division by zero - are named as the template's mistake under the strict check,
  // with a word on the piped value when the call left it out, and empty in the lenient walk.
  // PHP's error went up raw: a "CATCH:" report, or an uncaught one with $padErrorTry off.

  try {

    $padPhpResult = call_user_func_array ($name, $parm);

    if ( $padPhpStand )
      if ( is_string ( $padPhpResult ) )
        $padPhpResult = padProtect ( $padPhpResult );
      elseif ( is_array ( $padPhpResult ) )
        array_walk_recursive ( $padPhpResult, function ( &$one ) { $one = padProtect ( $one ); } );

    return $padPhpResult;

  } catch ( TypeError | ValueError | ArithmeticError $padPhpError ) {

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "the PHP function '$name' refused its arguments: " . $padPhpError->getMessage ()
                 . ( ( $parm and is_scalar ( $value ) and $value !== '' and ! in_array ( $value, $parm, TRUE ) )
                     ? " - a php: call with arguments is handed no piped value, @ puts it in its place" : '' ) );

    return '';

  }

?>
