<?php

  // {effect:strike}€49{/effect:strike} or {effect:strike 'was 49'/} - a line drawn through the words from left to
  // right, once: an <s>, text no longer right - the old price beside the new one, a task
  // done - read as it is. style= line (the default) or double; color= the ink (the text's own
  // colour when not given); thickness= whole pixels (1 to 12, a tenth of the letters when not
  // given); duration= seconds the stroke takes (0.5), delay= seconds before it starts. scroll
  // draws it as it scrolls into view, where the browser has scroll-driven animations - a
  // strike has no hover: what it says, that the words are no longer right, is no state of the
  // pointer. Over several lines every line is struck. Under reduced motion the line stands
  // drawn; on paper and in forced colours it is the text's own line-through.
  //
  //   {effect:strike delay=0.8}€49{/effect:strike} <b>€29</b>

  $padFxSource   = padEffectSource ();
  $padFxStyle    = padEffectChoice ( 'style', 'line', [ 'line', 'double' ] );
  $padFxColor    = padEffectColor ( 'color', '' );
  $padFxThick    = padEffectThickness ();
  $padFxDuration = padEffectNumber ( 'duration', 0.5, 0.1, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxWhen     = padEffectWhen ( FALSE );

  if ( padEffectPlain ( $padFxSource ) === '' )
    return padEffectNeedsText ( "{effect:strike}€49{/effect:strike} or {effect:strike '€49'/}" );

  return padEffectDone ( 'strike',
    padProtect ( padEffectOpen ( 'strike', 's', [
        'ink'      => $padFxColor,
        'thick'    => $padFxThick,
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay )
      ], '', trim ( "pe-$padFxStyle $padFxWhen" ) ) )
    . $padFxSource
    . padProtect ( '</s>' ) );

?>
