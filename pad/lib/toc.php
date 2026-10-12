<?php

  // A table of contents - the {toc} tag. It lists the headings of the page that holds it,
  // as they came out of the rendering, so a heading a loop or a snippet wrote is in it too:
  //
  //   {toc}                                   the <h2> and <h3> of the page, nested
  //   {toc levels='2,3,4', title='On this page', list='ul'}
  //
  // The page renders from top to bottom, and the headings below a {toc} are not there yet
  // when it runs. It therefore prints a marker, as {stack} does (lib/stack.php), and the
  // marker is filled in once the page has rendered: padTocFill, from exits/exits.php for
  // the page of the request, and from start/pad/pad.php for a page a {page} renders, which
  // gets the contents of its own headings. The marker carries its options in itself -
  // hexadecimal, so no character of it is syntax - and makes the same text on every
  // request: a {toc} inside a cached fragment still finds its page.
  //
  // A heading without an id gets one, the slug of its text, made unique on the page with
  // -2, -3 behind it; a heading with an id keeps it. The list nests one level deeper per
  // heading level, a missing level in between makes no empty item. Each entry is a link to
  // its heading; the nav is labelled by its title, which is a paragraph and not a heading,
  // so a second {toc} does not list it. No heading, and the tag answers nothing.
  //
  // The colours are CSS custom properties with light and dark defaults on .pad-toc -
  // --pad-toc-text, --pad-toc-line and --pad-toc-accent - which a page overrides.

  // The marker is sealed (padMarkSeal), and only a sealed one is filled: a value carrying the
  // marker's characters - a search page's "Results for {$q}" - failed any page with a 500,
  // or planted a table of contents in it.

  function padTocMarker ( $levels, $title, $list ) {

    // A broken byte in the title is a stand-in character, as {$t} shows it, where the JSON
    // of the options was nothing and the request ended - later the seal's digits as text.

    $hex = bin2hex ( json_encode ( [ $levels, $title, $list ], JSON_INVALID_UTF8_SUBSTITUTE ) );

    return "\u{E0F6}" . padMarkSeal ( $hex ) . $hex . "\u{E0F7}";

  }

  // The options of a marker the {toc} tag wrote - levels, title, list - or NULL for any
  // other text between the marker's characters.

  function padTocOptions ( $seal, $hex ) {

    if ( ! hash_equals ( padMarkSeal ( $hex ), $seal ) or strlen ( $hex ) % 2 )
      return NULL;

    $options = json_decode ( (string) hex2bin ( $hex ), TRUE );

    return ( is_array ( $options ) and count ( $options ) == 3 ) ? array_values ( $options ) : NULL;

  }

  // The heading levels of a levels= list, '2,3' or '2-4', in order.

  function padTocLevels ( $text ) {

    $levels = [];

    foreach ( explode ( ',', (string) $text ) as $part ) {

      $part = trim ( $part );

      if ( preg_match ( '/^([1-6])\s*-\s*([1-6])$/', $part, $match ) )
        $levels = array_merge ( $levels, range ( min ( $match [1], $match [2] ), max ( $match [1], $match [2] ) ) );
      elseif ( preg_match ( '/^[1-6]$/', $part ) )
        $levels [] = (int) $part;
      elseif ( $part !== '' )
        return [];

    }

    $levels = array_values ( array_unique ( array_map ( 'intval', $levels ) ) );

    sort ( $levels );

    return $levels;

  }

  function padTocFill ( $output ) {

    if ( ! is_string ( $output ) or ! str_contains ( $output, "\u{E0F6}" ) )
      return $output;

    // The ids the page has already, so a made one never takes one of them - read as the
    // browser reads them, entities and all: id="intro&#45;2" is intro-2.

    $taken    = padTocIds ( $output );
    $headings = [];

    // The number a repeated heading's id goes on from: each counted up from -2 through every
    // id its text had taken already, and a long listing of cards with the same heading took
    // seconds.

    $next = [];

    // Every heading of the levels any {toc} of the page asks for gets its id now, once.

    $wanted = [];

    preg_match_all ( '/\x{E0F6}(\d{16})([0-9a-f]*)\x{E0F7}/u', $output, $markers, PREG_SET_ORDER );

    foreach ( $markers as $marker )
      foreach ( (array) ( padTocOptions ( $marker [1], $marker [2] ) [0] ?? [] ) as $level )
        $wanted [ (int) $level ] = TRUE;

    if ( ! $wanted )
      return $output;

    // A heading reaches to its closer, never past another heading's tag: one left unclosed
    // - <h3>Orders<h3> - scanned on to the end of the page, and past a megabyte the scan
    // gave up and the whole page was gone. A failing pass leaves the page as it was.
    //
    // A comment, and an element whose content is no part of the page - a script, a style, a
    // textarea, a template - is passed over up to its closer (one left open, to the end of
    // the page): a script's "<h2>" + title + "</h2>" got an id="title" that broke the
    // script, and a commented-out heading was listed, linked to an anchor that is not there.

    $before = $output;
    $output = preg_replace_callback ( '/<h([1-6])(\s[^>]*)?>((?:(?!<\/?h[1-6][\s>]).)*+)<\/h\1\s*>'
                                    . '|<!--(?:[^-]++|-(?!->))*+(?:-->)?(*SKIP)(*FAIL)'
                                    . '|<(script|style|textarea|template|title|xmp|iframe|noembed|noframes)(?=[\s\/>])'
                                    .   '(?:[^<]++|<(?!\/\4[\s\/>]))*+(*SKIP)(*FAIL)/is',
      function ( $match ) use ( &$taken, &$headings, &$next, $wanted ) {

        $level = (int) $match [1];
        $attrs = $match [2] ?? '';
        $inner = $match [3];

        if ( ! isset ( $wanted [$level] ) )
          return $match [0];

        $text = trim ( preg_replace ( '/\s+/u', ' ', strip_tags ( $inner ) ) );

        if ( $text === '' )
          return $match [0];

        // The link's href is escaped from the id itself: one written with an entity -
        // id="a&amp;b" - was escaped a second time and led to a target that is not there.

        if ( ( $own = padTocIdOf ( $attrs ) ) !== NULL and $own !== '' )
          $id = padTocId ( $own );
        else {
          $base = padStrSlug ( html_entity_decode ( padUnprotect ( padUnescape ( $text ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
          $base = ( $base === '' ) ? 'section' : $base;
          $id   = $base;
          for ( $n = $next [$base] ?? 2; isset ( $taken [$id] ); $n++ )
            $id = "$base-$n";
          $next  [$base] = $n;
          $taken [$id]   = TRUE;
          $attrs = ' id="' . $id . '"' . $attrs;
        }

        $headings [] = [ $level, $id, $text ];

        return "<h$level$attrs>$inner</h$level>";

      }, $output ) ?? $before;

    return preg_replace_callback ( '/\x{E0F6}(\d{16})([0-9a-f]*)\x{E0F7}/u', function ( $match ) use ( $headings ) {

      $options = padTocOptions ( $match [1], $match [2] );

      if ( $options === NULL )
        return $match [0];

      list ( $levels, $title, $list ) = $options;

      $mine = array_values ( array_filter ( $headings, fn ( $one ) => in_array ( $one [0], (array) $levels ) ) );

      return $mine ? padTocHtml ( $mine, (string) $title, $list == 'ul' ? 'ul' : 'ol' ) : '';

    }, $output ) ?? $output;

  }

  // The ids of the page: the id attribute of every start tag, a quoted value passed over
  // whole. Searched for anywhere, id=intro in a paragraph's text took the made id of an
  // Intro heading - intro-2, every anchor of the page one up - and the "id = 5" of a
  // heading's x-on:click handler was taken for the heading's id, linked as #5.

  function padTocIds ( $html ) {

    preg_match_all ( '/<[a-z][^\s\/<>]*+([^"\'<>]*+(?:(?:"[^"]*+"|\'[^\']*+\')[^"\'<>]*+)*+)>/i', $html, $tags );

    $ids = [];

    foreach ( $tags [1] as $attrs )
      if ( ( $id = padTocIdOf ( $attrs ) ) !== NULL )
        $ids [ padTocId ( $id ) ] = TRUE;

    return $ids;

  }

  // The value of the id attribute in a tag's attributes, quoted or not - id=intro is an id
  // as id="intro" is - or NULL when it has none.

  function padTocIdOf ( $attrs ) {

    preg_match_all ( '/([^\s"\'<>\/=]++)(?:\s*+=\s*+(?:"([^"]*+)"|\'([^\']*+)\'|([^\s"\'<>]++)))?/', $attrs, $all, PREG_SET_ORDER );

    foreach ( $all as $one )
      if ( strtolower ( $one [1] ) === 'id' )
        return ( $one [2] ?? '' ) . ( $one [3] ?? '' ) . ( $one [4] ?? '' );

    return NULL;

  }

  // An id attribute's value as the browser reads it.

  function padTocId ( $value ) {

    return html_entity_decode ( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

  }

  // The headings as nested lists: a deeper heading opens a list inside the item before it,
  // a higher one closes lists until its own depth. The depth is the place of the level in
  // the levels the page has, and never more than one step in from the heading before it.

  function padTocHtml ( $headings, $title, $list ) {

    static $count = 0;

    $count++;

    $id     = "pad-toc-$count";
    $levels = array_values ( array_unique ( array_column ( $headings, 0 ) ) );

    sort ( $levels );

    $html  = padTocStyle () . '<nav class="pad-toc"' . ( $title !== '' ? " aria-labelledby=\"$id\">" : ' aria-label="Contents">' );
    $html .= ( $title !== '' ) ? "<p class=\"pad-toc-title\" id=\"$id\">" . padChartAttr ( $title ) . '</p>' : '';
    $depth = 0;

    foreach ( $headings as list ( $level, $anchor, $text ) ) {

      $want = min ( array_search ( $level, $levels ) + 1, $depth + 1 );

      if ( $depth == 0 ) {
        $html .= "<$list>";
        $depth = 1;
      } elseif ( $want > $depth ) {
        for ( ; $depth < $want; $depth++ )
          $html .= "<$list>";
      } else {
        $html .= '</li>';
        for ( ; $depth > max ( 1, $want ); $depth-- )
          $html .= "</$list></li>";
      }

      $html .= '<li><a href="#' . padChartAttr ( $anchor ) . "\">$text</a>";

    }

    for ( ; $depth > 0; $depth-- )
      $html .= "</li></$list>";

    return $html . '</nav>';

  }

  // Once per request: the rules hold for every {toc} after the first.

  function padTocStyle () {

    if ( ! padWidgetOnce ( 'style:toc' ) )
      return '';

    $roles = [ 'text'   => [ '#3d3c39', '#d6d5cd' ],
               'line'   => [ '#e4e3df', '#3a3a37' ],
               'accent' => [ '#2a78d6', '#5598e7' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-toc-$role:$day;";
      $both  .= "--pad-toc-$role:light-dark($day,$night);";
    }

    return '<style' . padStyleNonce () . '>'
         . ":where(.pad-toc){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-toc){{$both}}}"
         . '.pad-toc{border-left:3px solid var(--pad-toc-line);padding:2px 0 2px 14px;color:var(--pad-toc-text);line-height:1.45}'
         . '.pad-toc-title{margin:0 0 6px;font-weight:600;font-size:.8em;letter-spacing:.06em;text-transform:uppercase}'
         . '.pad-toc ol,.pad-toc ul{margin:0;padding-left:1.4em}'
         . '.pad-toc>ol,.pad-toc>ul{padding-left:1.1em}'
         . '.pad-toc li{margin:2px 0}'
         . '.pad-toc a{color:inherit;text-decoration:none}'
         . '.pad-toc a:hover,.pad-toc a:focus-visible{color:var(--pad-toc-accent);text-decoration:underline}'
         . '</style>';

  }

?>
