<?php

  // {marquee}<span>PHP</span> <span>Go</span> <span>Rust</span>{/marquee} or {marquee 'Breaking:
  // the sale ends tonight'/} - a band whose content scrolls by without end, a logo strip, a
  // ticker. The content goes round once in duration= seconds (20, 2 to 600), to the left or,
  // direction='right', to the right; gap= pixels between one round and the next (48). It
  // stops while the pointer is on it or the focus is in it, and a Pause button that works
  // without script stops it for good - moving content that goes on for more than five seconds
  // must be able to be paused. label= names the band for a screen reader ('Scrolling
  // content'). The band shows the content twice, end to end; the second copy is inert and
  // hidden from a screen reader, its ids taken off. Under reduced motion it stands still and
  // wraps, shown once, without the button.

  $padFxSource    = padEffectSource ();
  $padFxDuration  = padEffectNumber ( 'duration', 20, 2, 600 );
  $padFxDirection = padEffectChoice ( 'direction', 'left', [ 'left', 'right' ] );
  $padFxGap       = padEffectInt ( 'gap', 48, 0, 400 );
  $padFxLabel     = padEffectText ( 'label', 'Scrolling content' );

  if ( padEffectPlain ( $padFxSource ) === '' and ! preg_match ( '/<(img|svg|picture|video)\b/i', $padFxSource ) )
    return padEffectNeedsText ( "{marquee}...{/marquee} or {marquee 'news'/}" );

  $padFxId   = padWidgetId ( 'effect-marquee', [ $padFxSource, $padFxDuration, $padFxDirection, $padFxGap ] );
  // The copy without the attributes that name or point at an id - read on the content with
  // its markup's characters as they are (padUnprotect), and protected again.

  $padFxCopy = padUnprotect ( $padFxSource );

  do
    $padFxCopy = preg_replace ( '/(<[a-z][^>]*?)\s(?:id|for|aria-labelledby|aria-describedby|aria-controls)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '$1', $padFxBefore = $padFxCopy );
  while ( $padFxCopy !== $padFxBefore );

  $padFxCopy = padProtect ( $padFxCopy );

  return padEffectDone ( 'marquee',
    padProtect ( padEffectOpen ( 'marquee', 'div', [
        'duration' => padEffectSeconds ( $padFxDuration ),
        'gap'      => "{$padFxGap}px"
      ], ' role="region" aria-label="' . padWidgetAttr ( $padFxLabel ) . '"', $padFxDirection == 'right' ? 'pe-right' : '' )
      . "<input type=\"checkbox\" class=\"pe-pause\" id=\"$padFxId-pause\"><label class=\"pe-pause-button\" for=\"$padFxId-pause\">"
      . '<span class="pad-effect-sr">Pause</span></label>'
      . '<div class="pe-view"><div class="pe-track"><div class="pe-run">' )
    . $padFxSource
    . padProtect ( '</div><div class="pe-run pe-copy" aria-hidden="true" inert>' )
    . $padFxCopy
    . padProtect ( '</div></div></div></div>' ) );

?>
