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
  // padTidyClose    where such an element closes, a template or a custom one by its depth
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

    $out = '';
    $at  = 0;

    while ( preg_match ( '#<(textarea|template|[a-z][a-z0-9]*-[a-z0-9._-]*)(?=[\s/>])[^>]*>#i', $data, $open, PREG_OFFSET_CAPTURE, $at ) ) {

      $from  = $open [0] [1] + strlen ( $open [0] [0] );
      $close = padTidyClose ( $data, strtolower ( $open [1] [0] ), $from );

      $out .= substr ( $data, $at, $from - $at );
      $at   = $from;

      if ( $close === NULL )
        continue;

      $held [] = substr ( $data, $from, $close - $from );
      $out    .= $mark . ( count ( $held ) - 1 ) . 'x';
      $at      = $close;

    }

    return $out . substr ( $data, $at );

  }

  // Where the element opened before $from closes: the next close of a <textarea>, the close
  // at depth 0 of anything else, or NULL.

  function padTidyClose ( $data, $name, $from ) {

    $depth = 1;
    $tags  = '#<(/?)' . preg_quote ( $name, '#' ) . '(?=[\s/>])[^>]*>#i';

    while ( preg_match ( $tags, $data, $tag, PREG_OFFSET_CAPTURE, $from ) ) {

      if ( $tag [1] [0] == '/' ) {
        if ( --$depth == 0 )
          return $tag [0] [1];
      } elseif ( $name != 'textarea' and ! str_ends_with ( $tag [0] [0], '/>' ) )
        $depth++;

      $from = $tag [0] [1] + strlen ( $tag [0] [0] );

    }

    return NULL;

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
