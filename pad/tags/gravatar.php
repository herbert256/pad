<?php

  // {gravatar $email} - the Gravatar picture of an e-mail address as an <img>, by
  // lib/gravatar.php: the address goes out only as its SHA-256 hash, lazily loaded and with
  // no referrer. size= is the width and height in pixels (default 64), default= what
  // Gravatar shows for an address it does not know - mp (default), identicon, monsterid,
  // wavatar, retro, robohash, blank, 404 or an https:// image address - and alt= the text
  // for the image, the person's name, else 'Avatar'.
  //
  // fallback='avatar' asks no one: it draws the {avatar} initials of alt= (or of the address)
  // on the server instead, and fallback='identicon' the {identicon} of the address - for a
  // page that may make no request to a third party.
  //
  //   {gravatar $email, size=64, default='identicon'}
  //   {gravatar $email, fallback='avatar', alt=$name}

  $padGravatarEmail    = trim ( (string) $padParm );
  $padGravatarDefault  = trim ( (string) padTagParm ( 'default', 'mp' ) );
  $padGravatarFallback = strtolower ( trim ( (string) padTagParm ( 'fallback' ) ) );

  if ( $padGravatarEmail === '' and $padCheckSyntax )
    padError ( 'the gravatar has no e-mail address - {gravatar $email}' );

  if ( ! in_array ( $padGravatarDefault, PAD_GRAVATAR_DEFAULTS ) and ! preg_match ( '#^https://[^\s"<>]+$#i', $padGravatarDefault ) ) {
    if ( $padCheckSyntax )
      padError ( "the gravatar has no default '" . padMakeSafe ( $padGravatarDefault, 30 ) . "' - " . implode ( ', ', PAD_GRAVATAR_DEFAULTS ) . ' or an https:// address' );
    $padGravatarDefault = 'mp';
  }

  if ( ! in_array ( $padGravatarFallback, [ '', 'avatar', 'identicon' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the gravatar has no fallback '" . padMakeSafe ( $padGravatarFallback, 20 ) . "' - avatar or identicon" );
    $padGravatarFallback = '';
  }

  return padGravatar ( $padGravatarEmail, (int) padTagParm ( 'size', 64 ), $padGravatarDefault,
                       (string) padTagParm ( 'alt' ), $padGravatarFallback );

?>
