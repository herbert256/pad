<?php

  // {effect:scrollprogress/} - a thin bar along the top of the window that fills as the reader
  // scrolls the page, from its top to its end: how far into a long article, a manual page, a
  // post they are. A scroll-driven animation (animation-timeline: scroll()), no script. at=
  // top (the default) or bottom; size= its height in pixels (3, 1 to 12); color= the bar
  // (--pad-effect-accent when not given). The bar is decoration, hidden from a screen reader
  // and taking no clicks; it stands where the tag is written, anywhere in the page. Where a
  // browser has no scroll-driven animations, and under reduced motion - its moving is motion
  // the scrolling sets off - there is no bar. Only single: it follows the page's own
  // scrolling, and the reading of a part of it could only be followed while no box around
  // that part clips it - a card with rounded corners, a frame's column - so it is not offered.
  //
  //   {effect:scrollprogress color='#e11d48', size=4/}

  $padFxAt    = padEffectChoice ( 'at', 'top', [ 'top', 'bottom' ] );
  $padFxSize  = padEffectInt ( 'size', 3, 1, 12 );
  $padFxColor = padEffectColor ( 'color', '' );

  $padFxContent = padEffectContent ();

  if ( $padFxContent !== NULL )
    padEffectBad ( "is written single, it follows the scrolling of the whole page - {effect:scrollprogress/}" );

  return padEffectDone ( 'scrollprogress',
    padProtect ( padEffectOpen ( 'scrollprogress', 'span', [ 'size' => "{$padFxSize}px", 'ink' => $padFxColor ], ' aria-hidden="true"', "pe-$padFxAt" )
               . '<span class="pe-bar"></span></span>' ) )
    . ( ( $padFxContent === NULL ) ? '' : trim ( $padFxContent ) );

?>
