<?php

  // Image filters - the filter subsystem: what {img} does to a picture with filter=, a comma
  // list of filters applied in the order written. A filter is a file of subs/filters/types/,
  // and the files there are the filters there are (padKinds ( 'filters' )); there is no tag of
  // its own: a filter is a word of the img tag's filter= option, as a validation rule is a word
  // of padValidate.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='...'/}
  //   {img 'photos/harbour.jpg', width=400, filter='grayscale, dither', alt='...'/}
  //   {img 'photos/harbour.jpg', width=400, filter='blurhash', alt='...'/}
  //
  // A filter answers what it is and the options it reads:
  //
  //   return [ 'form'     => 'image',              image, markup or placeholder
  //            'options'  => [ 'levels' => [ 'number', 4, 2, 32 ] ],
  //            'pixels'   => TRUE,                 its look is the picture's pixels
  //            'lossless' => TRUE,                 PNG unless format= says otherwise
  //            'loops'    => TRUE,                 it reads every pixel in PHP
  //            'count'    => [ 'dots', 50000, function ( $o, $w, $h ) { ... }, 'a larger cell=' ],
  //            'apply'    => function ( $image, $o, $scale ) { ... return $image; } ];
  //
  // image - a GD picture in, a GD picture out: grayscale, sepia, duotone, posterize, pixelate,
  //   blur, sharpen, vignette, invert and the three dithers. They work on the thumbnail after
  //   it was resized - the 1x and the 2x picture alike, $scale the 2x one's density, so a blur
  //   of 6 pixels is 6 CSS pixels on both - and the thumbnail's file name holds them and their
  //   options. A filter whose look is the picture's own pixels - pixelate and the dithers - is
  //   made at 1x alone and shown with image-rendering: pixelated, so a sharp screen doubles its
  //   pixels instead of smoothing them; a filter of flat colours - those and posterize - is
  //   written as a PNG unless format= says otherwise, where JPEG would smear its edges.
  // markup - the picture written another way, in place of the <img>: halftone as SVG dots,
  //   ascii as text in a <pre>. 'make' turns the filtered 1x picture into what is kept in
  //   _thumbs/ beside the thumbnails (an .svg, a .txt), 'show' writes that with the alt text
  //   as the element's name - made once, shown on every request.
  // placeholder - what the page shows while the lazy <img> loads: blurhash, dominant, lqip.
  //   'hold' answers the img's style and attributes from the filtered 1x picture, kept in
  //   _thumbs/ as a .json.
  //
  // The budget, in the spirit of the 64 megapixels {img} resizes at most: what GD does in C
  // costs little, a loop of PHP over the pixels a microsecond a pixel and more - a dither of a
  // camera's 12 megapixels took 15 seconds and 209 MB, past max_execution_time and a stock
  // memory_limit at the 64, so the file was never written and every view began it again. A
  // filter that loops ('loops': sepia, duotone, posterize, vignette, the dithers, dominant)
  // works on 2 megapixels at most - a 1x picture larger is an error under the strict check
  // and the filter left out without it, a 2x picture is made only at the density those 2
  // megapixels reach - and a filter that writes a count of things ('count': halftone's dots,
  // printed in the page on every view) as many as it says at most, the same way.
  //
  // padFilterChain    the filters of filter=, each with its options read from the tag
  // padFilterBudget   the chain without the filters past their budget at a size, said why
  // padFilterKind     a filter's answer, its file read once a request
  // padFilterOption   one option of a filter, checked - its default after saying why
  // padFilterKey      what a list of filters adds to the hash of a file it makes
  // padFilterMap      every pixel of a picture through a function of its colour
  // padFilterLuma     the luminance of a colour, 0 to 255 (Rec. 601, as GD's grayscale)
  // padFilterRgb      a hex colour as [ r, g, b ]
  // padFilterMix      the colour a part of the way from one colour to another
  // padFilterResize   a picture at a new size, its transparency kept
  // padFilterFlat     a picture laid on a colour, without transparency
  // padFilterOpacity  the opacity of every pixel as a grey picture - NULL when all is opaque
  // padFilterStyle    the rules of the filtered pictures, once a page
  //
  // GD's output is the same bytes for the same picture and options on a machine (its PNG,
  // JPEG and WebP writers are deterministic), and every filter here is arithmetic on the
  // pixels or a GD filter of fixed numbers - no random number, no time - so a filtered
  // thumbnail has one name and one content, and a stored answer holds its md5.

  function padFilterKind ( $name ) {

    static $kinds = [];

    if ( ! array_key_exists ( $name, $kinds ) )
      $kinds [$name] = padKind ( 'filters', $name ) ? include PAD . "subs/filters/types/$name.php" : NULL;

    return $kinds [$name];

  }

  // The chain: [ name, answer, options ] per filter, in the order written. A name that is no
  // filter, a filter after halftone or ascii (there is no picture left to work on), a
  // placeholder beside them (they write no <img>) and two background pictures are errors
  // under the strict check, and are left out.

  function padFilterChain ( $text ) {

    global $padCheckSyntax, $padInfoXref;

    $chain  = [];
    $markup = FALSE;
    $back   = '';

    foreach ( explode ( ',', is_scalar ( $text ) ? (string) $text : '' ) as $name ) {

      $name = strtolower ( trim ( $name ) );

      if ( $name === '' )
        continue;

      $spec = padFilterKind ( $name );

      if ( ! $spec ) {
        if ( $padCheckSyntax )
          padError ( "the img tag has no filter '" . padMakeSafe ( $name, 20 ) . "' - " . implode ( ', ', padKinds ( 'filters' ) ) );
        continue;
      }

      if ( $markup !== FALSE ) {
        if ( $padCheckSyntax )
          padError ( "the img tag's filter $name comes after $markup, which writes the picture as "
                   . ( $markup == 'ascii' ? 'text' : 'dots' ) . " - there is no picture left to $name" );
        continue;
      }

      if ( $spec ['form'] == 'markup' )
        foreach ( $chain as list ( $other, $otherSpec ) )
          if ( $otherSpec ['form'] == 'placeholder' ) {
            if ( $padCheckSyntax )
              padError ( "the img tag's placeholder $other has no <img> to stand behind - $name writes none" );
            continue 2;
          }

      if ( $spec ['form'] == 'placeholder' and ( $spec ['background'] ?? FALSE ) ) {
        if ( $back !== '' ) {
          if ( $padCheckSyntax )
            padError ( "the img tag's filters $back and $name are both a picture behind the <img> - one of them" );
          continue;
        }
        $back = $name;
      }

      if ( $spec ['form'] == 'markup' )
        $markup = $name;

      if ( $padInfoXref ?? FALSE )
        padInfoXref ( 'filter', $name );

      $options = [];

      foreach ( $spec ['options'] ?? [] as $option => $rule )
        $options [$option] = padFilterOption ( $name, $option, $rule, padTagParm ( $option, NULL ) );

      $chain [] = [ $name, $spec, $options ];

    }

    return $chain;

  }

  const padFilterPixels = 2000000;

  // The chain without the filters past their budget at the 1x size $w by $h - reported under
  // the strict check, left out without it.

  function padFilterBudget ( $chain, $w, $h ) {

    global $padCheckSyntax;

    $kept = [];

    foreach ( $chain as $one ) {

      list ( $name, $spec, $options ) = $one;

      $over = '';

      if ( ( $spec ['loops'] ?? FALSE ) and $w * $h > padFilterPixels )
        $over = "works pixel by pixel, and the picture is $w x $h pixels - " . padFilterNum ( $w * $h / 1e6 )
              . ' megapixels, ' . ( padFilterPixels / 1e6 ) . ' at most: a smaller width= or height=';

      elseif ( isset ( $spec ['count'] ) ) {
        list ( $what, $most, $count, $fix ) = $spec ['count'];
        $n = $count ( $options, $w, $h );
        if ( $n > $most )
          $over = 'would draw ' . number_format ( $n ) . " $what on a picture of $w x $h pixels - " . number_format ( $most ) . " at most: $fix";
      }

      if ( $over === '' )
        $kept [] = $one;
      elseif ( $padCheckSyntax )
        padError ( "the img tag's filter $name $over" );

    }

    return $kept;

  }

  // An option by its rule: [ 'number', default, least, most ] - [ ..., 'whole' ] for a whole
  // number - [ 'colors', default list ], [ 'text', default, longest ] or [ 'pair', default
  // pair, least, most ] - two whole numbers, '4,3'. NULL, not given: the default.

  function padFilterOption ( $filter, $option, $rule, $value ) {

    global $padCheckSyntax;

    $kind    = $rule [0];
    $default = $rule [1];

    if ( $value === NULL )
      return $default;

    $shown = padMakeSafe ( is_scalar ( $value ) ? (string) $value : 'array', 30 );
    $fail  = function ( $want ) use ( $filter, $option, $shown, $padCheckSyntax, $default ) {
      if ( $padCheckSyntax )
        padError ( "the img tag's $option '$shown' is no $option for the $filter filter - $want" );
      return $default;
    };

    switch ( $kind ) {

      // A count - levels, columns, the cell of pixelate - is a whole number: a fraction was
      // cut to the whole one below it by the filter, and ascii's text sized for the fraction.

      case 'number':
        $whole = ( ( $rule [4] ?? '' ) == 'whole' );
        if ( is_string ( $value ) )
          $value = preg_replace ( '/\s*px\s*$/i', '', trim ( $value ) );
        if ( ! is_numeric ( $value ) or $value < $rule [2] or $value > $rule [3] or ( $whole and floor ( $value + 0 ) != $value + 0 ) )
          return $fail ( ( $whole ? 'a whole number' : 'a number' ) . " from {$rule [2]} to {$rule [3]}" );
        return $whole ? (int) $value : $value + 0;

      case 'colors':
        $list = is_array ( $value ) ? $value : explode ( ',', (string) $value );
        $out  = [];
        foreach ( $list as $one )
          if ( ! is_string ( $one ) or ! preg_match ( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/iD', trim ( $one ) ) )
            return $fail ( 'hex colours, the dark one first - \'#123, #fc6\' or \'#112233, #ffcc66\'' );
          else
            $out [] = strtolower ( trim ( $one ) );
        if ( count ( $out ) != count ( $default ) )
          return $fail ( count ( $default ) . ' hex colours, the dark one first - \'#123, #fc6\'' );
        return $out;

      // A list given to a text or a pair is no text: the message, not PHP's "Array to string
      // conversion" of the cast.

      case 'text':
        $value = is_scalar ( $value ) ? padParmText ( (string) $value ) : '';
        if ( $value === '' or mb_strlen ( $value ) < 2 or mb_strlen ( $value ) > $rule [2] )
          return $fail ( "from 2 to {$rule [2]} characters, the lightest first" );
        return $value;

      case 'pair':
        if ( ! is_scalar ( $value ) or ! preg_match ( '/^\s*(\d+)\s*,\s*(\d+)\s*$/', (string) $value, $match )
             or $match [1] < $rule [2] or $match [1] > $rule [3] or $match [2] < $rule [2] or $match [2] > $rule [3] )
          return $fail ( "two whole numbers from {$rule [2]} to {$rule [3]}, across and down - '4,3'" );
        return [ (int) $match [1], (int) $match [2] ];

    }

    return $default;

  }

  function padFilterKey ( $chain ) {

    $key = [];

    foreach ( $chain as list ( $name, , $options ) )
      $key [] = [ $name, $options ];

    return $key;

  }

  // Every pixel through $fn ( r, g, b ) => 0xRRGGBB, the alpha left as it was. A function per
  // pixel is what a filter of its own arithmetic costs: some 60 ms for a picture of 800 by 534
  // here, made once and kept.

  function padFilterMap ( $image, $fn ) {

    $w = imagesx ( $image );
    $h = imagesy ( $image );

    imagealphablending ( $image, FALSE );

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {
        $c = imagecolorat ( $image, $x, $y );
        imagesetpixel ( $image, $x, $y, ( $c & 0x7F000000 ) | $fn ( ( $c >> 16 ) & 255, ( $c >> 8 ) & 255, $c & 255 ) );
      }

    return $image;

  }

  // The luminance of every pixel, a row a list - what the dithers and the markup read.

  function padFilterLumas ( $image ) {

    $w     = imagesx ( $image );
    $h     = imagesy ( $image );
    $lumas = [];

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {
        $c = imagecolorat ( $image, $x, $y );
        $lumas [$y] [$x] = padFilterLuma ( ( $c >> 16 ) & 255, ( $c >> 8 ) & 255, $c & 255 );
      }

    return $lumas;

  }

  function padFilterLuma ( $r, $g, $b ) {

    return intdiv ( $r * 299 + $g * 587 + $b * 114 + 500, 1000 );

  }

  function padFilterRgb ( $hex ) {

    $hex = ltrim ( $hex, '#' );

    if ( strlen ( $hex ) == 3 )
      $hex = $hex [0] . $hex [0] . $hex [1] . $hex [1] . $hex [2] . $hex [2];

    return [ hexdec ( substr ( $hex, 0, 2 ) ), hexdec ( substr ( $hex, 2, 2 ) ), hexdec ( substr ( $hex, 4, 2 ) ) ];

  }

  function padFilterMix ( $from, $to, $t ) {

    return ( (int) round ( $from [0] + ( $to [0] - $from [0] ) * $t ) << 16 )
         | ( (int) round ( $from [1] + ( $to [1] - $from [1] ) * $t ) << 8 )
         |   (int) round ( $from [2] + ( $to [2] - $from [2] ) * $t );

  }

  // A number as the markup writes it: one decimal at most, no -0.

  function padFilterNum ( $n ) {

    $text = rtrim ( rtrim ( number_format ( (float) $n, 1, '.', '' ), '0' ), '.' );

    return ( $text === '-0' ) ? '0' : $text;

  }

  // A picture resized with GD's resampling - the average of what it covers, each colour weighed
  // by its opacity - to a new size, its transparency kept: the new picture starts transparent
  // and takes the averages as they are. On a picture of opaque black, as before, a transparent
  // part became black and a pixel half on it half black.

  function padFilterResize ( $image, $w, $h ) {

    $out = imagecreatetruecolor ( max ( 1, $w ), max ( 1, $h ) );

    imagealphablending ( $out, FALSE );
    imagesavealpha ( $out, TRUE );
    imagefill ( $out, 0, 0, imagecolorallocatealpha ( $out, 0, 0, 0, 127 ) );

    imagecopyresampled ( $out, $image, 0, 0, 0, 0, max ( 1, $w ), max ( 1, $h ), imagesx ( $image ), imagesy ( $image ) );

    return $out;

  }

  // The picture laid on a colour, 0xRRGGBB: a copy without transparency - what lqip and blurhash
  // read, as their pictures have none; the same pixels for a picture that has none either.

  function padFilterFlat ( $image, $rgb ) {

    $w   = imagesx ( $image );
    $h   = imagesy ( $image );
    $out = imagecreatetruecolor ( $w, $h );

    imagefill ( $out, 0, 0, $rgb );
    imagecopy ( $out, $image, 0, 0, 0, 0, $w, $h );

    return $out;

  }

  // The opacity of every pixel as a grey picture without transparency - white where the
  // picture shows fully, black where it is transparent - or NULL when the picture shows
  // fully everywhere: its average opacity, GD's resampling to one pixel, is full.

  function padFilterOpacity ( $image ) {

    $w     = imagesx ( $image );
    $h     = imagesy ( $image );
    $white = imagecreatetruecolor ( $w, $h );

    imagealphablending ( $white, FALSE );
    imagecopy ( $white, $image, 0, 0, 0, 0, $w, $h );
    imagefilter ( $white, IMG_FILTER_BRIGHTNESS, 255 );

    $mask = imagecreatetruecolor ( $w, $h );
    imagecopy ( $mask, $white, 0, 0, 0, 0, $w, $h );

    $one = imagecreatetruecolor ( 1, 1 );
    imagecopyresampled ( $one, $mask, 0, 0, 0, 0, 1, 1, $w, $h );

    return ( ( imagecolorat ( $one, 0, 0 ) & 255 ) == 255 ) ? NULL : $mask;

  }

  // The rules the filtered pictures need, once a page: a pixel filter's picture drawn with its
  // pixels sharp, the halftone's SVG kept inside its column, the ascii's text set solid in a
  // monospaced face - its size on the element, made to the picture's width.

  function padFilterStyle () {

    if ( ! padWidgetOnce ( 'style:pad-img-filters' ) )
      return '';

    return '<style' . padStyleNonce () . '>'
         . '.pad-img-pixels{image-rendering:pixelated}'
         . '.pad-img-halftone{max-width:100%;height:auto;vertical-align:middle}'
         . '.pad-img-ascii{display:inline-block;margin:0;line-height:1;letter-spacing:0;white-space:pre;overflow:hidden;'
         . 'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}'
         . '</style>';

  }

?>
