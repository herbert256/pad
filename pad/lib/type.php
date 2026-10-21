<?php

  // Type detection: given the name inside a {tag}, work out what kind of thing it is.
  // level/type.php calls into here for every tag, and the answer selects the handler
  // types/<type>.php that will run it.
  //
  // padTypeTag       the plain name case, resolved in priority order: application _tags/,
  //                  the _common application (unless $padCommon is off - the common: prefix
  //                  stays available), a built-in pad tag, then the shared cases below, and
  //                  finally a pipe function used as a tag
  // padTypeFunction  the mirror image for a name in a pipe: _functions/, built-in
  //                  function, shared cases, then a tag used as a function. The $goTag /
  //                  $goFunction flags stop the two from recursing into each other
  // padTypeCommon    everything both can be: a stored sequence, bool, content block,
  //                  {define}, {macro}, select, data block, _include/ snippet, tag
  //                  property, field, array, parameter, level variable, PHP constant,
  //                  _data/ file, _scripts/ script, PHP function, sequence type, sequence
  //                  action - in that order; a field or array that is only a request
  //                  value comes after all of them (padTypeAsked)
  // padTypeTagCheck  used when the template gave an explicit prefix (app:, pad:, php: ...)
  //                  - it verifies that one type instead of searching, so the prefix both
  //                  disambiguates and asserts
  // padTypeSeq       resolves prefixed sequence forms, where prefix and name may appear
  //                  either way round (make:fibonacci as well as fibonacci:make)
  // padPhpAllowed    whether a template may call the PHP function of that name, by
  //                  $padPhpFunctions. A bare name resolves to PHP only when it may - one
  //                  left out falls through to a sequence of the same name, as range or
  //                  sort - while an explicit php: still resolves, and is refused with
  //                  the reason where it is called

  function padPhpAllowed ( $name ) {

    global $padPhpFunctions;

    // An explicit php: tag can carry its call along - {php:hypot(1)} - and resolves on the
    // name in front of the (, as padTypeTagCheck does.

    $name = strstr ( "$name(", '(', TRUE );

    if ( ! function_exists ( $name ) )
      return FALSE;

    if ( $padPhpFunctions === TRUE )
      return TRUE;

    if ( ! is_array ( $padPhpFunctions ) )
      return FALSE;

    // PHP's own function names do not care about case, so neither does the list.

    return in_array ( strtolower ( $name ), array_map ( 'strtolower', $padPhpFunctions ), TRUE );

  }

  // Whether a name is only a request value: a variable a GET, POST or cookie value made
  // (padGetParms keeps the note) that the page has not set since, and no row or {set} of an
  // enclosing level holds - or, with no variable of the name, a value the field search finds
  // in $_GET or $_POST. The visitor chooses the names: ?prime=<script>... made {prime} on the
  // sequence application's prime page the field tag of that value, written out as it came,
  // ?navigate=x made the demo's menu, read from _data/navigate.json, iterate the text x, and
  // ?strrev=X made the pipe | strrev answer X. Such a name gives way to every kind the
  // template can mean by it, and is the field only when nothing else is there.
  //
  // A variable the application's PHP names is the page's own, whatever it holds: $date =
  // $date ?? '2000-01-01' with ?date=2026-01-01 keeps the request's value, and an assignment
  // of the same value cannot be told from none by the value - {date} then printed the date
  // pipe's answer, or the asked date, as the visitor sent it or not (padTypePageNamed).

  function padTypeAsked ( $item ) {

    global $pad, $padCurrent, $padSetOcc, $padSetLvl, $padRequestGot;

    if ( isset ( $GLOBALS [$item] ) ) {
      if ( ! isset ( $padRequestGot [$item] ) or $GLOBALS [$item] !== $padRequestGot [$item] )
        return FALSE;
    } elseif ( ! isset ( $_GET [$item] ) and ! isset ( $_POST [$item] ) )
      return FALSE;
    elseif ( array_key_exists ( $item, $GLOBALS ) )
      return FALSE;

    for ( $i = $pad; $i > 0; $i-- )
      if ( array_key_exists ( $item, $padCurrent [$i] ?? [] )
           or isset ( $padSetOcc [$i] [$item] ) or isset ( $padSetLvl [$i] [$item] ) )
        return FALSE;

    return ! padTypePageNamed ( $item );

  }

  // Whether the PHP this request ran in the page's own scope - the configuration, the guards,
  // _inits.php, the page, _exits.php - names the variable: $name, or $GLOBALS ['name']. The
  // files of the _ directories - _lib, _tags, _functions, _callbacks ... - hold functions and
  // tags, whose $date is a local of their own. The names are gathered again when more files
  // were included since - which the first use of every kind of tag does - each file's read
  // from its tokens once: read again for every gathering, a page of 2000 lines with a bare
  // tag of a request value's name ran the tokenizer 30 times over it.

  function padTypePageNamed ( $item ) {

    static $names = [], $seen = 0, $root = NULL, $byFile = [];

    $files = get_included_files ();

    if ( count ( $files ) != $seen ) {

      $root ??= rtrim ( realpath ( APPS ) ?: APPS, '/' ) . '/';
      $seen   = count ( $files );
      $names  = [];

      foreach ( $files as $file )
        if ( str_starts_with ( $file, $root )
             and ! preg_match ( '#(^|/)_(?!config/|common/)[^/]*/#', substr ( $file, strlen ( $root ) ) ) )
          $names += $byFile [$file] ??= padTypePageVars ( file_get_contents ( $file ) );

    }

    return isset ( $names [$item] );

  }

  // The variables a file of PHP names in its own scope, read from its tokens: a word in a
  // comment or a string is none, nor a parameter or a local of a function, a closure or a
  // class - only a global statement there names one. Searched in the text, $upper in a
  // comment made the page's own a name it never set, and ?upper=<script>... took {upper}'s
  // place again, written out raw.

  function padTypePageVars ( $code ) {

    $names  = [];
    $depth  = 0;
    $bodies = [];
    $wait   = FALSE;
    $params = 0;
    $string = FALSE;
    $global = FALSE;
    $use    = FALSE;
    $before = NULL;
    $tokens = token_get_all ( (string) $code );

    foreach ( $tokens as $i => $token ) {

      $id = is_array ( $token ) ? $token [0] : $token;

      if ( in_array ( $id, [ T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ], TRUE ) )
        continue;

      $last   = $before;
      $before = $id;

      // A string with variables in it: what stands inside is text, its {$x} braces too.

      if ( $id === '"' or $id === '`' ) { $string = ! $string; continue; }
      if ( $id === T_START_HEREDOC )    { $string = TRUE;      continue; }
      if ( $id === T_END_HEREDOC )      { $string = FALSE;     continue; }

      if ( $id === '{' or $id === T_CURLY_OPEN or $id === T_DOLLAR_OPEN_CURLY_BRACES ) {
        $depth++;
        if ( $wait ) {
          $bodies [] = $depth;
          $wait      = FALSE;
        }
        continue;
      }

      if ( $id === '}' ) {
        if ( $bodies and end ( $bodies ) == $depth )
          array_pop ( $bodies );
        $depth--;
        continue;
      }

      // A variable read in a string of the page's own scope is the page's: "Orders of $date",
      // {$date} and ${date} in it.

      if ( $string ) {
        if ( ! $bodies and $id === T_VARIABLE )      $names [ substr ( $token [1], 1 ) ] = TRUE;
        if ( ! $bodies and $id === T_STRING_VARNAME ) $names [ $token [1] ]              = TRUE;
        continue;
      }

      // A function, a closure, a class: its parameters and its body are its own. An arrow
      // function's parameters are its own, its expression reads the page's.

      if ( $id === T_FUNCTION
           or ( in_array ( $id, [ T_CLASS, T_INTERFACE, T_TRAIT ], TRUE ) and $last !== T_DOUBLE_COLON )
           or ( defined ( 'T_ENUM' ) and $id === T_ENUM ) ) {
        $wait = TRUE;
        continue;
      }

      if ( $wait and $id === ';' ) {
        $wait = FALSE;
        continue;
      }

      // A closure of the page's scope takes the variables of its use ( ) from there.

      if ( $wait and ! $bodies and $id === T_USE ) {
        $use = TRUE;
        continue;
      }

      if ( $use ) {
        if ( $id === ')' )        $use = FALSE;
        if ( $id === T_VARIABLE ) $names [ substr ( $token [1], 1 ) ] = TRUE;
        continue;
      }

      if ( $id === T_FN ) {
        $params = -1;
        continue;
      }

      if ( $params ) {
        if ( $id === '(' ) $params = ( $params < 0 ) ? 1 : $params + 1;
        if ( $id === ')' ) $params--;
        continue;
      }

      if ( $id === T_GLOBAL ) { $global = TRUE;  continue; }
      if ( $id === ';' )      { $global = FALSE; continue; }

      if ( $id !== T_VARIABLE )
        continue;

      // $GLOBALS ['name'] is the page's variable wherever it stands, in a function too.

      $name = substr ( $token [1], 1 );

      if ( $name === 'GLOBALS' ) {
        $next = array_values ( array_filter ( array_slice ( $tokens, $i + 1, 6 ),
          fn ( $one ) => ! is_array ( $one ) or ! in_array ( $one [0], [ T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ], TRUE ) ) );
        if ( ( $next [0] ?? '' ) === '[' and is_array ( $next [1] ?? NULL ) and $next [1] [0] === T_CONSTANT_ENCAPSED_STRING )
          $names [ substr ( $next [1] [1], 1, -1 ) ] = TRUE;
        continue;
      }

      if ( $wait or ( $bodies and ! $global ) )
        continue;

      $names [$name] = TRUE;

    }

    return $names;

  }

  // The field or list a name is when it is only a request value - the last of the kinds,
  // asked by padTypeTag and padTypeFunction once nothing else answered (padTypeAsked).

  function padTypeAskedKind ( $item ) {

    if ( ! padTypeAsked ( $item ) ) return FALSE;

    if     ( padFieldCheck ( $item ) ) return 'field';
    elseif ( padArrayCheck ( $item ) ) return 'array';

    return FALSE;

  }

  function padTypeCommon ( $item ) {

    global $padBoolStore, $padContentStore, $padDataStore, $padDefineStore, $padMacroStore, $padSelect, $pqStore;

    if     ( isset              ( $pqStore         [$item]       ) ) return 'pull';
    elseif ( isset              ( $padBoolStore    [$item]       ) ) return 'bool';
    elseif ( isset              ( $padContentStore [$item]       ) ) return 'content';
    elseif ( isset              ( $padDefineStore  [$item]       ) ) return 'define';
    elseif ( isset              ( $padMacroStore   [$item]       ) ) return 'macro';
    elseif ( isset              ( $padSelect       [$item]       ) ) return 'select';
    elseif ( isset              ( $padDataStore    [$item]       ) ) return 'data';
    elseif ( padAppIncludeCheck ( $item                          ) ) return 'include';
    elseif ( padTagCheck        ( $item                          ) ) return 'property';

    // A page's own array variable is the data of {name}, where a request value of the same
    // name must not be: a page sets $orders = db(...) and ?orders=evil made {orders} iterate
    // the scalar 'evil' - the field check finds the request value in $_GET before the array
    // check reaches the page's $orders - and ?todo&todos=x answered 500 when {$task} inside
    // the loop then missed. A direct global that is an array wins over a like-named request
    // value, so the page's data stands; a request value of a name the page does not set is
    // still read as a field as before.

    // A name that is only a request value - a list ?prime[]=x as well - is neither here:
    // padTypeAskedKind answers it, after every other kind - see padTypeAsked.

    elseif ( is_array ( $GLOBALS [$item] ?? NULL )
             and ( isset ( $_GET [$item] ) or isset ( $_POST [$item] ) or isset ( $_COOKIE [$item] ) )
             and ! padTypeAsked ( $item ) ) return 'array';

    elseif ( padFieldCheck      ( $item                          ) and ! padTypeAsked ( $item ) ) return 'field';
    elseif ( padArrayCheck      ( $item                          ) and ! padTypeAsked ( $item ) ) return 'array';
    elseif ( padOptCheck        ( $item, 1                       ) ) return 'parm';
    elseif ( padChkLevel        ( $item                          ) ) return 'level';
    elseif ( defined            ( $item                          ) ) return 'constant';
    elseif ( padDataFileName    ( $item                          ) ) return 'local';
    elseif ( padScriptCheck     ( $item                          ) ) return 'script';
    elseif ( padPhpAllowed      ( $item                          ) ) return 'php';
    elseif ( pqSeq              ( $item                          ) ) return 'sequence';
    elseif ( pqAction           ( $item                          ) ) return 'action';

    return FALSE;

  }

  function padTypeTag ( $type, $goFunction=1 ) {

    global $padCommon;

    if     ( ! padValid     ( $type              )                 ) return FALSE;
    elseif ( padAppTagCheck ( $type              )                 ) return 'app';
    elseif ( padCommonCheck ( $type              ) and $padCommon  ) return 'common';
    elseif ( padTagBuiltIn  ( $type              )                 ) return 'pad';

    $common = padTypeCommon ( $type );

    // A PHP function of the same name as a pipe function gives way to it: {substr 0, 2}
    // reached PHP's substr() and came back whole, where {mid} and {left} - names PHP does
    // not have - ran the pipe function as a tag. date, range, stripslashes, substr, time,
    // trim and ucwords are such names.

    if ( $common == 'php' and $goFunction
         and ( padAppFunctionCheck ( $type ) or padFunctionBuiltIn ( $type ) ) )
      return 'function';

    if ( $common )
      return $common;

    if ( $goFunction and padTypeFunction ( $type, 0 ) )
      return 'function';

    if ( $goFunction )
      return padTypeAskedKind ( $type );

    return FALSE;

  }


  function padTypeTagCheck ( $type, $item ) {

    global $padBoolStore, $padContentStore, $padDataStore, $padDefineStore, $padMacroStore, $padSelect, $pqStore;

    if ( ! padValidName ( $type ) or ! padValidName ( $item ) )
      return FALSE;

    if     ( padAppTagCheck     ( $item                          ) and $type == 'app'      ) return $type;
    elseif ( padCommonCheck     ( $item                          ) and $type == 'common'   ) return $type;
    elseif ( padTagBuiltIn      ( $item                          ) and $type == 'pad'      ) return $type;
    elseif ( isset              ( $pqStore         [$item]       ) and $type == 'pull'     ) return $type;
    elseif ( isset              ( $padBoolStore    [$item]       ) and $type == 'bool'     ) return $type;
    elseif ( isset              ( $padContentStore [$item]       ) and $type == 'content'  ) return $type;
    elseif ( isset              ( $padDefineStore  [$item]       ) and $type == 'define'   ) return $type;
    elseif ( isset              ( $padMacroStore   [$item]       ) and $type == 'macro'    ) return $type;
    elseif ( isset              ( $padSelect       [$item]       ) and $type == 'select'   ) return $type;
    elseif ( isset              ( $padDataStore    [$item]       ) and $type == 'data'     ) return $type;
    elseif ( padAppIncludeCheck ( $item                          ) and $type == 'include'  ) return $type;
    elseif ( padFieldCheck      ( $item                          ) and $type == 'field'    ) return $type;
    elseif ( padTagCheck        ( $item                          ) and $type == 'property' ) return $type;
    elseif ( padArrayCheck      ( $item                          ) and $type == 'array'    ) return $type;
    elseif ( padOptCheck        ( $item, 1                       ) and $type == 'parm'     ) return $type;
    elseif ( padChkLevel        ( $item                          ) and $type == 'level'    ) return $type;
    elseif ( defined            ( $item                          ) and $type == 'constant' ) return $type;
    elseif ( padDataFileName    ( $item                          ) and $type == 'local'    ) return $type;
    elseif ( padScriptCheck     ( $item                          ) and $type == 'script'   ) return $type;
    elseif ( function_exists    ( strstr ( "$item(", '(', TRUE ) ) and $type == 'php'      ) return $type;
    elseif ( pqSeq              ( $item                          ) and $type == 'sequence' ) return $type;
    elseif ( pqAction           ( $item                          ) and $type == 'action'   ) return $type;
    elseif ( in_array ( padTypeFunction ( $item, 0 ), [ 'app', 'pad' ], TRUE ) and $type == 'function' ) return $type;

    return FALSE;

  }

  // Whether $name is a built-in tag: a file in PAD/tags/ of exactly that name, as a built-in
  // pipe function is one below. A file system that ignores case found tags/if.php for {If}
  // and {IF}, whose code compares its own name exactly: {If 1 eq 2}a{Else}b{/If} printed
  // nothing, {ELSEIF} was skipped and {Data 'q'} ended on a PHP warning, where a server that
  // tells case apart has no tag named If.

  function padTagBuiltIn ( $name ) {

    static $exact = NULL;

    if ( ! padCheck ( PAD . "tags/$name" ) )
      return FALSE;

    $exact ??= file_exists ( PAD . 'TAGS/' ) ? pqNames ( PAD . 'tags' ) : [];

    return ! $exact or isset ( $exact ["$name.php"] ) or isset ( $exact ["$name.pad"] );

  }

  // Whether $name is a built-in pipe function: a file in PAD/functions/ of exactly that name.
  // A file system that ignores case found DATE and LEFT as well - a template that worked on
  // such a machine failed on one that does not - and the rules kept by a function's name
  // missed them there: DATE('Y', 0) took 'Y' for the moment and answered 0, LEFT ran past its
  // parameter check into a PHP warning. Where the engine's directory ignores case its
  // listing says, read once per request.

  function padFunctionBuiltIn ( $name ) {

    static $exact = NULL;

    if ( ! padFileExists ( PAD . "functions/$name.php" ) )
      return FALSE;

    $exact ??= file_exists ( PAD . 'FUNCTIONS/' )
      ? array_flip ( str_replace ( '.php', '', preg_grep ( '/\.php$/', scandir ( PAD . 'functions' ) ) ) )
      : [];

    return ! $exact or isset ( $exact [$name] );

  }

  function padTypeFunction ( $type, $goTag=1 ) {

        if ( ! padValid          ( $type                       ) ) return FALSE;
    elseif ( padAppFunctionCheck ( $type                       ) ) return 'app';
    elseif ( padFunctionBuiltIn  ( $type                       ) ) return 'pad';

    $common = padTypeCommon ( $type );
    if ( $common )
      return $common;

    if ( $goTag and padTypeTag ( $type, 0 ) )
      return 'tag';

    if ( $goTag )
      return padTypeAskedKind ( $type );

    return FALSE;

  }

  function padTypeSeq ( $type, $item ) {

    global $padTypeSeq, $pqStore;

    $padTypeSeq = $type;

    if ( ! padValidName ( $type ) or ! padValidName ( $item ) )
      return FALSE;

    // The entries in start/types/ are named as they are written on disk, as the types and the
    // actions are (pqNames in lib/sequence.php): asked of a file system that ignores case,
    // {Flag:even '1..4'} found start/types/flag.php and ran as make - 2 2 4 4 - where a Linux
    // server has no type named Flag.

    $entry = pqNames ( PQ . 'start/types' );

    if ( $type == 'action' and pqAction ( $item )                           ) return 'action';
    if ( $item == 'action' and pqAction ( $type )                           ) return 'action';

    if ( isset ( $pqStore [$type] ) and pqAction ( $item )                   ) return 'action';
    if ( isset ( $pqStore [$item] ) and pqAction ( $type )                   ) return 'action';

    if ( isset ( $pqStore [$type] ) and pqSeq ( $item )                       ) return 'make';
    if ( isset ( $pqStore [$item] ) and pqSeq ( $type )                       ) return 'make';

    if ( isset ( $pqStore [$type] ) and isset ( $entry ["$item.php"] )      ) return $item;
    if ( isset ( $pqStore [$item] ) and isset ( $entry ["$type.php"] )      ) return $type;

    if ( pqSeq ( $item )    and isset ( $entry ["$type.php"] )              ) return $type;
    if ( pqSeq ( $type )    and isset ( $entry ["$item.php"] )              ) return $item;

    if ( pqAction ( $item ) and isset ( $entry ["$type.php"] )              ) return $type;
    if ( pqAction ( $type ) and isset ( $entry ["$item.php"] )              ) return $item;

    return FALSE;

  }

?>
