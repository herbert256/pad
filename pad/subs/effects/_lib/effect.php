<?php

  // The effects subsystem: motion and decoration on a text or a block, CSS first.
  //
  //   {typewriter}Hello, world{/typewriter}
  //   {effect:highlighter}the one thing to remember{/effect:highlighter}
  //   {effect 'countup', to=1250, suffix='+'/}
  //   {reveal motion='left'}<div class="card">...</div>{/reveal}
  //
  // An effect is a file of subs/effects/types/ - <kind>.php reads its options and answers its
  // markup - and the files there are the effects there are (padKinds ( 'effects' ),
  // lib/subsystem.php); www/assets/effects/ holds the look of each, <kind>.css, beside the
  // base every effect shares, effect.css, and the two scripts, confetti.js and decipher.js.
  // subs/effects/effect.php is the body the {effect} tag, the effect: type and a bare name all
  // run.
  //
  // What every effect keeps to:
  //
  //   - CSS first: only confetti and decipher have a script. The style of the effects and the style of a
  //     kind are written once per page, with the CSP nonce (padWidgetStyle, padWidgetOnce);
  //     the colours are --pad-effect-* custom properties on .pad-effect with light-dark()
  //     defaults that fall back to the frame's (--accent, --text, --line), so a theme
  //     colours them.
  //   - Reduced motion: every animation stands inside @media (prefers-reduced-motion:
  //     no-preference). A visitor who asks for less motion gets the content still and whole -
  //     typed out, counted up, revealed, marked - never hidden.
  //   - Screen readers read the text once, as it ends: no letter is an announced span. Where
  //     a kind draws letters one by one or in layers - the wave, the glitch - the drawn copy
  //     is aria-hidden and the text stands once beside it for assistive technology; the
  //     typewriter's text is whole in the page while the CSS uncovers it, and the count-up
  //     announces its last number, not the frames.
  //   - Limited: an animation that repeats runs repeat= times (3 by default) - repeat='forever'
  //     is the author's choice; the marquee, which moves for as long as it is there, has a
  //     pause button that works without script and stops while the pointer or the focus is
  //     on it, and the spinner is a status that turns while it is shown.
  //   - The numbers of one effect - its duration, its delay, the length of its text - are
  //     custom properties in a rule of a class of their own (.pad-effect.pe-v1a2b3c4d
  //     {--pad-effect-duration:2s}), written once per page with the nonce beside the style of
  //     the kind - never a style attribute, which the CSP nonce drops: written by the server
  //     from checked values, never from text as given.
  //
  // padEffectTag      runs the file of a kind, its content - the rendered content of the pair,
  //                   or NULL for the single tag - at hand in $padEffectNow
  // padEffectWhat     the tag as written, for what a message says: {effect:x}, {x}, {effect 'x'}
  // padEffectBad      reports a mistake under the strict check
  // padEffectInt, padEffectNumber, padEffectChoice, padEffectFlag, padEffectText,
  // padEffectColor, padEffectColors, padEffectRepeat, padEffectWhen, padEffectThickness - the
  //                   options, checked
  // padEffectSource   the content of the pair, else the first parameter as markup
  // padEffectPlain    the characters of a rendered text, without its markup
  // padEffectLetters  a text cut into its letters - grapheme clusters - each escaped
  // padEffectBlock    whether a content holds blocks, and wants a div around it
  // padEffectVars     the custom properties of one effect as a class and its rule
  // padEffectOpen     the opening tag of an effect's element
  // padEffectStyle    the base style and the style of a kind, once per page each

  function padEffectTag ( $kind, $content ) {

    global $padEffectNow;

    $before       = $padEffectNow ?? NULL;
    $padEffectNow = [ 'kind' => $kind, 'content' => $content ];

    $html = include PAD . "subs/effects/types/$kind.php";

    $padEffectNow = $before;

    return is_string ( $html ) ? $html : '';

  }

  function padEffectKind () {

    return $GLOBALS ['padEffectNow'] ['kind'] ?? '';

  }

  // The content of the pair as it rendered - NULL for a single tag.

  function padEffectContent () {

    return $GLOBALS ['padEffectNow'] ['content'] ?? NULL;

  }

  function padEffectWhat () {

    global $pad, $padType, $padPrefix;

    $kind = padEffectKind ();

    if ( ( $padType [$pad] ?? '' ) == 'effect' )
      return ( ( $padPrefix [$pad] ?? '' ) !== '' ? 'effect:' : '' ) . $kind;

    return "effect '$kind'";

  }

  function padEffectBad ( $message ) {

    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( 'the {' . padEffectWhat () . '} ' . $message );

  }

  // What an option was given as, for a message: the value cut short and made safe.

  function padEffectShow ( $value ) {

    return padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 24 );

  }

  function padEffectRaw ( $option, $default ) {

    $value = padTagParm ( $option, $default );

    return is_string ( $value ) ? trim ( padParmText ( $value ) ) : $value;

  }

  // A whole number option held to its bounds - one that is no whole number, or out of them,
  // is named under the strict check and takes the default.

  function padEffectInt ( $option, $default, $min, $max ) {

    $value = padEffectRaw ( $option, $default );

    if ( ( is_int ( $value ) or ( is_string ( $value ) and preg_match ( '/^-?\d+$/D', $value ) ) )
         and (int) $value >= $min and (int) $value <= $max )
      return (int) $value;

    padEffectBad ( "has no $option '" . padEffectShow ( $value ) . "' - a whole number from $min to $max" );

    return $default;

  }

  // A number option that may have decimals - duration=1.5 - held to its bounds the same way.

  function padEffectNumber ( $option, $default, $min, $max ) {

    $value = padEffectRaw ( $option, $default );

    if ( ( is_int ( $value ) or is_float ( $value ) or ( is_string ( $value ) and preg_match ( '/^-?(\d+\.?\d*|\.\d+)$/D', $value ) ) )
         and is_finite ( (float) $value ) and (float) $value >= $min and (float) $value <= $max )
      return $value + 0;

    padEffectBad ( "has no $option '" . padEffectShow ( $value ) . "' - a number from $min to $max" );

    return $default;

  }

  // An option that is one of a few words.

  function padEffectChoice ( $option, $default, $choices ) {

    $value = padEffectRaw ( $option, $default );
    $value = is_string ( $value ) ? strtolower ( $value ) : $value;

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    padEffectBad ( "has no $option '" . padEffectShow ( $value ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // A bare option given or not - {countup to=90, scroll/}.

  function padEffectFlag ( $option ) {

    $value = padTagParm ( $option, FALSE );

    return ( $value !== FALSE and $value !== '' and $value !== 0 and $value !== '0' );

  }

  // A text option - a label, a prefix - as the characters it holds.

  function padEffectText ( $option, $default = '' ) {

    $value = padTagParm ( $option, $default );

    return is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : $default;

  }

  // A colour a rule may hold: #rgb to #rrggbbaa, a colour's name, a colour function of
  // numbers - rgb(), hsl(), oklch() ... - or a custom property, var(--brand). Nothing that
  // can close the declaration, the rule or the style: no quote, no semicolon, no brace, no
  // <, no url().

  function padEffectColorValid ( $color ) {

    return is_string ( $color ) and (
         preg_match ( '/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/iD', $color )
      or preg_match ( '/^[a-z]{3,20}$/iD', $color )
      or preg_match ( '/^(rgba?|hsla?|hwb|lab|lch|oklab|oklch)\(\s*[0-9a-z.%+\-\s,\/]{1,80}\)$/iD', $color )
      or preg_match ( '/^var\(--[a-z0-9_-]{1,40}\)$/iD', $color ) );

  }

  function padEffectColor ( $option, $default ) {

    $value = padEffectRaw ( $option, NULL );

    if ( $value === NULL )
      return $default;

    if ( padEffectColorValid ( $value ) )
      return $value;

    padEffectBad ( "has no $option '" . padEffectShow ( $value ) . "' - a CSS colour: #f59e0b, gold, rgb(245 158 11), var(--brand)" );

    return $default;

  }

  // Several colours between commas - a comma inside a colour function's brackets belongs to
  // the colour.

  function padEffectColors ( $option, $default, $fewest, $most ) {

    $value = padEffectRaw ( $option, NULL );

    if ( $value === NULL )
      return $default;

    $list = is_string ( $value ) ? array_map ( 'trim', preg_split ( '/,(?![^(]*\))/', $value ) ) : [];

    if ( count ( $list ) >= $fewest and count ( $list ) <= $most and count ( array_filter ( $list, 'padEffectColorValid' ) ) == count ( $list ) )
      return $list;

    padEffectBad ( "has no $option '" . padEffectShow ( $value ) . "' - $fewest to $most CSS colours between commas" );

    return $default;

  }

  // How often an animation runs: repeat=3, or repeat='forever' - as the animation-iteration-
  // count of its CSS.

  function padEffectRepeat ( $default = 3 ) {

    $value = padEffectRaw ( 'repeat', $default );

    if ( is_string ( $value ) and in_array ( strtolower ( $value ), [ 'forever', 'infinite' ], TRUE ) )
      return 'infinite';

    if ( ( is_int ( $value ) or ( is_string ( $value ) and preg_match ( '/^\d+$/D', $value ) ) ) and (int) $value >= 1 and (int) $value <= 100 )
      return (string) (int) $value;

    padEffectBad ( "has no repeat '" . padEffectShow ( $value ) . "' - a whole number from 1 to 100, or forever" );

    return (string) $default;

  }

  // A number of seconds as CSS writes it: 2s, 0.35s.

  function padEffectSeconds ( $seconds ) {

    return rtrim ( rtrim ( number_format ( (float) $seconds, 3, '.', '' ), '0' ), '.' ) . 's';

  }

  // The effect's own first value - the parameter of {typewriter 'Hello'/}, the second of
  // {effect 'typewriter', 'Hello'/}, whose first names the effect - as text.

  function padEffectValue () {

    global $padParm;

    return ( is_string ( $padParm ) or is_int ( $padParm ) or is_float ( $padParm ) ) ? trim ( padParmText ( (string) $padParm ) ) : '';

  }

  // What the effect works on, as markup: the rendered content of the pair, trimmed, or else
  // the first parameter, escaped.

  function padEffectSource () {

    $content = padEffectContent ();

    if ( $content !== NULL )
      return trim ( (string) $content );

    $value = padEffectValue ();

    return ( $value === '' ) ? '' : padProtect ( padWidgetAttr ( $value ) );

  }

  // The characters a rendered text shows: its markup taken off, its character references
  // read. The stand-ins of a value's braces and quotes stay what they are - one character
  // each, made the character again when the page is written out. A <style> or a <script>
  // shows nothing, and goes with its content - the style an effect inside writes on its first
  // use, in its svg, a widget's, confetti's script: strip_tags kept their text, so a wave
  // around a shimmer drew its CSS letter by letter, a glitch flashed it and a typewriter
  // typed it, minutes long.

  function padEffectPlain ( $html ) {

    $text = preg_replace ( '#<(style|script)\b[^>]*>.*?</\1\s*>#is', '', (string) $html );
    $text = strip_tags ( $text );
    $text = html_entity_decode ( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    return trim ( preg_replace ( '/\s+/u', ' ', $text ) );

  }

  // A text cut into what a reader sees as its letters - grapheme clusters, so an accented
  // letter or an emoji with its modifiers is one.

  function padEffectLetters ( $text ) {

    preg_match_all ( '/\X/u', (string) $text, $match );

    return $match [0];

  }

  function padEffectEscape ( $text ) {

    return padProtect ( htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) );

  }

  // Whether a content holds blocks - a paragraph, a list, a card - and wants a <div> around
  // it rather than a <span>.

  function padEffectBlock ( $html ) {

    return (bool) preg_match ( '/<(address|article|aside|blockquote|details|dialog|div|dl|fieldset|figure|footer|form|h[1-6]|header|hr|li|main|nav|ol|p|pre|section|table|ul)\b/i', (string) $html );

  }

  // The custom properties of one effect - its values the server's own, numbers and checked
  // colours - as a class named by them, pe-v and eight hex digits, whose rule goes into the
  // effect's style once per page (padEffectDone): a style attribute is dropped under the CSP
  // nonce the engine supports (style-src 'nonce-...' covers no attribute), and a count-up
  // showed 0, a typewriter typed nothing and every effect lost its timing. '' for none.

  function padEffectVars ( $vars ) {

    global $padEffectRules;

    $css = '';

    foreach ( $vars as $name => $value )
      if ( $value !== NULL and $value !== '' )
        $css .= "--pad-effect-$name:$value;";

    if ( $css === '' )
      return '';

    $class = 'pe-v' . substr ( md5 ( $css ), 0, 8 );

    if ( padWidgetOnce ( "style:effect-$class" ) )
      $padEffectRules [] = ".pad-effect.$class{" . rtrim ( $css, ';' ) . '}';

    return $class;

  }

  // The opening tag of an effect: its element, its classes - the class of its custom
  // properties among them - and the attributes it adds, each written by the kind, already
  // escaped.

  function padEffectOpen ( $kind, $element, $vars = [], $attrs = '', $classes = '' ) {

    $classes = trim ( "$classes " . padEffectVars ( $vars ) );

    return "<$element class=\"pad-effect pad-effect-$kind" . ( $classes !== '' ? " $classes" : '' ) . '"' . $attrs . '>';

  }

  // The style of the effects, once per page: the colours every kind draws in as custom
  // properties with light and dark defaults - the frame's own colours where the page has
  // them - and the rules they share, then the look of the kind.

  function padEffectStyle ( $kind ) {

    $roles = [ 'accent'  => [ 'var(--accent,#2563eb)', 'var(--accent,#60a5fa)' ],
               'accent2' => [ '#db2777',               '#f472b6'               ],
               'accent3' => [ '#0d9488',               '#2dd4bf'               ],
               'text'    => [ 'var(--text,#1d2330)',   'var(--text,#e5e7eb)'   ],
               'mark'    => [ '#fde68a',               'rgba(234,179,8,.38)'   ],
               'track'   => [ 'var(--line,#e3e6eb)',   'var(--line,#2a303b)'   ] ];

    $css = padWidgetStyle ( 'effect', $roles, padEffectAsset ( 'effect.css' ) );

    if ( padWidgetOnce ( "style:effect-$kind" ) )
      $css .= padWidgetSheet ( padEffectAsset ( "$kind.css" ) );

    return $css;

  }

  // A file of www/assets/effects/ (padWidgetFile): linked, or - written in - a style sheet,
  // written a rule a line, sent as one line - the line breaks and the indent after them taken
  // out, as padWidgetAsset sends the widgets' style; a script as it stands, without the line
  // break that ends it.

  function padEffectAsset ( $file ) {

    return padWidgetFile ( "effects/$file" );

  }

  // The effect on the page: its markup, then its style, the style in an svg of no size that
  // hides from a screen reader. An effect stands in a line - a heading, a paragraph - and
  // PHP's tidy (exits/tidy.php, on by default) ends a <p> or an <h2> at a <style> it meets
  // there, before it moves the style into the <head>: the rest of the line fell out of its
  // paragraph and onto a line of its own. A <style> in an inline <svg> is one the tidy takes
  // out without ending the paragraph, and without the tidy it is a style of the whole page as
  // any other is.

  function padEffectDone ( $kind, $html ) {

    global $padEffectRules;

    $css = padEffectStyle ( $kind );

    // The rules of the custom properties of the effect - its own and its letters' - each
    // written once per page, after the kind's style, with the nonce.

    if ( $padEffectRules ?? [] )
      $css .= padProtect ( '<style' . padStyleNonce () . '>' . implode ( '', $padEffectRules ) . '</style>' );

    $padEffectRules = [];

    // A <link> to a style sheet of www/assets/effects/ is no element of an svg - one inside
    // it is never loaded - so it stands before the svg; in a whole page it goes into the
    // <head> before tidy reads the line (padWidgetHead).

    $links = '';

    if ( str_contains ( $css, '<link ' ) )
      $css = preg_replace_callback ( '#<link [^>]*>#', function ( $m ) use ( &$links ) { $links .= $m [0]; return ''; }, $css );

    if ( $css === '' )
      return $html . $links;

    return $html . $links . padProtect ( '<svg class="pad-effect-css" width="0" height="0" aria-hidden="true" focusable="false">' )
                 . $css . padProtect ( '</svg>' );

  }

  // The text a kind needs and was not given - the single tag without its value, or a pair
  // around nothing.

  function padEffectNeedsText ( $example ) {

    padEffectBad ( "needs its text - $example" );

    return '';

  }

  // When a drawn stroke - an underline, a strike, a ring - is drawn: as the page opens, as it
  // scrolls into view (scroll) or, where the kind has it, while the pointer is on it or the
  // focus in it (hover) - the class that says so, '' for the first. Both at once is a mistake.

  function padEffectWhen ( $withHover = TRUE ) {

    $scroll = padEffectFlag ( 'scroll' );
    $hover  = ( $withHover and padEffectFlag ( 'hover' ) );

    if ( $scroll and $hover )
      padEffectBad ( 'is drawn on scroll or on hover, not both' );

    return $hover ? 'pe-hover' : ( $scroll ? 'pe-scroll' : '' );

  }

  // A stroke's thickness: thickness= whole pixels from 1 to 12, NULL when not given - the
  // kind's style then takes one from the size of the letters.

  function padEffectThickness () {

    return ( padTagParm ( 'thickness', NULL ) === NULL ) ? NULL : padEffectInt ( 'thickness', 2, 1, 12 ) . 'px';

  }

?>
