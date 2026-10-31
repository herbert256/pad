<?php

  // {tooltip 'Billed every month'}Starter plan{/tooltip} or {tooltip 'What this means'/} - a
  // short tip shown while the pointer is on its trigger or the trigger has the focus: CSS
  // alone shows it, the trigger names it for a screen reader (aria-describedby). The pair's
  // content is the trigger; the single tag gets a small information button. place= puts the
  // tip above (top, the default) or below its trigger. A small script (CSP nonce) hides a
  // shown tip on Escape. widgets/_lib/tooltip.php.
  //
  // The pair's content is template, rendered first: like {live}, the pair asks for the end
  // walk and frames what rendered.

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padTipText    = is_string ( $padParm ) ? trim ( padParmText ( $padParm ) ) : '';
  $padTipTrigger = $padPair [$pad] ? trim ( $padContent ) : '';

  if ( $padTipText === '' or ( $padPair [$pad] and $padTipTrigger === '' ) ) {

    if ( $padCheckSyntax )
      padError ( $padTipText === ''
        ? "the {tooltip} needs its tip - {tooltip 'Billed every month'}Starter{/tooltip}"
        : "the {tooltip} pair needs what it explains between its tags - or is written single, {tooltip 'tip'/}" );

    $padContent = '';

    return $padPair [$pad] ? TRUE : '';

  }

  $padTipHtml = padTooltip ( $padTipText, $padTipTrigger, padWidgetChoice ( 'tooltip', 'place', 'top', [ 'top', 'bottom' ] ) );

  if ( ! $padPair [$pad] )
    return $padTipHtml;

  $padContent = $padTipHtml;

  return TRUE;

?>
