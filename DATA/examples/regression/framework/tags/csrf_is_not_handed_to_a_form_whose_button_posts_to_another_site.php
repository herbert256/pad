<?php

  // A submit button's formaction= sends the form where it says, so a form holding one that
  // points to another site gets no token, a {form} as much as a form of the page's own
  // markup: the token went along to elsewhere.example with every click on that button. A
  // formaction on this site leaves the token in place.

  $padCsrf = TRUE;

?>
