<?php
  function padColorFinite ( $value ) {
    if ( ( ! is_int ( $value ) && ! is_float ( $value ) ) || ! is_finite ( (float) $value ) ) throw new InvalidArgumentException ( 'color channels must be finite native numbers' );
    return (float) $value;
  }

  function padColorLinear ( $v ) { return abs ( $v ) <= 0.04045 ? $v / 12.92 : ( $v < 0 ? -1 : 1 ) * ( ( abs ( $v ) + 0.055 ) / 1.055 ) ** 2.4; }
  function padColorEncoded ( $v ) { return abs ( $v ) <= 0.0031308 ? 12.92 * $v : ( $v < 0 ? -1 : 1 ) * ( 1.055 * abs ( $v ) ** ( 1 / 2.4 ) - 0.055 ); }

  function padColorRead ( $input, $extended = FALSE ) {
    if ( is_string ( $input ) ) {
      if ( ! preg_match ( '/^#(?:[a-f0-9]{3}|[a-f0-9]{4}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $input ) ) throw new InvalidArgumentException ( 'use an RGB hex color or an explicit channel record' );
      $hex = substr ( $input, 1 );
      if ( strlen ( $hex ) <= 4 ) $hex = implode ( '', array_map ( fn ( $c ) => $c . $c, str_split ( $hex ) ) );
      if ( strlen ( $hex ) === 6 ) $hex .= 'ff';
      return array_map ( fn ( $c ) => hexdec ( $c ) / 255, str_split ( $hex, 2 ) );
    }
    if ( ! is_array ( $input ) || array_diff ( array_keys ( $input ), [ 'space', 'channels', 'alpha' ] ) || ! isset ( $input ['channels'] ) || ! is_array ( $input ['channels'] ) || ! array_is_list ( $input ['channels'] ) || count ( $input ['channels'] ) !== 3 ) throw new InvalidArgumentException ( 'expected space/channels/alpha record' );
    $space = ( array_key_exists ( 'space', $input ) ? $input ['space'] : 'srgb' ); $c = array_map ( 'padColorFinite', $input ['channels'] ); $alpha = padColorFinite ( array_key_exists ( 'alpha', $input ) ? $input ['alpha'] : 1 );
    if ( $alpha < 0 || $alpha > 1 || max ( array_map ( 'abs', $c ) ) > 10000 ) throw new InvalidArgumentException ( 'color range exceeded' );
    if ( $space === 'hsl' ) {
      [ $h, $s, $l ] = $c;
      if ( $s < 0 || $s > 1 || $l < 0 || $l > 1 ) throw new InvalidArgumentException ( 'HSL saturation and lightness must be in 0..1' );
      $h = fmod ( fmod ( $h, 360 ) + 360, 360 ); $a = $s * min ( $l, 1 - $l );
      $f = function ( $n ) use ( $h, $l, $a ) { $k = fmod ( $n + $h / 30, 12 ); return $l - $a * max ( -1, min ( $k - 3, 9 - $k, 1 ) ); };
      $c = [ $f ( 0 ), $f ( 8 ), $f ( 4 ) ];
    } elseif ( $space === 'linear-srgb' ) $c = array_map ( 'padColorEncoded', $c );
    elseif ( $space !== 'srgb' ) throw new InvalidArgumentException ( 'space must be srgb, linear-srgb or hsl' );
    foreach ( $c as $v ) if ( ! is_finite ( $v ) || ( ! $extended && ( $v < -1e-12 || $v > 1 + 1e-12 ) ) ) throw new InvalidArgumentException ( 'out-of-gamut color; use gamut with an explicit clipping policy' );
    if ( ! $extended ) $c = array_map ( fn ( $v ) => min ( 1.0, max ( 0.0, $v ) ), $c );
    return [ ...$c, $alpha ];
  }

  function padColorOutput ( $rgba, $space = 'srgb' ) {
    $rgb = array_slice ( $rgba, 0, 3 );
    if ( $space === 'linear-srgb' ) $channels = array_map ( 'padColorLinear', $rgb );
    elseif ( $space === 'hsl' ) {
      [ $r, $g, $b ] = $rgb; $max = max ( $rgb ); $min = min ( $rgb ); $d = $max - $min; $l = ( $max + $min ) / 2;
      // Keep the denominator before rounding lightness: near-white channels can
      // have a nonzero delta while their averaged lightness rounds to one.
      $denominator = $max + $min <= 1 ? $max + $min : ( 1 - $max ) + ( 1 - $min );
      $s = $d == 0 ? 0.0 : $d / $denominator; $h = 0.0;
      if ( $d > 0 ) $h = 60 * ( $max === $r ? fmod ( ( $g - $b ) / $d, 6 ) : ( $max === $g ? ( $b - $r ) / $d + 2 : ( $r - $g ) / $d + 4 ) );
      $channels = [ fmod ( $h + 360, 360 ), $s, $l ];
    } elseif ( $space === 'srgb' ) $channels = $rgb;
    else throw new InvalidArgumentException ( 'unsupported output space' );
    $out = [ 'space' => $space, 'channels' => $channels, 'alpha' => $rgba [3] ];
    if ( $space === 'srgb' ) {
      $out ['hex'] = '#' . implode ( '', array_map ( fn ( $v ) => sprintf ( '%02x', (int) round ( $v * 255 ) ), $rgb ) );
      if ( $rgba [3] < 1 ) $out ['hex'] .= sprintf ( '%02x', (int) round ( $rgba [3] * 255 ) );
    }
    return $out;
  }

  function padColorLuminance ( $rgba ) {
    return 0.2126 * padColorLinear ( $rgba [0] ) + 0.7152 * padColorLinear ( $rgba [1] ) + 0.0722 * padColorLinear ( $rgba [2] );
  }

  function padColorComposite ( $front, $back ) {
    $alpha = $front [3] + $back [3] * ( 1 - $front [3] ); $rgb = [];
    for ( $i = 0; $i < 3; $i++ ) $rgb [] = $alpha == 0 ? 0.0 : ( $front [$i] * $front [3] + $back [$i] * $back [3] * ( 1 - $front [3] ) ) / $alpha;
    return [ ...$rgb, $alpha ];
  }

  function padColorCompute ( $kind, $one, $two, $options ) {
    $allowed = match ( $kind ) { 'convert' => ['to'], 'mix' => ['amount','space'], 'contrast' => ['background'], 'gamut' => ['method'] };
    if ( array_diff ( array_keys ( $options ), $allowed ) ) throw new InvalidArgumentException ( 'unknown option' );
    $a = padColorRead ( $one, $kind === 'gamut' );
    if ( $kind === 'gamut' ) {
      if ( $two !== NULL ) throw new InvalidArgumentException ( 'gamut takes one color' );
      $method = $options ['method'] ?? 'check'; if ( ! in_array ( $method, [ 'check', 'clip' ], TRUE ) ) throw new InvalidArgumentException ( 'gamut method is check or clip' );
      $inside = min ( array_slice ( $a, 0, 3 ) ) >= 0 && max ( array_slice ( $a, 0, 3 ) ) <= 1;
      $out = ['inGamut'=>$inside,'method'=>$method,'color'=>NULL];
      if ( $inside || $method === 'clip' ) $out ['color'] = padColorOutput ( array_map ( fn ( $v ) => max ( 0.0, min ( 1.0, $v ) ), $a ) );
      return $out;
    }
    if ( $kind === 'convert' ) { if ( $two !== NULL ) throw new InvalidArgumentException ( 'convert takes one color' ); return padColorOutput ( $a, $options ['to'] ?? 'srgb' ); }
    $b = padColorRead ( $two );
    if ( $kind === 'mix' ) {
      $t = padColorFinite ( $options ['amount'] ?? 0.5 ); $space = $options ['space'] ?? 'srgb';
      if ( $t < 0 || $t > 1 || ! in_array ( $space, [ 'srgb', 'linear-srgb' ], TRUE ) ) throw new InvalidArgumentException ( 'mix amount is 0..1; space is srgb or linear-srgb' );
      $alpha = ( 1 - $t ) * $a [3] + $t * $b [3]; $rgb = [];
      for ( $i = 0; $i < 3; $i++ ) {
        $x = $space === 'srgb' ? $a [$i] : padColorLinear ( $a [$i] ); $y = $space === 'srgb' ? $b [$i] : padColorLinear ( $b [$i] );
        $v = $alpha == 0 ? 0.0 : ( ( 1 - $t ) * $x * $a [3] + $t * $y * $b [3] ) / $alpha;
        $rgb [] = $space === 'srgb' ? $v : padColorEncoded ( $v );
      }
      return padColorOutput ( [ ...$rgb, $alpha ] );
    }
    if ( $b [3] < 1 && ! array_key_exists ( 'background', $options ) ) throw new InvalidArgumentException ( 'translucent background requires an opaque compositing background' );
    if ( array_key_exists ( 'background', $options ) ) {
      $backdrop = padColorRead ( $options ['background'] ); if ( $backdrop [3] !== 1.0 && $backdrop [3] !== 1 ) throw new InvalidArgumentException ( 'compositing background must be opaque' );
      $b = padColorComposite ( $b, $backdrop );
    }
    $a = padColorComposite ( $a, $b ); $l1 = padColorLuminance ( $a ); $l2 = padColorLuminance ( $b );
    return [ 'ratio' => ( max ( $l1, $l2 ) + 0.05 ) / ( min ( $l1, $l2 ) + 0.05 ), 'foreground' => padColorOutput ( $a ), 'background' => padColorOutput ( $b ), 'method' => 'WCAG2-srgb' ];
  }
?>
