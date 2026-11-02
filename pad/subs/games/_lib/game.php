<?php

  // The games subsystem: little games played in the browser.
  //
  //   {game:memory pairs=6/}
  //   {game:tictactoe first='computer'/}
  //   {game:sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8..79'/}
  //   {game:hangman}
  //     lighthouse
  //     harbour
  //   {/game:hangman}
  //
  // A game is a file of subs/games/types/ - <game>.php checks what the game is given and writes
  // its board - and the files there are the games there are (padGameTypes, lib/game.php);
  // www/assets/games/ holds its script, <game>.js, which plays it, and its style, <game>.css. subs/games/game.php is the body the {game:memory} tag and the
  // game: type both run; a game's file runs in padGameTag and answers padGame ( ... ), the
  // frame every game shares: its name, a status line its script writes to, the board, a bar
  // for its buttons, and its configuration as JSON for the script.
  //
  // The script of the games - www/assets/games/game.js, the runtime: a seeded random number,
  // the status, the buttons, the end of a game - and the script of each kind are written once
  // per page with the CSP nonce, as the interactive tags write theirs (lib/widget.php). A
  // game's script finds every board of its kind on the page, the ones a fragment or a live
  // region brings later as well. seed= makes the shuffles and the boards the same on every
  // load - a page can say which puzzle it gives; without it the browser picks.
  //
  // The other files of this directory are the code of games that need the server: the sudoku
  // solver, the crossword layout.

  foreach ( padDirList ( PAD . 'subs/games/_lib' ) ?: [] as $padGamePart )
    if ( $padGamePart [0] != '.' and str_ends_with ( $padGamePart, '.php' ) and $padGamePart != 'game.php' )
      include_once PAD . "subs/games/_lib/$padGamePart";

  // A game of one kind: its file in subs/games/types/ sets it up. The file runs in this function,
  // with the source - the content between the game's tags - at hand to padGameSource in
  // $padGameNow, put back afterwards for a game set up while another one is.

  function padGameTag ( $kind, $source = '' ) {

    global $padGameNow;

    $before     = $padGameNow ?? NULL;
    $padGameNow = [ 'kind' => $kind, 'source' => (string) $source ];

    $html = include PAD . "subs/games/types/$kind.php";

    $padGameNow = $before;

    return $html;

  }

  // The content between the game's tags, as written - its shared indent taken off.

  function padGameSource () {

    return padChartDedent ( (string) ( $GLOBALS ['padGameNow'] ['source'] ?? '' ) );

  }

  // The tag the game is written as, for what a message says: {game:x}, {game:x} or {data:x}.

  function padGameWhat () {

    global $pad, $padType, $padPrefix, $padGameNow;

    $kind = $padGameNow ['kind'] ?? '';

    if ( ( $padType [$pad] ?? '' ) == 'game' )
      return ( ( $padPrefix [$pad] ?? '' ) !== '' ? 'game:' : '' ) . $kind;

    return "game '$kind'";

  }

  // The game's first parameter as text, such as the grid in {game:sudoku '...'}.

  function padGameValue ( $default = '' ) {

    global $pad, $padType, $padOpt;

    $value = $padOpt [$pad] [ ( ( $padType [$pad] ?? '' ) == 'pad' ) ? 2 : 1 ] ?? $default;

    return is_string ( $value ) ? padParmText ( $value ) : $value;

  }

  // The rows a data= option names - a {pad:data} store, a sequence store, the page's array, a
  // field holding a list or a _data file, as {chart:bar} reads them - or NULL without data=.

  function padGameRows () {

    global $pad, $padPrm, $padData, $padDataStore, $pqStore, $padCheckSyntax;

    if ( ! isset ( $padPrm [$pad] ['data'] ) )
      return NULL;

    padTagParm ( 'data' );

    $name = $padPrm [$pad] ['data'];

    if ( is_string ( $name ) and padValidVar ( $name ) ) {

      if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
      if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

      if ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) )
        return $GLOBALS [$name];

      if ( padFieldCheck ( $name ) and is_array ( $field = padFieldValue ( $name ) ) )
        return $field;

      if ( $file = padDataFileName ( $name ) )
        return padDataFileData ( $file );

      if ( $padCheckSyntax and ! padStrHidden ( $name ) )
        padError ( "there is no data named '$name' for the " . padGameWhat () );

      return [];

    }

    return $padData [$pad];

  }

  // A list of texts for a game - words, questions, symbols: the rows of data= (a row that is
  // a list gives its first field, or the field named by $field), else the lines of the
  // content, else the comma list of the game's first value, else $default. Every item
  // trimmed, the empty ones left out.

  function padGameList ( $default = [], $field = '' ) {

    $rows = padGameRows ();

    if ( $rows !== NULL ) {
      $list = [];
      foreach ( (array) $rows as $row ) {
        if ( is_object ( $row ) )
          $row = padToArray ( $row );
        if ( is_array ( $row ) )
          $row = ( $field !== '' and array_key_exists ( $field, $row ) ) ? $row [$field] : reset ( $row );
        if ( is_scalar ( $row ) )
          $list [] = (string) $row;
      }
    } elseif ( trim ( padGameSource () ) !== '' )
      $list = preg_split ( '/\R/', padGameSource () );
    elseif ( is_string ( $value = padGameValue () ) and trim ( $value ) !== '' )
      $list = explode ( ',', $value );
    else
      $list = $default;

    return array_values ( array_filter ( array_map ( 'trim', $list ), fn ( $item ) => $item !== '' ) );

  }

  // Words for a word game - hangman, scramble, wordsearch: lower case, letters only, two to
  // $longest letters; one that is not is named under the strict check and left out, and so
  // is a second copy.

  function padGameWords ( $list, $longest = 18 ) {

    global $padCheckSyntax;

    $words = [];

    foreach ( $list as $word ) {
      $word = mb_strtolower ( trim ( $word ), 'UTF-8' );
      if ( preg_match ( '/^[a-z]{2,' . $longest . '}$/D', $word ) )
        $words [$word] = TRUE;
      elseif ( $padCheckSyntax )
        padError ( "the " . padGameWhat () . " has a word '" . padMakeSafe ( $word, 30 ) . "' - a word is 2 to $longest letters, a to z" );
    }

    return array_keys ( $words );

  }

  // A whole number option, held to its bounds: one that is no whole number, or out of them,
  // is named under the strict check and takes the default.

  function padGameInt ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = trim ( $value );

    if ( ( is_int ( $value ) or ( is_string ( $value ) and preg_match ( '/^-?\d+$/D', $value ) ) )
         and (int) $value >= $min and (int) $value <= $max )
      return (int) $value;

    if ( $padCheckSyntax )
      padError ( "the " . padGameWhat () . " has no $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 )
                 . "' - a whole number from $min to $max" );

    return $default;

  }

  // An option that is one of a few words, named under the strict check when it is another.

  function padGameChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_string ( $value ) ? strtolower ( trim ( padParmText ( $value ) ) ) : $value;

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the " . padGameWhat () . " has no $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 )
                 . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // A bare option given or not - {game:tictactoe two/}.

  function padGameFlag ( $option ) {

    $value = padTagParm ( $option, FALSE );

    return ( $value !== FALSE and $value !== '' and $value !== 0 and $value !== '0' );

  }

  // The game on the page: the frame, the configuration for the script, the style and the
  // script once per page. $board is markup the server wrote - a crossword's squares, a
  // sudoku's givens - which the script plays on; most games draw their board themselves.
  // title= names the game, else its own name does; seed= is handed to the script.

  function padGame ( $kind, $name, $config = [], $board = '' ) {

    $title = (string) padParmText ( padTagParm ( 'title', '' ) );
    $label = ( $title !== '' ) ? $title : $name;

    if ( padTagParm ( 'seed', NULL ) !== NULL )
      $config ['seed'] = padGameInt ( 'seed', 1, 0, 2147483647 );

    $config ['name'] = $label;

    $id   = padWidgetId ( "game-$kind", [ $config, $board ] );
    $json = padWidgetAttr ( json_encode ( $config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ) );
    $show = padWidgetAttr ( $label );

    return padGameStyle ( $kind )
         . padProtect ( "<section class=\"pad-game pad-game-$kind\" id=\"$id\" data-pad-game=\"$kind\" data-config=\"$json\" aria-label=\"$show\">"
                      . "<header class=\"pg-head\"><strong class=\"pg-title\">$show</strong>"
                      . '<span class="pg-status" role="status" aria-live="polite"></span></header>'
                      . "<div class=\"pg-board\">$board</div>"
                      . '<div class="pg-bar"></div>'
                      . '<noscript><p class="pg-noscript">This game is played in the browser and needs JavaScript.</p></noscript>'
                      . '</section>' )
         . padWidgetScript ( 'game', padWidgetFile ( 'games/game.js', 'raw' ) )
         . padWidgetScript ( "game-$kind", padWidgetFile ( "games/$kind.js", 'raw' ) );

  }

  // The look of the games - www/assets/games/game.css, with the colours every game draws in
  // as custom properties with light and dark defaults - and the look of the kind, www/assets/games/<game>.css,
  // once per page each.

  function padGameStyle ( $kind ) {

    $roles = [ 'ink'     => [ '#1a1a19', '#f4f3ee' ], 'muted'   => [ '#6b6a65', '#a8a79f' ],
               'line'    => [ '#c9c8c2', '#4a4a46' ], 'surface' => [ '#fcfcfb', '#1a1a19' ],
               'cell'    => [ '#ffffff', '#242422' ], 'shade'   => [ '#f0efea', '#2c2c29' ],
               'accent'  => [ '#2a78d6', '#5598e7' ], 'accent2' => [ '#eb6834', '#e8875f' ],
               'good'    => [ '#1a8a5a', '#3fbf86' ], 'bad'     => [ '#d43c3b', '#ef6b6a' ],
               'mark'    => [ '#fff1b8', '#4a4320' ] ];

    $css = padWidgetStyle ( 'game', $roles, padWidgetFile ( 'games/game.css', 'raw' ) );

    if ( padWidgetOnce ( "style:game-$kind" ) )
      $css .= padWidgetSheet ( padWidgetFile ( "games/$kind.css", 'raw' ) );

    return $css;

  }

?>
