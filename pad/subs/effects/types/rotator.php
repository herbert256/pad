<?php

  // {rotator}teams, makers, you{/rotator} or {rotator 'fast, simple, yours'/} - one place in a
  // line where its phrases take turns: each stands for hold= seconds (2.5, 0.5 to 30), then
  // slides up and away for the next, round after round - repeat= rounds (3, or forever) after
  // delay= seconds - and the first stands again when they end. The phrases are the text
  // between commas, two to twelve, their markup not drawn; the place is as wide as the widest,
  // so the line does not shift as they turn. The phrases are in the page as a list, one
  // after the other with their commas: a screen reader reads them all, once, and under
  // reduced motion, without the style and where a browser has no lh unit they stand there
  // just so - teams, makers, you. The copy of the first that closes a round is hidden.
  //
  //   <h1>Built for {rotator hold=2}teams, makers, you{/rotator}</h1>

  $padFxSource = padEffectSource ();
  $padFxHold   = padEffectNumber ( 'hold', 2.5, 0.5, 30 );
  $padFxDelay  = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxRepeat = padEffectRepeat ( 3 );

  // The text as the characters it holds - a comma of a parameter or of a field in the content
  // travels as its stand-in, which divides the phrases as well - escaped again phrase by phrase.

  $padFxText = padUnprotect ( padEffectPlain ( $padFxSource ) );

  if ( $padFxText === '' )
    return padEffectNeedsText ( "{rotator}teams, makers, you{/rotator} or {rotator 'fast, simple'/}" );

  $padFxPhrases = array_values ( array_filter ( array_map ( 'trim', explode ( ',', $padFxText ) ), fn ( $phrase ) => $phrase !== '' ) );

  if ( count ( $padFxPhrases ) < 2 ) {
    padEffectBad ( "turns two phrases or more between commas, not '" . padEffectShow ( $padFxText ) . "' - {rotator}teams, makers, you{/rotator}" );
    return padEffectEscape ( $padFxText );
  }

  if ( count ( $padFxPhrases ) > 12 ) {
    padEffectBad ( 'turns twelve phrases at most, not ' . count ( $padFxPhrases ) );
    $padFxPhrases = array_slice ( $padFxPhrases, 0, 12 );
  }

  // The turns of a number of phrases as keyframes, once per page for each number: the track
  // of the phrases and the copy of the first moves up by one phrase - a part of its own height
  // - at the end of every step, in its last sixth.

  $padFxCount = count ( $padFxPhrases );
  $padFxName  = "pad-effect-rotator-$padFxCount";

  if ( padWidgetOnce ( "style:effect-$padFxName" ) ) {
    $padFxStep   = 100 / ( $padFxCount + 1 );
    $padFxFrames = '0%{transform:translateY(0)}';
    for ( $padFxAt = 1; $padFxAt <= $padFxCount; $padFxAt++ )
      $padFxFrames .= round ( ( $padFxAt - 1 / 6 ) / $padFxCount * 100, 3 ) . '%{transform:translateY(' . ( $padFxAt == 1 ? '0' : '-' . round ( ( $padFxAt - 1 ) * $padFxStep, 4 ) . '%' ) . ')}'
                    . round ( $padFxAt / $padFxCount * 100, 3 ) . '%{transform:translateY(-' . round ( $padFxAt * $padFxStep, 4 ) . '%)}';
    $GLOBALS ['padEffectRules'] [] = "@keyframes $padFxName{" . $padFxFrames . '}';
  }

  $padFxItems = [];

  foreach ( $padFxPhrases as $padFxPhrase )
    $padFxItems [] = padProtect ( '<span class="pe-item">' ) . padEffectEscape ( $padFxPhrase ) . padProtect ( '</span>' );

  return padEffectDone ( 'rotator',
    padProtect ( padEffectOpen ( 'rotator', 'span', [
        'name'     => $padFxName,
        'duration' => padEffectSeconds ( $padFxHold * $padFxCount ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'repeat'   => $padFxRepeat
      ] ) . '<span class="pe-track">' )
    . implode ( padProtect ( '<span class="pe-sep">, </span>' ), $padFxItems )
    . padProtect ( '<span class="pe-item pe-again" aria-hidden="true" hidden>' ) . padEffectEscape ( $padFxPhrases [0] )
    . padProtect ( '</span></span></span>' ) );

?>
