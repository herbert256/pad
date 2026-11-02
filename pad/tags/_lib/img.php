<?php

  // Thumbnails - the {pad:img} tag. A picture of www/<application>/ is resized with GD to the
  // size the page shows it at, once, and written beside the entry point as a file the web
  // server sends on its own: www/<application>/_thumbs/<hash>.<ext>. The page gets an
  // <img> with its width and height (no layout shift), loading="lazy", decoding="async"
  // and a srcset whose second picture is made at twice the size for a sharp screen.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, height=300, alt='The harbour at dawn'}
  //   {pad:img 'photos/dunes.jpg', width=300, fit='contain', format='webp', quality=70, alt=''}
  //
  // padImg         the <img> element of a picture, '' when it cannot be made
  // padImgSource   the picture's path, checked: inside www/<application>/, a JPEG, PNG,
  //                GIF or WebP - else NULL after saying why
  // padImgSizes    the part of the picture taken and the size it is drawn at
  // padImgThumb    the address of one thumbnail, made when it is not there yet
  // padImgOrient   a JPEG turned the way its camera's EXIF orientation says
  //
  // Why files under www and not an engine address (?padImage=...): a thumbnail is made once
  // and then asked for on every view of every page that shows it, and a file the web server
  // sends itself costs no PHP, gets the server's own caching and range headers, and goes
  // along with pad export like any asset. The directory is the engine's alone - _thumbs/,
  // which .gitignore leaves out - and a name in it is sixteen hex digits of a hash and an
  // extension the engine picked, so no name a template wrote ever becomes a path there.
  //
  // The hash is of the picture's contents and the options, not of its file time - the
  // reason lib/asset.php gives: a deploy that copies the files anew would otherwise make
  // every thumbnail again, and the same page answered the same address on every machine.
  // A changed picture is a new address, so a browser never shows an old thumbnail.
  //
  // fit='cover' (the default when both width and height are given) fills the box and cuts
  // what sticks out, around the centre; fit='contain' shows the whole picture inside the
  // box. A picture is never made larger than it is: a box bigger than the source is shrunk
  // to it, and the 2x picture is made only as far as the source reaches - the srcset then
  // says the density it really has, 1.5x for one. The format is the source's (a GIF becomes
  // a PNG), or format= jpeg, png or webp; quality= 1 to 100 (80) for JPEG and WebP.
  //
  // filter= - a comma list of the filters of the filter subsystem (subs/filters/), applied in
  // the order written: those that change the picture work on each thumbnail after it was
  // resized and are part of its name; halftone and ascii write the picture as SVG dots or as
  // text in place of the <img>; the placeholders - blurhash, thumbhash, dominant, lqip - stand behind
  // the <img> while it loads. What a filter makes besides a thumbnail - the dots, the text,
  // the placeholder - is kept in _thumbs/ too, under a name of its own from the same hash. A
  // picture without filter= is written as it always was, to the byte.
  //
  // The source, decoded once for the thumbnails, the dots and the placeholders of the picture,
  // is let go when the picture is done (padImgDraw ( NULL )): a page of two pictures of 12
  // megapixels needs the memory of one, some 50 MB of GD's, as before the filters - the
  // decoded picture was kept for the rest of the request, a queue worker's life.

  function padImg ( $file, $opts ) {

    $html = padImgMake ( $file, $opts );

    padImgDraw ( NULL );

    return $html;

  }

  function padImgMake ( $file, $opts ) {

    global $padCheckSyntax;

    $source = padImgSource ( $file );

    if ( $source === NULL )
      return '';

    list ( $path, $name, $type, $sw, $sh, $turn ) = $source;

    // A list given to an option of one word is named as 'array' by the option's own message -
    // cast to a text it was PHP's "Array to string conversion", also in the lenient walk.

    $fit = strtolower ( padImgWord ( $opts ['fit'] ?? 'cover' ) );

    if ( ! in_array ( $fit, [ 'cover', 'contain' ], TRUE ) ) {
      if ( $padCheckSyntax )
        padError ( "the img tag has no fit '" . padMakeSafe ( $fit, 10 ) . "' - cover or contain" );
      $fit = 'cover';
    }

    $width  = max ( 0, (int) ( $opts ['width']  ?? 0 ) );
    $height = max ( 0, (int) ( $opts ['height'] ?? 0 ) );

    list ( $crop, $w1, $h1, $max ) = padImgSizes ( $sw, $sh, $width, $height, $fit );

    // The filters, without those past their budget at this size (padFilterBudget).

    $chain  = [];
    $filter = $opts ['filter'] ?? '';

    if ( ! is_scalar ( $filter ) ) {
      if ( $padCheckSyntax )
        padError ( "the img tag's filter= is a list - the filters are one text, apart by commas: filter='grayscale, dither'" );
      $filter = '';
    }

    if ( trim ( (string) $filter ) !== '' ) {
      include_once PAD . 'subs/filters/_lib/filter.php';
      $chain = padFilterBudget ( padFilterChain ( $filter ), $w1, $h1 );
    }

    $images = array_values ( array_filter ( $chain, fn ( $one ) => $one [1] ['form'] == 'image' ) );
    $markup = array_values ( array_filter ( $chain, fn ( $one ) => $one [1] ['form'] == 'markup' ) ) [0] ?? NULL;
    $holds  = array_values ( array_filter ( $chain, fn ( $one ) => $one [1] ['form'] == 'placeholder' ) );
    $pixels = (bool) array_filter ( $images, fn ( $one ) => $one [1] ['pixels'] ?? FALSE );
    $loops  = (bool) array_filter ( $images, fn ( $one ) => $one [1] ['loops'] ?? FALSE );

    // A filter of flat colours is a PNG unless format= says otherwise: JPEG smeared the edges
    // of a dither or a posterized area.

    $format = strtolower ( padImgWord ( $opts ['format'] ?? '' ) );

    if ( $format === '' and array_filter ( $images, fn ( $one ) => $one [1] ['lossless'] ?? FALSE ) )
      $format = 'png';

    $format = ( $format === '' ) ? [ 'gif' => 'png' ] [$type] ?? $type : ( $format == 'jpg' ? 'jpeg' : $format );

    if ( ! in_array ( $format, [ 'jpeg', 'png', 'webp' ], TRUE ) ) {
      if ( $padCheckSyntax )
        padError ( "the img tag has no format '" . padMakeSafe ( $format, 10 ) . "' - jpeg, png or webp" );
      $format = 'jpeg';
    }

    $quality = (int) ( $opts ['quality'] ?? 80 );

    if ( $quality < 1 or $quality > 100 ) {
      if ( $padCheckSyntax )
        padError ( "the img tag's quality is $quality - it goes from 1 to 100" );
      $quality = 80;
    }

    // The filters that change the picture are part of every thumbnail's name - only when
    // there are any, so a picture without them keeps the name it had.

    $hash = padImgHash ( $path );
    $base = [ $hash, $crop, $format, $quality, $turn ];

    if ( $images )
      $base [] = padFilterKey ( $images );

    if ( $markup )
      return padFilterStyle () . padImgMarkup ( $path, $type, $turn, $crop, $w1, $h1, $images, $markup, $base, $opts ['alt'] );

    $one = padImgThumb ( $path, $type, $turn, $crop, $w1, $h1, $format, $quality, $base, $images, 1 );

    if ( $one === NULL )
      return padImgTag ( padAsset ( $name ), '', $w1, $h1, $opts ['alt'] );

    // The 2x picture, as far as the source reaches: a density of 1.2 or more is worth a
    // second file, less is the same picture again. A picture of pixel filters has none: its
    // 1x pixels are drawn sharp, doubled (image-rendering: pixelated). A filter that loops
    // over the pixels in PHP has its budget for the 2x picture too: the density reaches as
    // far as it does.

    $density = min ( 2, $max );
    $set     = '';

    if ( $loops )
      $density = min ( $density, sqrt ( padFilterPixels / ( $w1 * $h1 ) ) );

    if ( $density >= 1.2 and ! $pixels ) {
      $two = padImgThumb ( $path, $type, $turn, $crop, (int) round ( $w1 * $density ), (int) round ( $h1 * $density ), $format, $quality, $base, $images, $density );
      if ( $two !== NULL )
        $set = "$one 1x, $two " . rtrim ( rtrim ( number_format ( $density, 2, '.', '' ), '0' ), '.' ) . 'x';
    }

    if ( ! $chain )
      return padImgTag ( $one, $set, $w1, $h1, $opts ['alt'] );

    $more = $holds ? padImgHold ( $one, $holds, $base, $w1, $h1 ) : '';

    return padFilterStyle () . padImgTag ( $one, $set, $w1, $h1, $opts ['alt'], ( $pixels ? ' pad-img-pixels' : '' ), $more );

  }

  // The element, and once per request the rule that keeps a picture inside its column and
  // shows a quiet colour in its place until the lazy load has it. $class and $more are what
  // the filters add: a class, and the placeholder's style and attributes.

  function padImgTag ( $src, $set, $width, $height, $alt, $class = '', $more = '' ) {

    static $styled = FALSE;

    $style = '';

    if ( ! $styled ) {
      $styled = TRUE;
      $style  = padImgStyle ();
    }

    return $style . "<img class=\"pad-img$class\" src=\"" . padImgAttr ( $src ) . '"'
         . ( $set !== '' ? ' srcset="' . padImgAttr ( $set ) . '"' : '' )
         . " width=\"$width\" height=\"$height\" alt=\"" . padImgAttr ( $alt ) . '"'
         . "$more loading=\"lazy\" decoding=\"async\">";

  }

  function padImgStyle () {

    global $padCsp;

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . padImgAttr ( padNonce () ) . '"' : '';

    return "<style$nonce>"
         . ':where(.pad-img){--pad-img-placeholder:#ecebe7}'
         . '@supports (color:light-dark(#000,#fff)){:where(.pad-img){--pad-img-placeholder:light-dark(#ecebe7,#2a2a28)}}'
         . '.pad-img{max-width:100%;height:auto;background:var(--pad-img-placeholder);vertical-align:middle}'
         . '</style>';

  }

  // An option of one word as a text - 'array' for a list, which no option takes.

  function padImgWord ( $value ) {

    return is_scalar ( $value ) ? (string) $value : 'array';

  }

  function padImgAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The source: a name below www/<application>/ as {pad:asset} takes it - the same check of
  // its form, then the real path must still be inside - and a picture GD reads. A JPEG
  // whose camera stood on its side says so in its EXIF orientation; the sizes are those of
  // the picture turned upright.

  function padImgSource ( $file ) {

    global $padApp, $padCheckSyntax;

    $name = padAssetName ( $file, 'the img tag' );

    if ( $name === NULL )
      return NULL;

    $dir  = realpath ( dirname ( APPS ) . "/www/$padApp" );
    $path = ( $dir === FALSE ) ? FALSE : realpath ( "$dir/$name" );

    if ( $path === FALSE or ! str_starts_with ( $path, $dir . DIRECTORY_SEPARATOR ) or ! is_file ( $path ) ) {
      if ( $padCheckSyntax )
        padError ( "the img tag has no picture www/$padApp/$name" );
      return NULL;
    }

    $info  = @getimagesize ( $path );
    $types = [ IMAGETYPE_JPEG => 'jpeg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp' ];

    if ( ! $info or ! isset ( $types [ $info [2] ] ) ) {
      if ( $padCheckSyntax )
        padError ( "the img tag cannot read www/$padApp/$name - a JPEG, PNG, GIF or WebP picture is what it resizes" );
      return NULL;
    }

    // A picture beyond 64 megapixels is refused before it is decoded - GD's memory is not
    // PHP's, and a PNG of 97 KB of 10,000 by 10,000 took 250 MB on every view that missed
    // its thumbnail; one of 40,000 by 40,000 asked for some 6 GB.

    if ( $info [0] * $info [1] > 64000000 ) {
      if ( $padCheckSyntax )
        padError ( "the img tag will not resize www/$padApp/$name - " . $info [0] . ' x ' . $info [1] . ' pixels, 64 megapixels at most' );
      return NULL;
    }

    $type = $types [ $info [2] ];
    $turn = 1;

    if ( $type == 'jpeg' and function_exists ( 'exif_read_data' ) ) {
      $exif = @exif_read_data ( $path );
      $turn = (int) ( $exif ['Orientation'] ?? 1 );
      if ( $turn < 1 or $turn > 8 )
        $turn = 1;
    }

    $sw = $info [0];
    $sh = $info [1];

    if ( $turn >= 5 )
      list ( $sw, $sh ) = [ $sh, $sw ];

    return [ $path, $name, $type, $sw, $sh, $turn ];

  }

  // The part of the upright source that is drawn - x, y, width, height - the size of the
  // 1x picture, and how much larger than that the source is: the most a 2x can be.

  function padImgSizes ( $sw, $sh, $width, $height, $fit ) {

    if ( ! $width and ! $height )
      list ( $width, $height ) = [ $sw, $sh ];
    elseif ( ! $height )
      $height = $width * $sh / $sw;
    elseif ( ! $width )
      $width = $height * $sw / $sh;

    if ( $fit == 'contain' ) {
      $scale = min ( $width / $sw, $height / $sh, 1 );
      $w1    = max ( 1, (int) round ( $sw * $scale ) );
      $h1    = max ( 1, (int) round ( $sh * $scale ) );
      return [ [ 0, 0, $sw, $sh ], $w1, $h1, $sw / $w1 ];
    }

    // cover: the largest part of the source with the box's proportion, around the centre;
    // a box larger than that part is shrunk to it.

    $ratio = $width / $height;
    $cw    = min ( $sw, $sh * $ratio );
    $ch    = $cw / $ratio;
    $cx    = (int) round ( ( $sw - $cw ) / 2 );
    $cy    = (int) round ( ( $sh - $ch ) / 2 );
    $f     = min ( 1, $cw / $width );
    $w1    = max ( 1, (int) round ( $width  * $f ) );
    $h1    = max ( 1, (int) round ( $height * $f ) );

    return [ [ $cx, $cy, (int) round ( $cw ), (int) round ( $ch ) ], $w1, $h1, $cw / $w1 ];

  }

  // Ten hex digits of the picture's contents, kept per request: a picture shown twice on a
  // page is read once.

  function padImgHash ( $path ) {

    static $memo = [];

    if ( ! isset ( $memo [$path] ) ) {
      $algo = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
      $memo [$path] = (string) hash_file ( $algo, $path );
    }

    return $memo [$path];

  }

  // One thumbnail: its address, made first when the file is not there. It is written to a
  // name of its own and renamed into place, so a request that arrives while another makes
  // it never reads half a picture. NULL when it cannot be written - said once. $filters are
  // the image filters of filter=, applied after the picture was resized, $scale the density
  // of this picture - 1, or that of the 2x one - which a filter measured in CSS pixels reads.
  //
  // A filtered thumbnail's name holds that density too: a blur of 6 CSS pixels is 12 pixels
  // on the 2x picture of a box of 100 by 50 and 6 on the 1x picture of a box of 200 by 100 -
  // the same size of the same part of the source, another picture. Without it both had one
  // name, and whichever was made first was shown for both.

  function padImgThumb ( $path, $type, $turn, $crop, $w, $h, $format, $quality, $base, $filters = [], $scale = 1 ) {

    global $padApp;

    list ( $file, $url, $hash ) = padImgName ( $filters ? [ $base, $scale ] : $base, $w, $h, [ 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp' ] [$format] );

    if ( is_file ( $file ) )
      return $url;

    if ( ! padImgDir () )
      return NULL;

    $out = padImgDraw ( $path, $type, $turn, $crop, $w, $h, $format == 'jpeg' );

    if ( ! $out )
      return NULL;

    foreach ( $filters as list ( , $spec, $options ) )
      $out = $spec ['apply'] ( $out, $options, $scale );

    if ( $format != 'jpeg' )
      imagealphablending ( $out, FALSE );

    $temp = dirname ( $file ) . "/." . $hash . '-' . bin2hex ( random_bytes ( 4 ) );

    $done = match ( $format ) {
      'jpeg' => imagejpeg ( $out, $temp, $quality ),
      'png'  => imagepng  ( $out, $temp, 9 ),
      'webp' => imagewebp ( $out, $temp, $quality )
    };

    if ( ! $done or ! @rename ( $temp, $file ) ) {
      @unlink ( $temp );
      padImgFailed ( "the img tag cannot write its thumbnails to www/$padApp/_thumbs/" );
      return NULL;
    }

    return $url;

  }

  // The file, the address and the hash of what the img tag keeps in _thumbs/: sixteen hex
  // digits of a hash of what it is made from, and an extension the engine picked.

  function padImgName ( $base, $w, $h, $ext ) {

    global $padApp, $padRoot;

    $hash = substr ( hash ( 'sha256', serialize ( [ $base, $w, $h ] ) ), 0, 16 );

    return [ dirname ( APPS ) . "/www/$padApp/_thumbs/$hash.$ext", ( $padRoot ?? '/' ) . "$padApp/_thumbs/$hash.$ext", $hash ];

  }

  // The _thumbs/ directory, made when it is not there; FALSE - said once - when the web server
  // cannot write it.

  function padImgDir () {

    global $padApp;

    $dir = dirname ( APPS ) . "/www/$padApp/_thumbs";

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, 0755, TRUE );

    if ( ! is_dir ( $dir ) or ! is_writable ( $dir ) ) {
      padImgFailed ( "the img tag cannot write its thumbnails to www/$padApp/_thumbs/ - make the directory writable for the web server" );
      return FALSE;
    }

    return TRUE;

  }

  // The part $crop of the upright source, resized to $w by $h - a GD picture, NULL when the
  // source cannot be read. The source is decoded once for the thumbnails, the dots and the
  // placeholders of one picture alike, and kept until padImgDraw ( NULL ) lets it go - or
  // another source is asked for, the one before let go before the next is decoded: never two
  // at a time.

  function padImgDraw ( $path, $type = '', $turn = 1, $crop = [], $w = 0, $h = 0, $white = TRUE ) {

    static $memo = [ '', NULL ];

    if ( $path === NULL ) {
      $memo = [ '', NULL ];
      return NULL;
    }

    if ( $memo [0] !== "$path|$turn" ) {

      $memo = [ '', NULL ];

      $from = [ 'jpeg' => 'imagecreatefromjpeg', 'png' => 'imagecreatefrompng',
                'gif'  => 'imagecreatefromgif',  'webp' => 'imagecreatefromwebp' ] [$type];

      $src = @$from ( $path );

      if ( ! $src ) {
        padImgFailed ( "the img tag cannot read the picture " . basename ( $path ) );
        return NULL;
      }

      $memo = [ "$path|$turn", padImgOrient ( $src, $turn ) ];

    }

    $out = imagecreatetruecolor ( $w, $h );

    // A JPEG has no transparency: what was transparent becomes white. PNG and WebP keep it.

    if ( $white )
      imagefill ( $out, 0, 0, imagecolorallocate ( $out, 255, 255, 255 ) );
    else {
      imagealphablending ( $out, FALSE );
      imagesavealpha ( $out, TRUE );
      imagefill ( $out, 0, 0, imagecolorallocatealpha ( $out, 0, 0, 0, 127 ) );
      imagealphablending ( $out, TRUE );
    }

    imagecopyresampled ( $out, $memo [1], 0, 0, $crop [0], $crop [1], $w, $h, $crop [2], $crop [3] );

    return $out;

  }

  // halftone or ascii: the picture written another way in place of the <img>. What the
  // filter makes of the filtered 1x picture is kept in _thumbs/ - an .svg, a .txt - under a
  // name of its own; shown, it gets the alt text as its name. '' when it cannot be made.

  function padImgMarkup ( $path, $type, $turn, $crop, $w, $h, $images, $markup, $base, $alt ) {

    list ( $name, $spec, $options ) = $markup;

    list ( $file, , $hash ) = padImgName ( [ $base, $name, $options ], $w, $h, $spec ['ext'] );

    if ( ! is_file ( $file ) ) {

      if ( ! padImgDir () )
        return '';

      $picture = padImgDraw ( $path, $type, $turn, $crop, $w, $h, TRUE );

      if ( ! $picture )
        return '';

      foreach ( $images as list ( , $one, $o ) )
        $picture = $one ['apply'] ( $picture, $o, 1 );

      padImgKeep ( $file, $hash, $spec ['make'] ( $picture, $options ) );

    }

    $kept = (string) @file_get_contents ( $file );

    return ( $kept === '' ) ? '' : $spec ['show'] ( $kept, $options, $w, $h, $alt );

  }

  // The placeholders: the style and the attributes they add to the <img>, made from the 1x
  // thumbnail just written - the picture as the page shows it - and kept in _thumbs/ as a
  // .json under a name of their own.

  function padImgHold ( $url, $holds, $base, $w, $h ) {

    global $padApp, $padRoot;

    list ( $file, , $hash ) = padImgName ( [ $base, padFilterKey ( $holds ) ], $w, $h, 'json' );

    if ( ! is_file ( $file ) ) {

      $thumb   = dirname ( APPS ) . "/www/$padApp/_thumbs/" . basename ( $url );
      $picture = is_file ( $thumb ) ? @imagecreatefromstring ( (string) file_get_contents ( $thumb ) ) : FALSE;

      if ( ! $picture )
        return '';

      $style = [];
      $attrs = [];

      foreach ( $holds as list ( , $spec, $options ) ) {
        $held  = $spec ['hold'] ( $picture, $options );
        $style = array_merge ( $style, $held ['style'] ?? [] );
        $attrs = array_merge ( $attrs, $held ['attrs'] ?? [] );
      }

      padImgKeep ( $file, $hash, json_encode ( [ 'style' => $style, 'attrs' => $attrs ], JSON_UNESCAPED_SLASHES ) );

    }

    $held = json_decode ( (string) @file_get_contents ( $file ), TRUE );

    if ( ! is_array ( $held ) )
      return '';

    $more  = '';
    $style = '';

    foreach ( $held ['attrs'] ?? [] as $name => $value )
      $more .= " $name=\"" . padImgAttr ( $value ) . '"';

    foreach ( $held ['style'] ?? [] as $name => $value )
      $style .= "$name:$value;";

    return $more . ( $style !== '' ? ' style="' . padImgAttr ( rtrim ( $style, ';' ) ) . '"' : '' );

  }

  // A text kept in _thumbs/, written to a name of its own and renamed into place.

  function padImgKeep ( $file, $hash, $text ) {

    global $padApp;

    $temp = dirname ( $file ) . "/." . $hash . '-' . bin2hex ( random_bytes ( 4 ) );

    if ( @file_put_contents ( $temp, $text ) === FALSE or ! @rename ( $temp, $file ) ) {
      @unlink ( $temp );
      padImgFailed ( "the img tag cannot write its thumbnails to www/$padApp/_thumbs/" );
    }

  }

  function padImgFailed ( $message ) {

    static $said = FALSE;

    if ( ! $said ) {
      $said = TRUE;
      padError ( $message );
    }

  }

  // EXIF orientation 1 to 8: as stored, mirrored, turned half, flipped, and the four that
  // lie on their side.

  function padImgOrient ( $image, $turn ) {

    if ( $turn == 2 or $turn == 4 or $turn == 5 or $turn == 7 )
      imageflip ( $image, IMG_FLIP_HORIZONTAL );

    $angle = [ 3 => 180, 4 => 180, 5 => 90, 6 => 270, 7 => 270, 8 => 90 ] [$turn] ?? 0;

    return $angle ? imagerotate ( $image, $angle, 0 ) : $image;

  }

?>
