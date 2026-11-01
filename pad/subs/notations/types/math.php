<?php

  // math - a formula written in AsciiMath, the notation of asciimath.org, as MathML: the math
  // element every current browser draws without a script. AsciiMath and not TeX, as TeX's
  // braces are PAD's: x^2, a/b, sqrt(x), root(3)(x), sum_(i=1)^n, int_0^1, lim_(x->oo),
  // [[a,b],[c,d]], text(...), sin, log, alpha, oo, ->, <=, != and the rest of its symbols.
  // display='block' sets the formula apart on its own line, centred; alttext= is the text a
  // screen reader without MathML says - the formula as written unless given. The style puts a
  // stroke through cancel(x).
  //
  //   {math}x = (-b +- sqrt(b^2 - 4ac)) / (2a){/math}
  //   {math 'sum_(i=1)^n i = (n(n+1))/2', display='block'/}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padMathBlock = ( padNotationChoice ( 'display', 'inline', [ 'inline', 'block' ] ) == 'block' );

  return padNotationWrap ( padNotationStyle ( 'math', [ 'stroke' => [ '#c8102e', '#ff6b7d' ] ] )
       . padMathMl ( $padNotationText, $padMathBlock, padNotationOption ( 'alttext' ) ) );

?>
