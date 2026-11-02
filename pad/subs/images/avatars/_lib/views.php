<?php

  // Deterministic geometric portraits. The hash controls geometry and colours; no input
  // becomes SVG markup. Each style uses the same 100-unit square and accessible label.
  function padAvatarGeometric ( $value, $size = 64, $style = 'rings', $label = '' ) {

    $hash = hash ( 'sha256', mb_strtolower ( padAvatarName ( $value ) ) );
    $size = max ( 8, min ( 2048, (int) $size ) );
    $label = trim ( (string) $label ) ?: 'Geometric avatar';
    $colour = fn ( $at ) => PAD_AVATAR_PALETTE [ hexdec ( substr ( $hash, $at, 2 ) ) % 10 + 1 ] [0];
    $art = '<rect width="100" height="100" rx="18" fill="' . $colour ( 0 ) . '"/>';

    if ( $style === 'mosaic' ) {
      for ( $y = 0; $y < 4; $y++ )
        for ( $x = 0; $x < 4; $x++ ) {
          $n = $y * 4 + $x;
          $art .= '<path fill="' . $colour ( 2 + $n * 2 ) . '" d="M' . ( $x * 25 ) . ',' . ( $y * 25 ) . 'h25v25z"/>';
        }
    } elseif ( $style === 'beam' ) {
      for ( $i = 0; $i < 4; $i++ ) {
        $y = 14 + $i * 22;
        $art .= '<path stroke="' . $colour ( 2 + $i * 2 ) . '" stroke-width="12" d="M0,' . $y . 'L100,' . ( 100 - $y ) . '"/>';
      }
    } else {
      $x = 35 + hexdec ( $hash [10] ) * 2;
      $y = 35 + hexdec ( $hash [11] ) * 2;
      foreach ( [ 36, 25, 14 ] as $i => $radius )
        $art .= '<circle cx="' . $x . '" cy="' . $y . '" r="' . $radius . '" fill="' . $colour ( 2 + $i * 2 ) . '"/>';
    }

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-avatar-geometric" role="img" aria-label="' . padAvatarAttr ( $label ) . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 100 100"><title>' . padAvatarAttr ( $label ) . '</title>' . $art . '</svg>';

  }

  // Remote photos are fetched by the browser only. Local names stay within the app's
  // public directory (including symlinks), and a missing file uses the local fallback.
  function padAvatarPhotoSource ( $source ) {

    global $padApp;

    if ( ! is_scalar ( $source ) )
      return '';
    $source = trim ( padUnprotectQuotes ( (string) $source ) );
    if ( $source === '' or preg_match ( '/[\x00-\x20\x7f\\\\<>"\']/', $source ) )
      return '';
    if ( preg_match ( '~^https?://~i', $source ) ) {
      $url = parse_url ( $source );
      return $url && ! empty ( $url ['host'] ) && ! isset ( $url ['user'] ) && ! isset ( $url ['pass'] ) ? $source : '';
    }
    if ( preg_match ( '~[:?#]|^/|(?:^|/)\.\.(?:/|$)|%~', $source ) )
      return '';
    $root = realpath ( dirname ( APPS ) . "/www/$padApp" );
    $file = $root ? realpath ( "$root/$source" ) : FALSE;
    if ( ! $file or ! str_starts_with ( $file, "$root/" ) or ! is_file ( $file ) )
      return '';
    $info = @getimagesize ( $file );
    return $info && in_array ( $info [2], [ IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP ], TRUE ) ? padAsset ( $source ) : '';

  }

  function padAvatarPhoto ( $source, $name, $size = 64, $fallback = 'initials', $shape = 'circle' ) {

    $size = max ( 8, min ( 2048, (int) $size ) );
    $name = trim ( (string) $name ) ?: 'Avatar';
    $portrait = match ( $fallback ) {
      'identicon' => padIdenticon ( $name, $size, $name ),
      'geometric' => padAvatarGeometric ( $name, $size, 'rings', $name ),
      default => padAvatar ( $name, $size, $shape, $name )
    };
    $src = padAvatarPhotoSource ( $source );
    if ( $src === '' )
      return $portrait;

    $style = '';
    if ( padWidgetOnce ( 'style:avatar-photo' ) )
      $style = '<style' . padAvatarNonce () . '>.pad-avatar-photo{display:inline-grid;vertical-align:middle;overflow:hidden;border-radius:50%;isolation:isolate}.pad-avatar-photo-square{border-radius:18%}.pad-avatar-photo>*{grid-area:1/1}.pad-avatar-photo img{position:relative;width:100%;height:100%;object-fit:cover}.pad-avatar-photo-fallback{display:flex}</style>'
             . '<script' . padAvatarNonce () . '>document.addEventListener("error",function(e){if(e.target instanceof HTMLImageElement&&e.target.classList.contains("pad-avatar-photo-image"))e.target.remove()},true)</script>';

    return $style . '<span class="pad-avatar-photo' . ( $shape === 'square' ? ' pad-avatar-photo-square' : '' ) . '" role="img" aria-label="' . padAvatarAttr ( $name ) . '"><span class="pad-avatar-photo-fallback" aria-hidden="true">' . $portrait . '</span><img class="pad-avatar-photo-image" src="' . padAvatarAttr ( $src ) . '" width="' . $size . '" height="' . $size . '" alt="" aria-hidden="true" loading="lazy" decoding="async" referrerpolicy="no-referrer"></span>';

  }

  function padAvatarChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;
    $value = padTagParm ( $option, $default );
    if ( ! is_string ( $value ) or ! in_array ( $value, $choices, TRUE ) ) {
      if ( $padCheckSyntax )
        padError ( "the avatar's $option= must be " . implode ( ', ', $choices ) );
      return $default;
    }
    return $value;

  }

?>
