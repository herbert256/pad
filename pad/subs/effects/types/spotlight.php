<?php

  // {spotlight}<h2>Introducing PAD</h2><p>...</p>{/spotlight} - a block lit by a soft light
  // behind its content: the light sweeps across it from the left and comes to rest where at=
  // says - top (the default), center, bottom, left, right, a corner (top left, bottom right
  // ...) or two percentages, '30% 20%'. The pointer on it, or the focus in it, sweeps it
  // again. color= the light (a CSS colour; --pad-effect-accent, faint, when not given);
  // duration= seconds a sweep takes (2.5), repeat= sweeps (1, or forever), delay= seconds
  // before the first. The light lies behind the content and takes no clicks. Under reduced
  // motion it stands where it rests. Only a pair: it wraps what it lights.
  //
  //   {spotlight at='top left', color='#f59e0b'}<p>Limited offer</p>{/spotlight}

  $padFxContent  = padEffectContent ();
  $padFxAt       = strtolower ( padEffectText ( 'at', 'top' ) );
  $padFxColor    = padEffectColor ( 'color', '' );
  $padFxDuration = padEffectNumber ( 'duration', 2.5, 0.3, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxRepeat   = padEffectRepeat ( 1 );

  // Where the light rests: a word or two of a position, or two percentages.

  $padFxWords = [ 'center' => [ 50, 50 ], 'top' => [ 50, 0 ], 'bottom' => [ 50, 100 ], 'left' => [ 0, 50 ], 'right' => [ 100, 50 ],
                  'top left' => [ 0, 0 ], 'top right' => [ 100, 0 ], 'bottom left' => [ 0, 100 ], 'bottom right' => [ 100, 100 ] ];

  $padFxAt = preg_replace ( '/\s+/', ' ', $padFxAt );
  $padFxAt = [ 'left top' => 'top left', 'right top' => 'top right', 'left bottom' => 'bottom left', 'right bottom' => 'bottom right' ] [$padFxAt] ?? $padFxAt;

  if ( isset ( $padFxWords [$padFxAt] ) )
    [ $padFxX, $padFxY ] = $padFxWords [$padFxAt];
  elseif ( preg_match ( '/^(\d{1,3})% (\d{1,3})%$/D', $padFxAt, $padFxMatch ) and $padFxMatch [1] <= 100 and $padFxMatch [2] <= 100 )
    [ $padFxX, $padFxY ] = [ (int) $padFxMatch [1], (int) $padFxMatch [2] ];
  else {
    padEffectBad ( "has no at '" . padEffectShow ( $padFxAt ) . "' - center, top, bottom, left, right, a corner like top left, or two percentages like '30% 20%'" );
    [ $padFxX, $padFxY ] = [ 50, 0 ];
  }

  if ( $padFxContent === NULL ) {
    padEffectBad ( "wraps what it lights - {spotlight}<h2>...</h2>{/spotlight}" );
    return '';
  }

  return padEffectDone ( 'spotlight',
    padProtect ( padEffectOpen ( 'spotlight', 'div', [
        'x'        => "$padFxX%",
        'y'        => "$padFxY%",
        'light'    => $padFxColor,
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'repeat'   => $padFxRepeat
      ] ) )
    . trim ( $padFxContent )
    . padProtect ( '</div>' ) );

?>
