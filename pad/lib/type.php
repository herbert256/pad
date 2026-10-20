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
  //                  action - in that order
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

    elseif ( is_array ( $GLOBALS [$item] ?? NULL )
             and ( isset ( $_GET [$item] ) or isset ( $_POST [$item] ) or isset ( $_COOKIE [$item] ) ) ) return 'array';

    elseif ( padFieldCheck      ( $item                          ) ) return 'field';
    elseif ( padArrayCheck      ( $item                          ) ) return 'array';
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
    elseif ( padCheck       ( PAD . "tags/$type" )                 ) return 'pad';

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

    return FALSE;

  }


  function padTypeTagCheck ( $type, $item ) {

    global $padBoolStore, $padContentStore, $padDataStore, $padDefineStore, $padMacroStore, $padSelect, $pqStore;

    if ( ! padValidName ( $type ) or ! padValidName ( $item ) )
      return FALSE;

    if     ( padAppTagCheck     ( $item                          ) and $type == 'app'      ) return $type;
    elseif ( padCommonCheck     ( $item                          ) and $type == 'common'   ) return $type;
    elseif ( padCheck           ( PAD . "tags/$item"             ) and $type == 'pad'      ) return $type;
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
    elseif ( padTypeFunction    ( $item, 0                       ) and $type == 'function' ) return $type;

    return FALSE;

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
