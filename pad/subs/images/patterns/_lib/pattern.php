<?php

  // Patterns - the pattern subsystem: seeded SVG backgrounds and generative art, drawn on the
  // server as inline SVG, or as a CSS url() of a data URI for a style attribute. A kind is a
  // file of subs/images/patterns/types/, which answers what it takes and how it draws:
  //
  //   return [ 'size'    => [ 90, 20, 1000 ],          the motif's size: default, least, most
  //            'colors'  => [ '#f6f1e7', '#b8a283' ],  background first, then the inks
  //            'opacity' => 1,                         the default opacity, when not 1
  //            'options' => [ 'levels' => [ 12, 2, 60 ] ],   its own: default, least, most
  //            'count'   => [ 'triangles', 50000, function ( $p ) { ... }, 'a larger size' ],
  //            'draw'    => function ( $p ) { ... return [ $defs, $body ]; } ];
  //
  // $p holds w and h, the size, bg and ink (the colours), id (the prefix of the ids the
  // drawing defines) and the kind's own options; the drawing reads its random numbers from
  // padPatternRand and writes its numbers with padPatternNum.
  //
  // The budget: a kind that draws a shape per place of the picture - triangles, hexagons,
  // truchet, chevron, waves, voronoi - or traces a grid - topography - answers in 'count' what
  // it draws, the most it draws, how many it would for these options and what to change. Each
  // number between its bounds is not enough: a topography of 4000 by 4000 at size 20 and 60
  // levels took 26 seconds, 415 MB and wrote 107 MB of SVG, and triangles of 6 on 4000 by 4000
  // 25 MB - on every view, as a pattern is drawn on every request. Past its count a pattern is
  // an error under the strict check (padPattern: always) and is not drawn - '' - without it:
  // no single value is the one to put back to its default.
  //
  // padPatternMake     the SVG of a pattern, or its url() - '' for a kind that is not there
  // padPatternOptions  the options a kind takes: the common ones and its own
  // padPatternKind     a kind's answer, its file read once a request
  // padPatternColors   a list of colours from a text or an array - FALSE when one is no colour
  // padPatternNum      a number as the SVG writes it: so many decimals at most, no -0
  // padPatternRand     the next number of the seeded sequence, from 0 up to, not including, 1
  // padPatternPick     one value of a list, picked by the sequence
  // padPatternFill     the rect that covers the picture with a <pattern> of the defs
  // padPatternUrl      the SVG as url(data:image/svg+xml,...), safe in either quotes
  // padPatternSimilar  the kinds that look like a name that is none - for the error message
  //
  // The same options give the same bytes, on every request and every machine: the random
  // numbers are the sequence of lib/fake.php (xorshift32, padFakeSeed), seeded with the kind
  // and seed= for this one pattern and put back afterwards, as {widget:lorem} does - a page that
  // seeds padFake* for its own data gets the same values with or without a pattern between
  // them - and every number is written rounded to a fixed number of decimals, never as PHP's
  // float to string writes it. A trigonometric value is rounded to ten decimals before it is
  // used, as the charts round theirs: the last bit of a sine is the C library's to choose.
  // Without seed= the seed is 1: a pattern is the same everywhere unless told otherwise.
  //
  // The ids of a pattern's <pattern>, <filter> and gradients are its own on the page - made
  // from its options by padWidgetId, a second of the same pattern numbered - and in a url()
  // simply p: a data URI is a document of its own.
  //
  // Accessibility: a pattern is decoration - aria-hidden, out of the reading order - unless
  // title= names it: then it is role="img" with that name and a <title>. It draws no motion.

  const padPatternCommon = [ 'seed', 'colors', 'size', 'width', 'height', 'opacity', 'as', 'title' ];

  function padPatternKind ( $kind ) {

    static $kinds = [];

    if ( ! array_key_exists ( $kind, $kinds ) )
      $kinds [$kind] = padKind ( 'images/patterns', $kind ) ? include PAD . "subs/images/patterns/types/$kind.php" : NULL;

    return $kinds [$kind];

  }

  function padPatternOptions ( $kind ) {

    $spec = padPatternKind ( $kind );

    return array_merge ( padPatternCommon, array_keys ( $spec ['options'] ?? [] ) );

  }

  // $given: the options as written, by name; $fail: what is done with a message about one -
  // the tag reports it under the strict check, padPattern always. A value that cannot be used
  // is drawn as its default.

  function padPatternMake ( $kind, $given, $fail ) {

    $spec = padPatternKind ( $kind );

    if ( ! is_array ( $spec ) )
      return '';

    $name = "the pattern $kind";

    $width   = padPatternNumber ( $given, 'width',   480, 1, 4000, $name, $fail );
    $height  = padPatternNumber ( $given, 'height',  240, 1, 4000, $name, $fail );
    $size    = padPatternNumber ( $given, 'size',    $spec ['size'] [0], $spec ['size'] [1], $spec ['size'] [2], $name, $fail );
    $opacity = padPatternNumber ( $given, 'opacity', $spec ['opacity'] ?? 1, 0, 1, $name, $fail );

    $as = strtolower ( trim ( (string) ( $given ['as'] ?? 'svg' ) ) );

    if ( $as !== 'svg' and $as !== 'url' ) {
      $fail ( "$name has no as '" . padMakeSafe ( $as, 12 ) . "' - svg (the default) or url" );
      $as = 'svg';
    }

    $seed = $given ['seed'] ?? 1;

    if ( ! is_scalar ( $seed ) or is_bool ( $seed ) or trim ( (string) $seed ) === '' ) {
      $fail ( "$name has no usable seed - a number or a word" );
      $seed = 1;
    }

    $seed = trim ( (string) $seed );

    $colors = $spec ['colors'];

    if ( isset ( $given ['colors'] ) ) {
      $list = padPatternColors ( $given ['colors'] );
      if ( $list === FALSE or ! $list )
        $fail ( "$name has no usable colors '" . padMakeSafe ( is_scalar ( $given ['colors'] ) ? (string) $given ['colors'] : 'array', 40 )
              . "' - a list of colours, the background first: #fff, #1d3557, a name or rgb() / hsl(), none for no background" );
      else
        $colors = $list;
    }

    $title = trim ( is_scalar ( $given ['title'] ?? '' ) ? (string) ( $given ['title'] ?? '' ) : '' );

    // The kind's own options - a number each, between its least and its most.

    $p = [ 'w' => $width, 'h' => $height, 'size' => $size ];

    foreach ( $spec ['options'] ?? [] as $option => list ( $default, $least, $most ) )
      $p [$option] = padPatternNumber ( $given, $option, $default, $least, $most, $name, $fail );

    // The colours: the first the background, the rest the inks - one colour alone is a new
    // background on the kind's own inks.

    $p ['bg']  = $colors [0];
    $p ['ink'] = ( count ( $colors ) > 1 ) ? array_slice ( $colors, 1 ) : array_slice ( $spec ['colors'], 1 );

    if ( ! $p ['ink'] )
      $p ['ink'] = [ $p ['bg'] ];

    if ( isset ( $spec ['count'] ) ) {
      list ( $what, $most, $count, $fix ) = $spec ['count'];
      $n = $count ( $p );
      if ( $n > $most ) {
        $fail ( "$name would draw " . number_format ( $n ) . " $what on $width x $height at size $size - " . number_format ( $most ) . " at most: $fix" );
        return '';
      }
    }

    $p ['id'] = ( $as == 'url' ) ? 'p' : padWidgetId ( 'pattern', [ $kind, $seed, $width, $height, $size, $colors, $opacity, $p ] );

    // The drawing, its random numbers the fake sequence seeded for this pattern alone.

    $state = padFakeState ();

    padFakeSeed ( "pattern:$kind:$seed" );

    list ( $defs, $body ) = $spec ['draw'] ( $p );

    padFakeState ( $state );

    $w = padPatternNum ( $width );
    $h = padPatternNum ( $height );

    $inner = ( $defs !== '' ? "<defs>$defs</defs>" : '' )
           . ( in_array ( strtolower ( $p ['bg'] ), [ 'none', 'transparent' ], TRUE ) ? '' : "<rect width=\"$w\" height=\"$h\" fill=\"" . padPatternAttr ( $p ['bg'] ) . '"/>' )
           . ( $opacity < 1 ? '<g opacity="' . padPatternNum ( $opacity, 3 ) . "\">$body</g>" : $body );

    $open = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"$w\" height=\"$h\" viewBox=\"0 0 $w $h\" preserveAspectRatio=\"xMidYMid slice\"";

    if ( $as == 'url' )
      return padPatternUrl ( "$open>$inner</svg>" );

    $role = ( $title !== '' )
          ? ' role="img" aria-label="' . padPatternAttr ( $title ) . '"><title>' . padPatternAttr ( $title ) . '</title>'
          : ' aria-hidden="true" focusable="false">';

    return str_replace ( '<svg ', "<svg class=\"pad-pattern pad-pattern-$kind\" ", $open ) . $role . $inner . '</svg>';

  }

  // A number option: given as a number - a text of one, '24px' the 24 it says - between its
  // least and its most; else its default, after saying why.

  function padPatternNumber ( $given, $option, $default, $least, $most, $name, $fail ) {

    if ( ! isset ( $given [$option] ) )
      return $default;

    $value = $given [$option];

    if ( is_string ( $value ) )
      $value = preg_replace ( '/\s*px\s*$/i', '', trim ( $value ) );

    if ( ! is_numeric ( $value ) or ! is_finite ( (float) $value ) or $value < $least or $value > $most ) {
      $fail ( "$name has no usable $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : 'array', 20 ) . "' - a number from $least to $most" );
      return $default;
    }

    return $value + 0;

  }

  // A list of colours: a text, its colours apart by commas outside parentheses - '#fff, rgb(0,
  // 0, 0)' - or an array. A colour is a hex colour, a name, or a colour function of numbers;
  // what goes into the SVG is checked here, nothing else. FALSE when one is none of these.

  function padPatternColors ( $value ) {

    if ( is_string ( $value ) )
      $value = preg_split ( '/,(?![^(]*\))/', $value );

    if ( ! is_array ( $value ) )
      return FALSE;

    $list = [];

    foreach ( $value as $color ) {
      if ( ! is_string ( $color ) )
        return FALSE;
      $color = trim ( $color );
      if ( $color === '' )
        continue;
      if ( ! preg_match ( '/^(#[0-9a-f]{3,4}|#[0-9a-f]{6}|#[0-9a-f]{8}|[a-z]{3,30}|(rgba?|hsla?|hwb|lab|lch|oklab|oklch)\([0-9a-z.,%\s\/+-]{1,60}\))$/iD', $color ) )
        return FALSE;
      $list [] = $color;
    }

    return $list;

  }

  function padPatternNum ( $n, $decimals = 2 ) {

    $text = number_format ( (float) $n, $decimals, '.', '' );

    if ( str_contains ( $text, '.' ) )
      $text = rtrim ( rtrim ( $text, '0' ), '.' );

    return ( $text === '-0' ) ? '0' : $text;

  }

  function padPatternRand () {

    return padFakeUnit ();

  }

  function padPatternPick ( $list ) {

    return $list [ padFakeNext () % count ( $list ) ];

  }

  // A sine and a cosine rounded to ten decimals - the same on every machine.

  function padPatternSin ( $a ) { return round ( sin ( $a ), 10 ); }
  function padPatternCos ( $a ) { return round ( cos ( $a ), 10 ); }

  function padPatternFill ( $p, $id ) {

    return '<rect width="' . padPatternNum ( $p ['w'] ) . '" height="' . padPatternNum ( $p ['h'] ) . "\" fill=\"url(#$id)\"/>";

  }

  // The SVG as a CSS url() for a style attribute or a style sheet: url(data:image/svg+xml,...),
  // the SVG written in it with %-escapes for what would end the attribute or the url - both
  // quotes, the parentheses, white space and the backslash - and what a URL or HTML reads as
  // its own: % # & < > and the braces. Unquoted, so it stands in style='...' as in style="...":
  // the single quotes it had around the data ended an attribute of single quotes.

  function padPatternUrl ( $svg ) {

    return 'url(data:image/svg+xml,' . strtr ( $svg, [ '%' => '%25', '#' => '%23', '&' => '%26', '<' => '%3C', '>' => '%3E',
                                                       '"' => '%22', "'" => '%27', '{' => '%7B', '}' => '%7D', '|' => '%7C',
                                                       '(' => '%28', ')' => '%29', ' ' => '%20', "\t" => '%09', "\r" => '%0D',
                                                       "\n" => '%0A', "\f" => '%0C', '\\' => '%5C' ] ) . ')';

  }

  function padPatternAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The kinds close to a name that is none: one that starts with it - topo finds topography -
  // or a small edit distance away. At most three, the closest first.

  function padPatternSimilar ( $name ) {

    $scored = [];

    foreach ( padPatternKinds () as $kind ) {
      $distance = levenshtein ( $name, $kind );
      if ( $name !== '' and ( str_starts_with ( $kind, $name ) or str_starts_with ( $name, $kind ) ) )
        $distance = min ( $distance, 1 );
      if ( $distance <= max ( 1, intdiv ( strlen ( $name ), 3 ) ) )
        $scored [$kind] = $distance;
    }

    asort ( $scored );

    return array_slice ( array_keys ( $scored ), 0, 3 );

  }

?>
