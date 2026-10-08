<?php

  // The rules= of form.pad checked the post before this file runs: padPosted ( 'signup' ) is
  // TRUE for a post that kept them alone. htmx posts the form (hx-post) and swaps the answer
  // into its place - hx-target="#signup" sends HX-Target: signup, so the answer is the
  // {fragment 'signup'}: the form again, refilled, with PAD's messages beside its fields, or
  // the welcome. The browser checks the same rules first (client); switch that off to see
  // PAD's own answer.

  $done = padPosted ( 'signup' );
  $name = (string) padRequest ( 'name', '' );

?>
