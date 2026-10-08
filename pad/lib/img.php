<?php

  // Thumbnails - the {img} tag. A picture of www/<application>/ is resized with GD to the
  // size the page shows it at, once, and written beside the entry point as a file the web
  // server sends on its own: www/<application>/_thumbs/<hash>.<ext>. The page gets an
  // <img> with its width and height (no layout shift), loading="lazy", decoding="async"
  // and a srcset whose second picture is made at twice the size for a sharp screen.
  //
  //   {img 'photos/harbour.jpg', width=400, height=300, alt='The harbour at dawn'}
  //   {img 'photos/dunes.jpg', width=300, fit='contain', format='webp', quality=70, alt=''}
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

  function padImg ( $file, $opts ) {

    global $padCheckSyntax;

    $source = padImgSource ( $file );

    if ( $source === NULL )
      return '';

    list ( $path, $name, $type, $sw, $sh, $turn ) = $source;

    $format = strtolower ( (string) ( $opts ['format'] ?? '' ) );
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

    $fit = strtolower ( (string) ( $opts ['fit'] ?? 'cover' ) );

    if ( ! in_array ( $fit, [ 'cover', 'contain' ], TRUE ) ) {
      if ( $padCheckSyntax )
        padError ( "the img tag has no fit '" . padMakeSafe ( $fit, 10 ) . "' - cover or contain" );
      $fit = 'cover';
    }

    $width  = max ( 0, (int) ( $opts ['width']  ?? 0 ) );
    $height = max ( 0, (int) ( $opts ['height'] ?? 0 ) );

    list ( $crop, $w1, $h1, $max ) = padImgSizes ( $sw, $sh, $width, $height, $fit );

    $hash = padImgHash ( $path );
    $base = [ $hash, $crop, $format, $quality, $turn ];
    $one  = padImgThumb ( $path, $type, $turn, $crop, $w1, $h1, $format, $quality, $base );

    if ( $one === NULL )
      return padImgTag ( padAsset ( $name ), '', $w1, $h1, $opts ['alt'] );

    // The 2x picture, as far as the source reaches: a density of 1.2 or more is worth a
    // second file, less is the same picture again.

    $density = min ( 2, $max );
    $set     = '';

    if ( $density >= 1.2 ) {
      $two = padImgThumb ( $path, $type, $turn, $crop, (int) round ( $w1 * $density ), (int) round ( $h1 * $density ), $format, $quality, $base );
      if ( $two !== NULL )
        $set = "$one 1x, $two " . rtrim ( rtrim ( number_format ( $density, 2, '.', '' ), '0' ), '.' ) . 'x';
    }

    return padImgTag ( $one, $set, $w1, $h1, $opts ['alt'] );

  }

  // The element, and once per request the rule that keeps a picture inside its column and
  // shows a quiet colour in its place until the lazy load has it.

  function padImgTag ( $src, $set, $width, $height, $alt ) {

    static $styled = FALSE;

    $style = '';

    if ( ! $styled ) {
      $styled = TRUE;
      $style  = padImgStyle ();
    }

    return $style . '<img class="pad-img" src="' . padImgAttr ( $src ) . '"'
         . ( $set !== '' ? ' srcset="' . padImgAttr ( $set ) . '"' : '' )
         . " width=\"$width\" height=\"$height\" alt=\"" . padImgAttr ( $alt ) . '"'
         . ' loading="lazy" decoding="async">';

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

  function padImgAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The source: a name below www/<application>/ as {asset} takes it - the same check of
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
  // it never reads half a picture. NULL when it cannot be written - said once.

  function padImgThumb ( $path, $type, $turn, $crop, $w, $h, $format, $quality, $base ) {

    global $padApp, $padRoot;

    $ext  = [ 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp' ] [$format];
    $hash = substr ( hash ( 'sha256', serialize ( [ $base, $w, $h ] ) ), 0, 16 );
    $dir  = dirname ( APPS ) . "/www/$padApp/_thumbs";
    $file = "$dir/$hash.$ext";
    $url  = ( $padRoot ?? '/' ) . "$padApp/_thumbs/$hash.$ext";

    if ( is_file ( $file ) )
      return $url;

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, 0755, TRUE );

    if ( ! is_dir ( $dir ) or ! is_writable ( $dir ) ) {
      padImgFailed ( "the img tag cannot write its thumbnails to www/$padApp/_thumbs/ - make the directory writable for the web server" );
      return NULL;
    }

    $from = [ 'jpeg' => 'imagecreatefromjpeg', 'png' => 'imagecreatefrompng',
              'gif'  => 'imagecreatefromgif',  'webp' => 'imagecreatefromwebp' ] [$type];

    $src = @$from ( $path );

    if ( ! $src ) {
      padImgFailed ( "the img tag cannot read the picture " . basename ( $path ) );
      return NULL;
    }

    $src = padImgOrient ( $src, $turn );
    $out = imagecreatetruecolor ( $w, $h );

    // A JPEG has no transparency: what was transparent becomes white. PNG and WebP keep it.

    if ( $format == 'jpeg' )
      imagefill ( $out, 0, 0, imagecolorallocate ( $out, 255, 255, 255 ) );
    else {
      imagealphablending ( $out, FALSE );
      imagesavealpha ( $out, TRUE );
      imagefill ( $out, 0, 0, imagecolorallocatealpha ( $out, 0, 0, 0, 127 ) );
      imagealphablending ( $out, TRUE );
    }

    imagecopyresampled ( $out, $src, 0, 0, $crop [0], $crop [1], $w, $h, $crop [2], $crop [3] );

    if ( $format != 'jpeg' )
      imagealphablending ( $out, FALSE );

    $temp = "$dir/." . $hash . '-' . bin2hex ( random_bytes ( 4 ) );

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
