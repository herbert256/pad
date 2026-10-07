<?php

  // HTML/XML cleanup through ext-tidy, plus header and output-buffer handling. Everything
  // tidy-related degrades quietly: without the extension the data is returned untouched.
  //
  // padFileXmlTidy  reformats an XML file in place, and only writes it back if tidy
  //                 actually produced something
  // padTidy         the general pass over page output using $padTidyConfig; for a
  //                 fragment or an included page it emits the body only
  // padTidyHold     holds the content of a <textarea>, a <template> or a custom element
  //                 out of that pass
  // padTidyPairs    every open tag of one name paired with its close, in one pass
  // padTidySmall    an aggressive minifier - drops comments and empty elements, then
  //                 strips newlines, runs of spaces and whitespace between tags
  //
  // padHeader       sends a header and records it in $padHeaders for the dump, ignoring
  //                 the call once headers have gone out
  // padEmptyBuffers collects and closes every open output buffer into $output
  // padCheckBuffers the same, but treats anything buffered as an error - used where the
  //                 engine must own the output, e.g. before a redirect or a download

  function padFileXmlTidy ( $file ) {

    $options = [
      'input-xml'           => true,
      'output-xml'          => true,
      'force-output'        => true,
      'add-xml-decl'        => false,
      'indent'              => true,
      'tab-size'            => 2,
      'indent-spaces'       => 2,
      'vertical-space'      => 'no',
      'wrap'                => 0,
      'clean'               => 'yes',
      'drop-empty-elements' => 'yes'
    ];

    $data = padFileGet ( $file );

    if ( ! class_exists('tidy') )
      return;

    try {
      $tidy = new tidy;
      $tidy->parseString ( $data, $options, 'utf8' );
      $tidy->cleanRepair();
      $value = $tidy->value ?? '';
    } catch (Throwable $e) {
      return;
    }

    if ( $value and strlen($value) > 10 )
      padFilePut ( $file, $value, 0 );

  }


  function padTidy ( $data, $fragment=FALSE ) {

    global $padInclude, $padTidyCcsid, $padTidyConfig;

    if ( ! class_exists ( 'tidy' ) )
      return $data;

    $config = $padTidyConfig;

    if ( $fragment or $padInclude ) {
      $data = trim ( $data );
      $config ['show-body-only'] = true;
    }

    // What Tidy must not rewrite is held out of the pass and put back after it: the content
    // of a <textarea> is a form value, and Tidy dropped its leading whitespace - "  two" came
    // back as "two", so a refilled form posted something else than it showed - the content
    // of a <template> is markup for a script, into which Tidy put a <ul> round a bare <li>,
    // and a custom element's is its component's (padTidyHold). Each is replaced by a mark of
    // this request's own, no page text.

    $held = [];
    $mark = 'padTidyHeld' . bin2hex ( random_bytes ( 8 ) ) . 'x';
    $data = padTidyHold ( $data, $held, $mark );

    try {
      $tidy = new tidy;
      $tidy->parseString($data, $config, $padTidyCcsid );
      $tidy->cleanRepair();
      $value = $tidy->value ?? $data;
    } catch (Throwable $e) {
      $value = $data;
    }

    return preg_replace_callback ( '#\s*' . $mark . '(\d+)x\s*#', fn ( $match ) => $held [ $match [1] ], $value ) ?? $value;

  }

  // The content of each element padTidy holds out of the pass, replaced by the mark and its
  // number. A <textarea> holds text and ends at its own close; a <template> is matched by
  // depth - held up to the first </template>, a template inside a template left the outer
  // one's </ul></template> to Tidy, which dropped them, and the rest of the page went into
  // the template. So is a custom element, a name with a - in it (<my-app>, <x-icon>): Tidy
  // keeps the element itself (custom-tags, config/tidy.php) but took one holding a <div> and
  // a <p> apart into three copies of itself, one round each part. An element that never
  // closes is left to Tidy.

  function padTidyHold ( $data, &$held, $mark ) {

    $out   = '';
    $at    = 0;
    $pairs = [];

    while ( preg_match ( '#<(textarea|template|[a-z][a-z0-9]*-[a-z0-9._-]*)(?=[\s/>])[^>]*>#i', $data, $open, PREG_OFFSET_CAPTURE, $at ) ) {

      $name = strtolower ( $open [1] [0] );
      $from = $open [0] [1] + strlen ( $open [0] [0] );

      $out .= substr ( $data, $at, $from - $at );
      $at   = $from;

      // A tag written self-closing - <x-icon name="a"/> - holds nothing.

      if ( str_ends_with ( $open [0] [0], '/>' ) )
        continue;

      $pairs [$name] ??= padTidyPairs ( $data, $name );

      $close = $pairs [$name] [$from] ?? NULL;

      if ( $close === NULL )
        continue;

      $held [] = substr ( $data, $from, $close - $from );
      $out    .= $mark . ( count ( $held ) - 1 ) . 'x';
      $at      = $close;

    }

    return $out . substr ( $data, $at );

  }

  // Every open tag of one name paired with its close, in one pass over the page: the end of
  // the open tag => the start of its close. A <textarea> closes at the next </textarea>;
  // anything else at the close of its own depth, a tag written self-closing counting for
  // none. An open tag left out never closes. Each open tag searched the rest of the page
  // for its own close before, so a page of self-closing or never-closed custom elements -
  // <x-icon name="a"/> in every row - took a time growing with its square: 8000 of them,
  // seconds.

  function padTidyPairs ( $data, $name ) {

    $pairs = [];
    $stack = [];

    preg_match_all ( '#<(/?)' . preg_quote ( $name, '#' ) . '(?=[\s/>])[^>]*>#i', $data, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

    foreach ( $tags as $tag ) {

      if ( $tag [1] [0] == '/' ) {

        if ( $name == 'textarea' ) {
          foreach ( $stack as $one )
            $pairs [$one] = $tag [0] [1];
          $stack = [];
        } elseif ( $stack )
          $pairs [ array_pop ( $stack ) ] = $tag [0] [1];

      } elseif ( ! str_ends_with ( $tag [0] [0], '/>' ) )

        $stack [] = $tag [0] [1] + strlen ( $tag [0] [0] );

    }

    return $pairs;

  }

  function padTidySmall ( $data ) {

    global $padTidyCcsid;

    $config = [
      'indent'          => false,     // Disable indentation
      'wrap'            => 0,         // Prevent wrapping lines at a certain length
      'vertical-space'  => false,     // Remove extra empty lines
      'hide-comments'   => true,      // Strip HTML comments
      'tidy-mark'       => false,     // Remove the Tidy meta tag
      'drop-empty-paras'=> true,      // Remove empty <p> tags
      'join-classes'    => true,      // Merge consecutive classes
      'join-styles'     => true,      // Merge consecutive styles
      'show-body-only'  => true,
      'merge-spans'     => 'yes',
      'force-output'    => true,
      'show-warnings'   => FALSE,
      'omit-optional-tags'  => 'yes',
      'merge-divs'      => 'yes',
      'indent-spaces' => 0,
      'drop-empty-elements' => true,
      'drop-proprietary-attributes' => true,
      'new-blocklevel-tags' => '',
      'new-empty-tags' => '',
      'new-inline-tags' => ''
    ];

    $tidy = new tidy;
    $tidy->parseString($data, $config, $padTidyCcsid );
    $tidy->cleanRepair();

    $result = $tidy->value ?? $data;

    $result = str_replace  ( ["\r", "\n", "\t"], '', $result );
    $result = preg_replace ( '/ {2,}/', ' ',         $result );
    $result = preg_replace ( '/>\s+</', '><', $result );

    return $result;

  }


  function padHeader ($header) {

    global $padHeaders;

    if ( headers_sent () )
      return;

    header ($header);

    $padHeaders [] = $header;

  }

  function padEmptyBuffers ( &$output ) {

    $output = '';

    set_error_handler ( 'padErrorThrow' );

    try {

      $j = ob_get_level ();

      for ( $i = 1; $i <= $j; $i++ )
        $output = ob_get_clean () . $output;

    } catch (Throwable $ignored) {

    }

    restore_error_handler ();

  }

  function padCheckBuffers () {

    padEmptyBuffers ( $output );

    if ( trim ( $output ) )
      return padError ( "Illegal output: '$output'" );

  }

?>
