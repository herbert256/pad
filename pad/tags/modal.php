<?php

  // {modal 'terms', title='Terms of sale', button='Read the terms'} ... {/modal} - a <dialog>
  // with the content, and a button that opens it: by the HTML invoker commands where the
  // browser has them, a small script (CSP nonce) where it has not, and a link with a
  // :target overlay where scripting is off. The name is the dialog's id - pad-modal-terms;
  // title= heads the dialog and names it, button= is the button's text ('Open' when not
  // given). lib/modal.php.
  //
  // Like {live} it runs twice: the content renders first, then the end walk puts it in the
  // dialog.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {modal} never closes" );

    if ( ! preg_match ( '/^[A-Za-z0-9_-]{1,64}$/D', (string) $padParm ) ) {
      if ( $padCheckSyntax )
        padError ( "a {modal} needs a name of letters, digits, _ and -, like {modal 'terms'}" );
      return FALSE;
    }

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padModalButton = (string) padTagParm ( 'button', 'Open' );
  $padModalTitle  = (string) padTagParm ( 'title', $padModalButton );

  $padContent = padModal ( (string) $padParm, $padModalTitle, $padModalButton, trim ( $padContent ) );

  return TRUE;

?>
