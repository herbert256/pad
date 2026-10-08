<?php

  // Initials avatars - the {avatar} tag. A name becomes a circle (or a rounded square) with
  // its initials, as inline SVG: no image, no remote service, the same picture for the same
  // name on every page and in every e-mail.
  //
  //   {avatar 'Herbert Jebbink'}
  //   {avatar $name, size=32, shape='square'}
  //
  // padAvatar          the SVG of a name
  // padAvatarInitials  the one or two letters drawn: the first letters of the first and the
  //                    last word, or of the parts of an e-mail address before its @
  // padAvatarSlot      the palette colour of a name, 1-10, from a hash of the name
  // padAvatarInk       the text colour that reads best on a background: white or near-black,
  //                    whichever has the higher WCAG contrast
  //
  // The background comes from a palette of ten colours, each with its own variant for a dark
  // colour scheme; the text on it is chosen per colour by contrast, so every initial reads.
  // The colours are the custom properties --pad-avatar-1 ... --pad-avatar-10 and their
  // --pad-avatar-ink-<n>, written once per page in a light-dark() rule on .pad-avatar; the
  // fill attributes carry the light colours for a reader without the style - an e-mail.

  const PAD_AVATAR_PALETTE = [
    1  => [ '#2a6fd0', '#1f5aae' ],
    2  => [ '#c9501f', '#a8431a' ],
    3  => [ '#12805a', '#0f6b4b' ],
    4  => [ '#eda100', '#c98500' ],
    5  => [ '#c64a7c', '#a63c67' ],
    6  => [ '#0d7d87', '#0b6870' ],
    7  => [ '#5b4bc4', '#4a3aa7' ],
    8  => [ '#c93534', '#a82b2a' ],
    9  => [ '#5d6a80', '#4b5668' ],
    10 => [ '#6b7a1a', '#576415' ] ];

  function padAvatar ( $name, $size = 48, $shape = 'circle', $label = '' ) {

    $name     = trim ( (string) $name );
    $label    = ( trim ( (string) $label ) !== '' ) ? trim ( (string) $label ) : $name;
    $initials = padAvatarInitials ( $name );
    $slot     = padAvatarSlot ( $name );
    $size     = max ( 8, (int) $size );
    $light    = PAD_AVATAR_PALETTE [$slot] [0];

    // The shape on a 100 by 100 view: a circle, or a square with rounded corners. One
    // letter is drawn a little larger than two.

    $back = ( $shape == 'square' )
          ? "<rect class=\"pa-bg$slot\" width=\"100\" height=\"100\" rx=\"18\" fill=\"$light\"/>"
          : "<circle class=\"pa-bg$slot\" cx=\"50\" cy=\"50\" r=\"50\" fill=\"$light\"/>";

    $font = ( mb_strlen ( $initials ) > 1 ) ? 40 : 46;

    return padAvatarStyle ()
         . '<svg xmlns="http://www.w3.org/2000/svg" class="pad-avatar" role="img" aria-label="' . padAvatarAttr ( $label ) . '"'
         . " width=\"$size\" height=\"$size\" viewBox=\"0 0 100 100\">"
         . '<title>' . padAvatarAttr ( $label ) . '</title>'
         . $back
         . "<text class=\"pa-ink$slot\" x=\"50\" y=\"50\" dy=\".35em\" text-anchor=\"middle\" fill=\"" . padAvatarInk ( $light ) . '"'
         . " font-family=\"system-ui,-apple-system,'Segoe UI',sans-serif\" font-size=\"$font\" font-weight=\"600\">"
         . padAvatarAttr ( $initials ) . '</text></svg>';

  }

  // An e-mail address counts by the part before its @, its dots, dashes and underscores
  // separating words: herbert.jebbink@example.com is HJ, as Herbert Jebbink is.

  function padAvatarInitials ( $name ) {

    $name = trim ( (string) $name );

    if ( str_contains ( $name, '@' ) )
      $name = strstr ( $name, '@', TRUE );

    $words = preg_split ( '/[\s._\-]+/u', $name, -1, PREG_SPLIT_NO_EMPTY );
    $words = array_values ( array_filter ( $words, fn ( $word ) => preg_match ( '/^[\p{L}\p{N}]/u', $word ) ) );

    if ( ! $words )
      return '?';

    $first = mb_strtoupper ( mb_substr ( $words [0], 0, 1 ) );

    if ( count ( $words ) == 1 )
      return $first;

    return $first . mb_strtoupper ( mb_substr ( end ( $words ), 0, 1 ) );

  }

  function padAvatarSlot ( $name ) {

    $hash = hash ( 'sha256', mb_strtolower ( trim ( (string) $name ) ) );

    return hexdec ( substr ( $hash, 0, 8 ) ) % count ( PAD_AVATAR_PALETTE ) + 1;

  }

  // WCAG relative luminance and contrast: white or #1a1a19 on the background, the one that
  // stands out more.

  function padAvatarInk ( $background ) {

    $dark = '#1a1a19';

    return ( padAvatarContrast ( $background, '#ffffff' ) >= padAvatarContrast ( $background, $dark ) ) ? '#ffffff' : $dark;

  }

  function padAvatarContrast ( $one, $two ) {

    $a = padAvatarLuminance ( $one );
    $b = padAvatarLuminance ( $two );

    return ( max ( $a, $b ) + 0.05 ) / ( min ( $a, $b ) + 0.05 );

  }

  function padAvatarLuminance ( $hex ) {

    $hex = ltrim ( $hex, '#' );
    $sum = 0;

    foreach ( [ 0 => 0.2126, 2 => 0.7152, 4 => 0.0722 ] as $at => $weight ) {
      $c    = hexdec ( substr ( $hex, $at, 2 ) ) / 255;
      $sum += $weight * ( ( $c <= 0.03928 ) ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4 );
    }

    return $sum;

  }

  // The colours, once per page: the light colours for a browser without light-dark(), the
  // pair where it is known, and a rule per slot that takes them.

  function padAvatarStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;
    $light   = $both = $rules = '';

    foreach ( PAD_AVATAR_PALETTE as $slot => list ( $day, $night ) ) {
      $inkDay   = padAvatarInk ( $day );
      $inkNight = padAvatarInk ( $night );
      $light   .= "--pad-avatar-$slot:$day;--pad-avatar-ink-$slot:$inkDay;";
      $both    .= "--pad-avatar-$slot:light-dark($day,$night);--pad-avatar-ink-$slot:light-dark($inkDay,$inkNight);";
      $rules   .= ".pad-avatar .pa-bg$slot{fill:var(--pad-avatar-$slot)}.pad-avatar .pa-ink$slot{fill:var(--pad-avatar-ink-$slot)}";
    }

    return '<style' . padAvatarNonce () . '>'
         . ":where(.pad-avatar){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-avatar){{$both}}}"
         . '.pad-avatar{vertical-align:middle;flex:none}'
         . $rules
         . '</style>';

  }

  // A style block written by the engine carries the request's nonce when the policy asks
  // for one - $padCsp with 'nonce' in it - so a strict style-src lets it through.

  function padAvatarNonce () {

    global $padCsp;

    return ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) ) ? ' nonce="' . padNonce () . '"' : '';

  }

  function padAvatarAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
