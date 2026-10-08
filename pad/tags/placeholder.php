<?php

  // {placeholder '600x300'} - a grey box with crossing lines and its size in the middle, as
  // inline SVG by lib/placeholder.php: the stand-in for an image that is not there yet. The
  // first parameter is the size, width x height; text= is written instead of the size, and
  // is the accessible name. ratio= gives the proportions instead - '16:9', drawn width=
  // pixels wide (default 640) and saying its ratio - and fluid makes the box as wide as its
  // container, its height following from the proportions.
  //
  //   {placeholder '600x300', text='Hero image'}
  //   {placeholder ratio='16:9', fluid}

  $padPlaceholderRatio = trim ( (string) padTagParm ( 'ratio' ) );
  $padPlaceholderWidth = (int) padTagParm ( 'width', 640 );

  if ( $padPlaceholderRatio !== '' ) {

    $padPlaceholderSize = padPlaceholderSize ( $padPlaceholderRatio, ':' );

    if ( $padPlaceholderSize === NULL ) {
      if ( $padCheckSyntax )
        padError ( "the placeholder has no ratio '" . padMakeSafe ( $padPlaceholderRatio, 20 ) . "' - write it as 16:9" );
      $padPlaceholderSize = [ 16, 9 ];
    }

    $padPlaceholderWidth = max ( 1, $padPlaceholderWidth );
    $padPlaceholderSize  = [ $padPlaceholderWidth, (int) round ( $padPlaceholderWidth * $padPlaceholderSize [1] / $padPlaceholderSize [0] ) ];

  } else {

    $padPlaceholderSize = padPlaceholderSize ( $padParm );

    if ( $padPlaceholderSize === NULL ) {
      if ( $padCheckSyntax )
        padError ( "the placeholder has no size '" . padMakeSafe ( $padParm, 20 ) . "' - write it as 600x300, or give a ratio='16:9'" );
      $padPlaceholderSize = [ 640, 360 ];
    }

  }

  // Without a text, a box drawn from a ratio says its ratio, one drawn from a size its size.

  $padPlaceholderText = (string) padTagParm ( 'text', $padPlaceholderRatio !== '' ? preg_replace ( '/\s+/', '', $padPlaceholderRatio ) : '' );

  return padPlaceholder ( $padPlaceholderSize [0], $padPlaceholderSize [1], $padPlaceholderText, (bool) padTagParm ( 'fluid', FALSE ) );

?>
