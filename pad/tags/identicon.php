<?php

  // {identicon $email} - a symmetric 5 by 5 pattern in a colour of its own, drawn from a
  // SHA-256 hash of the value by lib/identicon.php: the same value is always the same
  // picture, and nothing leaves the server. size= is the width and height in pixels
  // (default 64), title= the accessible name, else 'Identicon' - the value itself, often an
  // e-mail address, is not written into the page.
  //
  //   {identicon $email, size=64}

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( 'the identicon has no value - {identicon $email}' );

  return padIdenticon ( (string) $padParm, (int) padTagParm ( 'size', 64 ), (string) padTagParm ( 'title' ) );

?>
