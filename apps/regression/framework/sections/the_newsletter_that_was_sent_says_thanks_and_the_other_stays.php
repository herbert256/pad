<?php

  // Two newsletter sections on a page are one form, by its name, and the second carries its
  // place, as a second {pad:form 'newsletter'} does: a post of the second says thanks in the
  // second alone, the first stays an empty form. Both said thanks after one post, and both
  // refilled and showed its messages.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'newsletter', 'padFormAt' => "$padPage:2", 'email' => 'ann@example.com' ];

  padValidate ( [ 'email' => 'required|email' ] );

?>
