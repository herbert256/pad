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
  // lib/subsystem.php); subs/effects/assets/ holds the look of each, <kind>.css, beside the
  // base every effect shares, effect.css, and the one script, confetti.js. subs/effects/
  // effect.php is the body the {effect} tag, the effect: type and a bare name all run.
  //
  // What every effect keeps to:
  //
  //   - CSS first: only confetti has a script. The style of the effects and the style of a
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
  //     custom properties in a style attribute of its element (style="--pad-effect-duration:
  //     2s"), as the poll writes the width of a bar: written by the server from checked
  //     values, never from text as given.
  //
  // padEffectTag      runs the file of a kind, its content - the rendered content of the pair,
  //                   or NULL for the single tag - at hand in $padEffectNow
  // padEffectWhat     the tag as written, for what a message says: {effect:x}, {x}, {effect 'x'}
  // padEffectBad      reports a mistake under the strict check
  // padEffectInt, padEffectNumber, padEffectChoice, padEffectFlag, padEffectText,
  // padEffectColor, padEffectColors, padEffectRepeat - the options, checked
  // padEffectSource   the content of the pair, else the first parameter as markup
  // padEffectPlain    the characters of a rendered text, without its markup
  // padEffectLetters  a text cut into its letters - grapheme clusters - each escaped
  // padEffectBlock    whether a content holds blocks, and wants a div around it
  // padEffectVars     the custom properties of one effect as a style attribute
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

  // A colour a style attribute may hold: #rgb to #rrggbbaa, a colour's name, a colour
  // function of numbers - rgb(), hsl(), oklch() ... - or a custom property, var(--brand).
  // Nothing that can close the attribute or the declaration: no quote, no semicolon, no
  // brace, no url().

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
  // each, made the character again when the page is written out.

  function padEffectPlain ( $html ) {

    $text = strip_tags ( (string) $html );
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

  // The custom properties of one effect, a style attribute - its values the server's own,
  // numbers and checked colours.

  function padEffectVars ( $vars ) {

    $css = '';

    foreach ( $vars as $name => $value )
      if ( $value !== NULL and $value !== '' )
        $css .= "--pad-effect-$name:$value;";

    return ( $css === '' ) ? '' : ' style="' . htmlspecialchars ( rtrim ( $css, ';' ), ENT_QUOTES, 'UTF-8' ) . '"';

  }

  // The opening tag of an effect: its element, its classes, its custom properties and the
  // attributes it adds - each written by the kind, already escaped.

  function padEffectOpen ( $kind, $element, $vars = [], $attrs = '', $classes = '' ) {

    return "<$element class=\"pad-effect pad-effect-$kind" . ( $classes !== '' ? " $classes" : '' ) . '"'
         . $attrs . padEffectVars ( $vars ) . '>';

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

    if ( padWidgetOnce ( "style:effect-$kind" ) and padFileExists ( PAD . "subs/effects/assets/$kind.css" ) )
      $css .= padProtect ( '<style' . padStyleNonce () . '>' . padEffectAsset ( "$kind.css" ) . '</style>' );

    return $css;

  }

  // A file of subs/effects/assets/: a style sheet, written a rule a line, sent as one line - the
  // line breaks and the indent after them taken out, as padWidgetAsset sends the widgets'
  // style; a script as it stands, without the line break that ends it.

  function padEffectAsset ( $file ) {

    static $memo = [];

    if ( isset ( $memo [$file] ) )
      return $memo [$file];

    $text = (string) padFileGet ( PAD . "subs/effects/assets/$file" );

    return $memo [$file] = str_ends_with ( $file, '.css' )
      ? preg_replace ( '/\R[ \t]*/', '', trim ( $text ) )
      : rtrim ( $text, "\r\n" );

  }

  // The effect on the page: its markup, then its style, the style in an svg of no size that
  // hides from a screen reader. An effect stands in a line - a heading, a paragraph - and
  // PHP's tidy (exits/tidy.php, on by default) ends a <p> or an <h2> at a <style> it meets
  // there, before it moves the style into the <head>: the rest of the line fell out of its
  // paragraph and onto a line of its own. A <style> in an inline <svg> is one the tidy takes
  // out without ending the paragraph, and without the tidy it is a style of the whole page as
  // any other is.

  function padEffectDone ( $kind, $html ) {

    $css = padEffectStyle ( $kind );

    if ( $css === '' )
      return $html;

    return $html . padProtect ( '<svg class="pad-effect-css" width="0" height="0" aria-hidden="true" focusable="false">' )
                 . $css . padProtect ( '</svg>' );

  }

  // The text a kind needs and was not given - the single tag without its value, or a pair
  // around nothing.

  function padEffectNeedsText ( $example ) {

    padEffectBad ( "needs its text - $example" );

    return '';

  }

?>
