<?php

  // Image placeholders - the {placeholder} tag. A grey box of the size an image will have,
  // with its corners joined by two lines and the size or a text in the middle, as inline
  // SVG: a wireframe or a layout that waits for its pictures, without a placeholder service.
  //
  //   {placeholder '600x300', text='Hero image'}
  //   {placeholder ratio='16:9', fluid}
  //
  // padPlaceholder      the SVG of a width and height
  // padPlaceholderSize  '600x300' as [ 600, 300 ], or NULL when it is no size
  //
  // The view box is the size itself, so with fluid the box takes the width of its container
  // and keeps its proportions. The colours are --pad-placeholder-surface, -line and -text,
  // light-dark() on .pad-placeholder, written once per page; the attributes carry the light
  // ones for a reader without the style.

  function padPlaceholder ( $width, $height, $text = '', $fluid = FALSE ) {

    $width  = max ( 1, (int) $width );
    $height = max ( 1, (int) $height );
    $label  = ( trim ( (string) $text ) !== '' ) ? trim ( (string) $text ) : "$width × $height";
    $font   = min ( 40, $height / 3.2, $width / 9, $width * 0.8 / max ( 1, mb_strlen ( $label ) * 0.58 ) );
    $font   = max ( 6, round ( $font ) );
    $pad    = round ( $font * 0.7 );
    $boxW   = min ( $width - 2, round ( mb_strlen ( $label ) * $font * 0.58 + 2 * $pad ) );
    $boxH   = min ( $height - 2, round ( $font * 1.9 ) );
    $boxX   = round ( ( $width - $boxW ) / 2, 1 );
    $boxY   = round ( ( $height - $boxH ) / 2, 1 );
    $size   = $fluid ? 'width="100%"' : "width=\"$width\" height=\"$height\"";

    return padPlaceholderStyle ()
         . '<svg xmlns="http://www.w3.org/2000/svg" class="pad-placeholder" role="img" aria-label="' . padPlaceholderAttr ( $label ) . '"'
         . " $size viewBox=\"0 0 $width $height\" preserveAspectRatio=\"xMidYMid meet\">"
         . '<title>' . padPlaceholderAttr ( $label ) . '</title>'
         . "<rect class=\"ph-surface\" width=\"$width\" height=\"$height\" fill=\"#e4e3df\"/>"
         . "<path class=\"ph-line\" d=\"M0,0L$width,{$height}M$width,0L0,$height\" stroke=\"#c3c2bc\" stroke-width=\"1\" vector-effect=\"non-scaling-stroke\" fill=\"none\"/>"
         . "<rect class=\"ph-surface\" x=\"$boxX\" y=\"$boxY\" width=\"$boxW\" height=\"$boxH\" rx=\"" . round ( $boxH / 2 ) . '" fill="#e4e3df"/>'
         . '<text class="ph-text" x="' . ( $width / 2 ) . '" y="' . ( $height / 2 ) . '" dy=".35em" text-anchor="middle" fill="#6b6a65"'
         . " font-family=\"system-ui,-apple-system,'Segoe UI',sans-serif\" font-size=\"$font\" font-weight=\"500\">"
         . padPlaceholderAttr ( $label ) . '</text></svg>';

  }

  // A size is two whole numbers with an x between them; a ratio the same with a colon.

  function padPlaceholderSize ( $size, $separator = 'x' ) {

    if ( ! preg_match ( '/^\s*(\d{1,5})\s*' . preg_quote ( $separator, '/' ) . '\s*(\d{1,5})\s*$/i', (string) $size, $m ) )
      return NULL;

    if ( ! $m [1] or ! $m [2] )
      return NULL;

    return [ (int) $m [1], (int) $m [2] ];

  }

  function padPlaceholderStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;
    $roles   = [ 'surface' => [ '#e4e3df', '#2c2c2a' ],
                 'line'    => [ '#c3c2bc', '#454542' ],
                 'text'    => [ '#6b6a65', '#a3a29b' ] ];
    $light   = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-placeholder-$role:$day;";
      $both  .= "--pad-placeholder-$role:light-dark($day,$night);";
    }

    return '<style' . padPlaceholderNonce () . '>'
         . ":where(.pad-placeholder){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-placeholder){{$both}}}"
         . '.pad-placeholder{display:block;max-width:100%;height:auto}'
         . '.pad-placeholder .ph-surface{fill:var(--pad-placeholder-surface)}'
         . '.pad-placeholder .ph-line{stroke:var(--pad-placeholder-line)}'
         . '.pad-placeholder .ph-text{fill:var(--pad-placeholder-text)}'
         . '</style>';

  }

  function padPlaceholderNonce () {

    global $padCsp;

    return ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) ) ? ' nonce="' . padNonce () . '"' : '';

  }

  function padPlaceholderAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
