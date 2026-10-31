<?php

  // {toast 'Saved'}Your changes are kept.{/toast} or {toast 'Saved'/} - a notice the visitor
  // dismisses: a <dialog> shown from the start, its title, its text, and a close button in a
  // form of method dialog, which closes it without a script. tone= colours it - info,
  // success, warning or error; place= puts it in a corner of the window, top or bottom,
  // instead of in the flow; timeout= closes it after so many seconds, which a small script
  // does (CSP nonce), held while the pointer or the focus is on it. widgets/_lib/toast.php.
  //
  // The text of the pair is template, rendered first: like {live}, the pair asks for the end
  // walk and frames what rendered.

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padToastTitle = is_string ( $padParm ) ? trim ( padParmText ( $padParm ) ) : '';
  $padToastText  = $padPair [$pad] ? trim ( $padContent ) : '';

  if ( $padToastTitle === '' and $padToastText === '' ) {

    if ( $padCheckSyntax )
      padError ( "the {toast} needs a title or a text - {toast 'Saved'/} or {toast}...{/toast}" );

    $padContent = '';

    return $padPair [$pad] ? TRUE : '';

  }

  $padToastHtml = padToast ( $padToastTitle, $padToastText,
                             padWidgetChoice ( 'toast', 'tone',  'info',   [ 'info', 'success', 'warning', 'error' ] ),
                             padWidgetChoice ( 'toast', 'place', 'inline', [ 'inline', 'top', 'bottom' ] ),
                             padToastTimeout () );

  if ( ! $padPair [$pad] )
    return $padToastHtml;

  $padContent = $padToastHtml;

  return TRUE;

?>
