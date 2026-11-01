<?php

  // The body of an effect, whichever way it is written: {effect 'typewriter'}Hello{/effect},
  // the effect: type - {effect:typewriter}Hello{/effect:typewriter} - or the bare name,
  // {typewriter}Hello{/typewriter}, when nothing else has it. The caller sets $padEffectKind,
  // and $padParm to the effect's own first parameter - the second of {effect 'typewriter',
  // 'Hello'/}, whose first names the effect.
  //
  // An effect is a file of subs/effects/types/, and the files there are all the effects there
  // are (padKinds ( 'effects' ), lib/subsystem.php). A pair's content is template, rendered
  // first: like {live}, the pair asks for the end walk and the kind wraps what rendered; a
  // single tag works on its first parameter, or on nothing - a spinner, confetti. The
  // functions are subs/effects/_lib/effect.php, the looks www/assets/effects/.

  include_once PAD . 'subs/effects/_lib/effect.php';

  if ( ! padKind ( 'effects', $padEffectKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no effect named '" . padMakeSafe ( $padEffectKind, 20 ) . "' - " . implode ( ', ', padKinds ( 'effects' ) ) );
    return '';
  }

  // The effect used goes on the xref record, which the reference application shows: the
  // pages that use each effect - once, on its first walk.

  if ( ( $padInfoXref ?? FALSE ) and $padWalk [$pad] == 'start' )
    padInfoXref ( 'effect', $padEffectKind );

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padEffectHtml = padEffectTag ( $padEffectKind, $padPair [$pad] ? $padContent : NULL );

  if ( ! $padPair [$pad] )
    return $padEffectHtml;

  $padContent = $padEffectHtml;

  return TRUE;

?>
