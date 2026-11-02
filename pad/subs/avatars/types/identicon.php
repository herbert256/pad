<?php

  // {avatar:identicon $email/} - a symmetric 5 by 5 pattern in a colour of its own, drawn from a
  // SHA-256 hash of the value by subs/avatars/_lib/identicon.php: the same value is always the same
  // picture, and nothing leaves the server. size= is the width and height in pixels
  // (default 64), title= the accessible name, label= its alias, else 'Identicon' - the value itself, often an
  // e-mail address, is not written into the page.
  //
  //   {avatar:identicon $email, size=64}

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( 'the identicon has no value - {avatar:identicon $email/}' );

  return padIdenticon ( padParmText ( $padParm ), (int) padWidgetNumber ( 'identicon', 'size', 64 ), padParmText ( padTagParm ( 'title', padTagParm ( 'label' ) ) ) );

?>
