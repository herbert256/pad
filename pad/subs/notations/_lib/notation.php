<?php

  // The notations subsystem: a text written in a notation, drawn on the server - a formula in
  // AsciiMath as MathML, a chemical equation, a tune in ABC as a staff, a guitar chord as its
  // box, a word in braille, morse, semaphore or signal flags, a chess, go or draughts position.
  //
  //   {notation 'math'}x = (-b +- sqrt(b^2-4ac))/(2a){/notation}
  //   {notation:chem 'CH4 + 2O2 -> CO2 + 2H2O'/}
  //   {chess 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'/}
  //
  // A notation is a file of subs/notations/types/ - <kind>.php reads the options of the kind,
  // draws the text ($padNotationText) and answers the markup - and the files there are the
  // notations there are (padNotationTypes). Its functions stand in _lib/<kind>.php, included
  // when it is first drawn (subs/notations/notation.php, the body every form runs); its style
  // in www/assets/notations/<kind>.css, written with the first drawing of the kind on a page, with the CSP
  // nonce (padNotationStyle). Nothing a notation draws needs a script: every kind is markup -
  // MathML, HTML, inline SVG - drawn the same for the same text, so a page answers the same
  // bytes every time.
  //
  // padNotationTypes   the notations: the files of subs/notations/types/
  // padNotationType    whether a name is one
  // padNotationWhat    the tag the notation is written as, for a message
  // padNotationInt     a whole number option held to its bounds
  // padNotationChoice  an option that is one of a few words
  // padNotationOption  a text option, read as the characters it holds
  // padNotationAttr    a text escaped for an attribute or an element's content
  // padNotationId      a deterministic id of a drawing on the page
  // padNotationStyle   the style of a kind, once per page, with its colours
  // padNotationScrub   a text as valid UTF-8, a byte that is none U+FFFD
  // padNotationNum     a number as an SVG coordinate: at most two decimals, no trailing zeros

  function padNotationTypes () {

    return padKinds ( 'notations' );

  }

  function padNotationType ( $name ) {

    return padKind ( 'notations', $name );

  }

  // The tag the notation is written as, for what a message says: {notation 'x'}, {notation:x},
  // {x}, or the built-in tag that draws a notation of its name, {chess}. Read from the level,
  // not from the body's variables: a notation drawn by a template that a pipe runs - {echo $t
  // | code/} - has those in the scope of that run.

  function padNotationWhat () {

    global $pad, $padType, $padPrefix, $padTag, $padOpt;

    if ( ( $padType [$pad] ?? '' ) == 'notation' )
      return '{' . ( ( $padPrefix [$pad] ?? '' ) !== '' ? 'notation:' : '' ) . ( $padTag [$pad] ?? '' ) . '}';

    if ( ( $padTag [$pad] ?? '' ) !== 'notation' )
      return '{' . ( $padTag [$pad] ?? '' ) . '}';

    $kind = $padOpt [$pad] [1] ?? '';

    return "{notation '" . ( is_string ( $kind ) ? strtolower ( trim ( $kind ) ) : '' ) . "'}";

  }

  // What an option was given as, for a message: the value cut short and made safe.

  function padNotationShow ( $value ) {

    return padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 30 );

  }

  // A whole number option, held to its bounds: one that is no whole number, or out of them,
  // is named under the strict check and takes the default. A number written with px, the
  // unit it is in - size='240px' - is that number.

  function padNotationInt ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = preg_replace ( '/\s*px$/i', '', trim ( padParmText ( $value ) ) );

    if ( ( is_int ( $value ) or ( is_string ( $value ) and preg_match ( '/^-?\d+$/D', $value ) ) )
         and (int) $value >= $min and (int) $value <= $max )
      return (int) $value;

    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has no $option '" . padNotationShow ( $value ) . "' - a whole number from $min to $max" );

    return $default;

  }

  // An option that is one of a few words, named under the strict check when it is another -
  // the default then. A bare option is TRUE, which is no word.

  function padNotationChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_string ( $value ) ? strtolower ( trim ( padParmText ( $value ) ) ) : ( is_int ( $value ) ? (string) $value : NULL );

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has no $option '" . padNotationShow ( $value ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // A text option - a title, a label - read as the characters it holds, so a field inside a
  // quoted option is the text of the field. A bare option is no text: the default.

  function padNotationOption ( $option, $default = '' ) {

    $value = padTagParm ( $option, $default );

    return ( is_string ( $value ) or is_int ( $value ) or is_float ( $value ) ) ? trim ( padParmText ( (string) $value ) ) : $default;

  }

  function padNotationAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The id of a drawing, made from what it draws (padWidgetId): two drawings on a page never
  // share one, and the same drawing twice gets a number behind the second.

  function padNotationId ( $kind, $seed ) {

    return padWidgetId ( "notation-$kind", $seed );

  }

  // The style of a kind: the rules of www/assets/notations/<kind>.css - linked, or written in
  // a rule a line sent as one line (padWidgetFile) - after its colours, custom properties
  // --pad-<class>-<role> with light-dark() defaults, once per page (padWidgetStyle,
  // lib/widget.php).

  function padNotationStyle ( $class, $roles, $kind = '' ) {

    $kind  = ( $kind !== '' ) ? $kind : $class;
    $rules = padWidgetFile ( "notations/$kind.css" );

    // A kind without colours of its own - the formulas take the colour of their text - has its
    // rules alone.

    if ( ! $roles )
      return padWidgetOnce ( "style:$class" ) ? padWidgetSheet ( $rules ) : '';

    return padWidgetStyle ( $class, $roles, $rules );

  }

  // A drawing as it goes into the page: in a <pad-notation>, a custom element - inline, no
  // role of its own - whose content the page's tidy pass (lib/tidy.php) leaves as it is.
  // Tidy indented MathML, a space and a line break inside every <mi> and <mo>, so the
  // browser drew x upright and the brackets of a matrix short; and it took a <style> out of
  // a paragraph into the head, closing the <p> in front of it, so an inline formula, a braille
  // word or a chord in a sentence stood after an empty paragraph; and it took every attribute
  // off the chess board's svg, its style kept inside it.

  function padNotationWrap ( $html ) {

    return ( $html === '' ) ? '' : "<pad-notation>$html</pad-notation>";

  }

  // A text as valid UTF-8: every byte that is none the replacement character U+FFFD.

  function padNotationScrub ( $text ) {

    if ( mb_check_encoding ( $text, 'UTF-8' ) )
      return $text;

    $keep = mb_substitute_character ();

    mb_substitute_character ( 0xFFFD );

    $text = mb_scrub ( $text, 'UTF-8' );

    mb_substitute_character ( $keep );

    return $text;

  }

  function padNotationNum ( $number ) {

    $text = number_format ( round ( (float) $number, 2 ), 2, '.', '' );
    $text = rtrim ( rtrim ( $text, '0' ), '.' );

    return ( $text === '-0' or $text === '' ) ? '0' : $text;

  }

?>
