<?php

  // {widget:toast 'Saved'}Your changes are kept.{/widget:toast} or {widget:toast 'Saved'/} - a notice the visitor
  // dismisses: a <dialog> shown from the start, its title, its text, and a close button the
  // browser makes close it (invoker commands), a small script (CSP nonce) where it does not -
  // no form of its own, so a toast stands inside a {input:form} too. tone= colours it - info,
  // success, warning or error; place= puts it in a corner of the window, top or bottom,
  // instead of in the flow; timeout= closes it after so many seconds, which the script
  // does, held while the pointer or the focus is on it; sound= plays a sound of the sounds
  // subsystem as it shows, sound='success'. subs/widgets/_lib/toast.php.
  //
  // The text of the pair is template, rendered first: like {pad:live}, the pair asks for the end
  // walk and frames what rendered.

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padToastTitle = is_string ( $padParm ) ? trim ( padParmText ( $padParm ) ) : '';
  $padToastText  = $padPair [$pad] ? trim ( $padContent ) : '';

  if ( $padToastTitle === '' and $padToastText === '' ) {

    if ( $padCheckSyntax )
      padError ( "the {widget:toast} needs a title or a text - {widget:toast 'Saved'/} or {widget:toast}...{/widget:toast}" );

    $padContent = '';

    return $padPair [$pad] ? TRUE : '';

  }

  $padToastHtml = padToast ( $padToastTitle, $padToastText,
                             padWidgetChoice ( 'toast', 'tone',  'info',   [ 'info', 'success', 'warning', 'error' ] ),
                             padWidgetChoice ( 'toast', 'place', 'inline', [ 'inline', 'top', 'bottom' ] ),
                             padToastTimeout (), padToastSound () );

  if ( ! $padPair [$pad] )
    return $padToastHtml;

  $padContent = $padToastHtml;

  return TRUE;

?>
