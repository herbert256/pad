<?php

  // The PHP factory accepts options without depending on a running PAD tag. Its original
  // five styles keep the 64-pixel default and 8–1024 bounds. The three styles supplied by
  // the avatar subsystem also expose their native helper options and 2048-pixel bound.

  function padAvatarMake ( $kind, $identity, $options = [] ) {

    if ( ! is_string ( $kind ) or ! in_array ( $kind, [ 'initials', 'identicon', 'rings', 'mosaic', 'pixelbot', 'geometric', 'gravatar', 'photo' ], TRUE ) )
      throw new InvalidArgumentException ( 'Unknown avatar style' );
    if ( ! is_scalar ( $identity ) )
      throw new InvalidArgumentException ( 'Avatar identity must be scalar' );
    if ( ! is_array ( $options ) )
      throw new InvalidArgumentException ( 'Avatar options must be an array' );

    include_once __DIR__ . '/identicon.php';
    include_once __DIR__ . '/gravatar.php';
    include_once __DIR__ . '/views.php';

    $oldStyle = in_array ( $kind, [ 'initials', 'identicon', 'rings', 'mosaic', 'pixelbot' ], TRUE );
    $size = max ( 8, min ( $oldStyle ? 1024 : 2048, (int) ( $options ['size'] ?? 64 ) ) );

    // Photo URLs and Gravatar addresses retain their native normalization rules. In
    // particular a photo path is case-sensitive and must not be normalized as a name.

    if ( $kind === 'photo' )
      return padAvatarPhoto ( $identity, $options ['alt'] ?? $options ['label'] ?? 'Avatar', $size,
        padAvatarMakeChoice ( $options, 'fallback', 'initials', [ 'initials', 'identicon', 'geometric' ] ),
        padAvatarMakeChoice ( $options, 'shape', 'circle', [ 'circle', 'square' ] ) );

    if ( $kind === 'gravatar' ) {
      $default = $options ['default'] ?? 'mp';
      if ( ! is_string ( $default ) or ( ! in_array ( $default, PAD_GRAVATAR_DEFAULTS, TRUE ) and ! preg_match ( '#^https://[^\s"<>]+$#i', $default ) ) )
        throw new InvalidArgumentException ( 'Invalid Gravatar default' );
      return padGravatar ( $identity, $size, $default, $options ['alt'] ?? $options ['label'] ?? '',
        padAvatarMakeChoice ( $options, 'fallback', '', [ '', 'avatar', 'identicon' ] ) );
    }

    if ( $kind === 'geometric' )
      return padAvatarGeometric ( $identity, $size,
        padAvatarMakeChoice ( $options, 'style', 'rings', [ 'rings', 'mosaic', 'beam' ] ), $options ['label'] ?? '' );

    $identity = padAvatarName ( (string) $identity );
    $palette = $options ['palette'] ?? [ '#235789', '#f1b24a', '#d1495b', '#197278', '#f5f1e3' ];
    if ( ! is_array ( $palette ) or count ( $palette ) < 2 or count ( $palette ) > 16 )
      throw new InvalidArgumentException ( 'Palette needs 2 to 16 hex colors' );
    foreach ( $palette as $color )
      if ( ! is_string ( $color ) or ! preg_match ( '/^#[0-9a-f]{6}$/iD', $color ) )
        throw new InvalidArgumentException ( 'Palette colors must be #rrggbb' );
    $palette = array_values ( $palette );

    if ( $kind === 'initials' )
      return padAvatar ( $identity, $size, $options ['shape'] ?? 'circle', $options ['label'] ?? '' );
    if ( $kind === 'identicon' )
      return padIdenticon ( $identity, $size, $options ['title'] ?? $options ['label'] ?? '' );

    // These three styles use NFC, trimmed, case-sensitive identities. Preserve the
    // established geometry and palette separately from geometric's own named styles.

    $hash = hash ( 'sha256', $identity );
    $draw = include __DIR__ . "/art/$kind.php";
    $label = htmlspecialchars ( (string) ( $options ['label'] ?? 'Avatar' ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    return '<svg xmlns="http://www.w3.org/2000/svg" role="img" class="pad-avatar pad-avatar-' . $kind . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 100 100" aria-label="' . $label . '"><title>' . $label . '</title>' . $draw . '</svg>';

  }

  function padAvatarMakeChoice ( $options, $name, $default, $choices ) {

    $value = $options [$name] ?? $default;
    if ( ! is_string ( $value ) or ! in_array ( $value, $choices, TRUE ) )
      throw new InvalidArgumentException ( 'Invalid avatar ' . $name );
    return $value;

  }

  // Tag adapter for the new artwork styles. Master initials, identicon, geometric,
  // photo and Gravatar types keep their own validation, defaults and diagnostics.

  function padAvatarArtTag ( $kind ) {

    global $padParm, $padCheckSyntax;

    $options = [ 'size' => padWidgetNumber ( 'avatar', 'size', 64 ) ];
    foreach ( [ 'label', 'palette' ] as $key )
      if ( ( $value = padTagParm ( $key, NULL ) ) !== NULL )
        $options [$key] = $key === 'label' ? padParmText ( $value ) : $value;

    try {
      return padAvatarMake ( $kind, padParmText ( $padParm ), $options );
    } catch ( InvalidArgumentException $error ) {
      if ( $padCheckSyntax )
        padError ( $error->getMessage () );
      return '';
    }

  }

?>
