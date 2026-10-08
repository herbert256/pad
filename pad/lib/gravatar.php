<?php

  // Gravatars - the {gravatar} tag. The picture a person registered for an e-mail address at
  // gravatar.com, as an <img>: the address is never written into the page, only its SHA-256
  // hash, the way Gravatar asks for it - trimmed and lower-cased first.
  //
  //   {gravatar $email, size=64}
  //   {gravatar $email, fallback='avatar', alt=$name}
  //
  // padGravatarUrl  the address of the picture: https://www.gravatar.com/avatar/<hash>?s=&d=
  // padGravatar     the <img>, with a twice as large one in srcset for a sharp screen,
  //                 loading="lazy", decoding="async" and referrerpolicy="no-referrer" - the
  //                 page the visitor is on is not told to Gravatar either
  //
  // Showing the picture asks a third party for it, and with it the visitor's IP address.
  // fallback='avatar' (or 'identicon') does not: it answers the {avatar} (or {identicon})
  // SVG of lib/avatar.php instead, drawn on the server - for a page under a privacy policy
  // that allows no outside requests, or a test without network.

  const PAD_GRAVATAR_DEFAULTS = [ 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank', '404' ];

  function padGravatarUrl ( $email, $size = 64, $default = 'mp' ) {

    $hash = hash ( 'sha256', strtolower ( trim ( (string) $email ) ) );

    return "https://www.gravatar.com/avatar/$hash?s=" . (int) $size . '&d=' . rawurlencode ( (string) $default );

  }

  function padGravatar ( $email, $size = 64, $default = 'mp', $alt = '', $fallback = '' ) {

    $size = max ( 8, min ( 2048, (int) $size ) );

    // The local stand-ins: the initials of the alt text - the person's name - or of the
    // address before its @, and the identicon of the address.

    if ( $fallback == 'avatar' )
      return padAvatar ( ( trim ( (string) $alt ) !== '' ) ? $alt : $email, $size, 'circle',
                         ( trim ( (string) $alt ) !== '' ) ? $alt : 'Avatar' );

    if ( $fallback == 'identicon' )
      return padIdenticon ( $email, $size, ( trim ( (string) $alt ) !== '' ) ? $alt : 'Avatar' );

    $alt = ( trim ( (string) $alt ) !== '' ) ? trim ( (string) $alt ) : 'Avatar';

    return '<img class="pad-gravatar" src="' . padGravatarAttr ( padGravatarUrl ( $email, $size, $default ) ) . '"'
         . ' srcset="' . padGravatarAttr ( padGravatarUrl ( $email, min ( 2048, $size * 2 ), $default ) ) . ' 2x"'
         . " width=\"$size\" height=\"$size\" alt=\"" . padGravatarAttr ( $alt ) . '"'
         . ' loading="lazy" decoding="async" referrerpolicy="no-referrer">';

  }

  function padGravatarAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
