<?php

  // Runs after the post was checked against the rules of forms_4.pad, and acts only when
  // it kept them; a post that broke one renders the form again, with its messages.

  if ( padPosted ( 'contact' ) ) {

    padFlash ( 'Thank you for your message.' );

    padRedirect ();

  }

?>