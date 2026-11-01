<?php

  // {countup to=1250/} or {countup 1250/} or {countup}1250{/countup} - a number counted up to
  // its value by the CSS alone. It starts at from= (0) and gets there in duration= seconds (2),
  // after delay= seconds: an integer custom property (@property, <integer>) animated by the CSS
  // and shown by a counter - no script. prefix= and suffix= stand before and after it - '€',
  // '+', '%'. scroll counts as the number scrolls into view, where the browser has
  // scroll-driven animations, and in time where it has not. The number is a whole one, from
  // -999999999 to 999999999, written without grouping; counting down is from= above to=. A
  // screen reader reads the last number only - the counting copy is hidden from it; under
  // reduced motion the number stands at its value.
  //
  //   {countup to=98, suffix='%', scroll/}

  $padFxContent = padEffectContent ();
  $padFxValue   = ( $padFxContent !== NULL ) ? padEffectPlain ( $padFxContent ) : padEffectValue ();
  $padFxTo      = padTagParm ( 'to', NULL );
  $padFxFrom    = padEffectInt ( 'from', 0, -999999999, 999999999 );
  $padFxPrefix  = padEffectText ( 'prefix' );
  $padFxSuffix  = padEffectText ( 'suffix' );
  $padFxScroll  = padEffectFlag ( 'scroll' );
  $padFxTime    = padEffectNumber ( 'duration', 2, 0.1, 60 );
  $padFxDelay   = padEffectNumber ( 'delay', 0, 0, 60 );

  if ( $padFxTo !== NULL )
    $padFxTo = padEffectInt ( 'to', 0, -999999999, 999999999 );
  elseif ( preg_match ( '/^-?\d{1,9}$/D', $padFxValue ) )
    $padFxTo = (int) $padFxValue;
  else {
    padEffectBad ( ( $padFxValue === '' ? 'needs the number it counts to' : "counts to a whole number, not '" . padEffectShow ( $padFxValue ) . "'" )
                   . " - {countup to=1250/}, {countup 1250/} or {countup}1250{/countup}" );
    return '';
  }

  $padFxDigits = max ( strlen ( (string) abs ( $padFxFrom ) ), strlen ( (string) abs ( $padFxTo ) ) ) + ( min ( $padFxFrom, $padFxTo ) < 0 ? 1 : 0 );
  $padFxBefore = ( $padFxPrefix === '' ) ? '' : padEffectEscape ( $padFxPrefix );
  $padFxAfter  = ( $padFxSuffix === '' ) ? '' : padEffectEscape ( $padFxSuffix );

  return padEffectDone ( 'countup',
    padProtect ( padEffectOpen ( 'countup', 'span', [
        'from'     => $padFxFrom,
        'to'       => $padFxTo,
        'digits'   => $padFxDigits,
        'duration' => padEffectSeconds ( $padFxTime ),
        'delay'    => padEffectSeconds ( $padFxDelay )
      ], '', $padFxScroll ? 'pe-scroll' : '' ) . '<span class="pe-shown" aria-hidden="true">' )
    . $padFxBefore . padProtect ( '<span class="pe-count"></span>' ) . $padFxAfter
    . padProtect ( '</span><span class="pad-effect-sr">' ) . $padFxBefore . $padFxTo . $padFxAfter
    . padProtect ( '</span></span>' ) );

?>
