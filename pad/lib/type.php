<?php


  /**
   * Determines the common type of a tag name.
   *
   * Checks various type stores and file locations to classify
   * the tag: pull, flag, content, data, include, field, property,
   * array, parm, level, constant, local, script, php, page,
   * sequence, or action.
   *
   * @param string $item The type name to classify.
   *
   * @return string|false The type classification or FALSE if unknown.
   */
  function padTypeCommon ( $item ) {

    if     ( isset              ( $GLOBALS ['pqStore']         [$item] ) ) return 'pull';
    elseif ( isset              ( $GLOBALS ['padBoolStore']    [$item] ) ) return 'flag';
    elseif ( isset              ( $GLOBALS ['padContentStore'] [$item] ) ) return 'content';
    elseif ( isset              ( $GLOBALS ['padSelect']       [$item] ) ) return 'select';
    elseif ( isset              ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( padAppIncludeCheck ( $item                                ) ) return 'include';
    elseif ( padFieldCheck      ( $item                                ) ) return 'field';
    elseif ( padTagCheck        ( $item                                ) ) return 'property';
    elseif ( padArrayCheck      ( $item                                ) ) return 'array';
    elseif ( padOptCheck        ( $item, 1                             ) ) return 'parm';
    elseif ( padChkLevel        ( $item                                ) ) return 'level';
    elseif ( defined            ( $item                                ) ) return 'constant';
    elseif ( padDataFileName    ( $item                                ) ) return 'local';
    elseif ( padScriptCheck     ( $item                                ) ) return 'script';
    elseif ( function_exists    ( $item                                ) ) return 'php';
    elseif ( file_exists        ( PT . $item                           ) ) return 'sequence';
    elseif ( file_exists        ( PQ . "actions/types/$item.php"       ) ) return 'action';

    return FALSE;

  }


  /**
   * Determines the type of a tag.
   *
   * Checks app tags, pad tags, common types, and optionally
   * function types.
   *
   * @param string $type       The tag name to check.
   * @param int    $goFunction Also check function types (default 1).
   *
   * @return string|false The tag type or FALSE if not found.
   */
  function padTypeTag ( $type, $goFunction=1 ) {

    if     ( ! padValid     ( $type                  ) ) return FALSE;
    elseif ( padAppTagCheck ( $type                  ) ) return 'app';
    elseif ( file_exists    ( PAD . "tags/$type.php" ) ) return 'pad';
    elseif ( file_exists    ( PAD . "tags/$type.pad" ) ) return 'pad';

    $common = padTypeCommon ( $type );
    if ( $common )
      return $common;

    if ( $goFunction and padTypeFunction ( $type, 0 ) )
      return 'function';

    return FALSE;

  }


  /**
   * Validates a type:item combination.
   *
   * Checks if the specified type handler exists and the item
   * matches that type classification.
   *
   * @param string $type The type name (e.g., 'app', 'pad', 'function').
   * @param string $item The item name to validate against the type.
   *
   * @return string|false The type if valid, FALSE otherwise.
   */
  function padTypeTagCheck ( $type, $item ) {

    if     ( padAppTagCheck     ( $item                                ) and $item == 'app'      ) return $type;
    elseif ( file_exists        ( PAD . "tags/$item.php"               ) and $type == 'pad'      ) return $type;
    elseif ( file_exists        ( PAD . "tags/$item.pad"               ) and $type == 'pad'      ) return $type;
    elseif ( isset              ( $GLOBALS ['pqStore']         [$item] ) and $type == 'pull'     ) return $type;
    elseif ( isset              ( $GLOBALS ['padBoolStore']    [$item] ) and $type == 'flag'     ) return $type;
    elseif ( isset              ( $GLOBALS ['padContentStore'] [$item] ) and $type == 'content'  ) return $type;
    elseif ( isset              ( $GLOBALS ['padSelect']       [$item] ) and $type == 'select'   ) return $type;
    elseif ( isset              ( $GLOBALS ['padDataStore']    [$item] ) and $type == 'data'     ) return $type;
    elseif ( padAppIncludeCheck ( $item                                ) and $type == 'include'  ) return $type;
    elseif ( padFieldCheck      ( $item                                ) and $type == 'field'    ) return $type;
    elseif ( padTagCheck        ( $item                                ) and $type == 'property' ) return $type;
    elseif ( padArrayCheck      ( $item                                ) and $type == 'array'    ) return $type;
    elseif ( padOptCheck        ( $item, 1                             ) and $type == 'parm'     ) return $type;
    elseif ( padChkLevel        ( $item                                ) and $type == 'level'    ) return $type;
    elseif ( defined            ( $item                                ) and $type == 'constant' ) return $type;
    elseif ( padDataFileName    ( $item                                ) and $type == 'local'    ) return $type;
    elseif ( padScriptCheck     ( $item                                ) and $type == 'script'   ) return $type;
    elseif ( function_exists    ( $item                                ) and $type == 'php'      ) return $type;
    elseif ( file_exists        ( PT . $item                           ) and $type == 'sequence' ) return $type;
    elseif ( file_exists        ( PQ . "actions/types/$item.php"       ) and $type == 'action'   ) return $type;
    elseif ( padTypeFunction    ( $item, 0                             ) and $type == 'function' ) return $type;

    return FALSE;

  }


  /**
   * Determines the type of a pipe function.
   *
   * Checks app functions, pad functions, common types, and
   * optionally falls back to tag types.
   *
   * @param string $type  The function name to check.
   * @param int    $goTag Also check tag types (default 1).
   *
   * @return string|false The function type or FALSE if not found.
   */
  function padTypeFunction ( $type, $goTag=1 ) {

        if ( ! padValid          ( $type                       ) ) return FALSE;
    elseif ( padAppFunctionCheck ( $type                       ) ) return 'app';
    elseif ( file_exists         ( PAD . "functions/$type.php" ) ) return 'pad';

    $common = padTypeCommon ( $type );
    if ( $common )
      return $common;

    if ( $goTag and padTypeTag ( $type, 0 ) )
      return 'tag';

    return FALSE;

  }



  /**
   * Determines sequence type from type:item pair.
   *
   * Resolves sequence actions and start types based on file
   * existence and store contents.
   *
   * @param string $type The first part of type:item.
   * @param string $item The second part of type:item.
   *
   * @return string|false The resolved sequence type or FALSE.
   *
   * @global string $padTypeSeq Stores the type for reference.
   */
  function padTypeSeq ( $type, $item ) {

    $GLOBALS ['padTypeSeq'] = $type;

    if ( $type == 'action' and file_exists ( PQ . "actions/types/$item.php" )                           ) return 'action';
    if ( $item == 'action' and file_exists ( PQ . "actions/types/$type.php" )                           ) return 'action';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ( PQ . "actions/types/$item.php")       ) return 'action';
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ( PQ . "actions/types/$type.php")       ) return 'action';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ( PT . "$item")                         ) return 'make';
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ( PT . "$type")                         ) return 'make';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ( PQ . "start/types/$item.php")         ) return $item;
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ( PQ . "start/types/$type.php")         ) return $type;

    if ( file_exists ( PT . "$item" )    and file_exists ( PQ . "start/types/$type.php")                ) return $type;
    if ( file_exists ( PT . "$type" )    and file_exists ( PQ . "start/types/$item.php")                ) return $item;

    if ( file_exists ( PQ . "actions/types/$item.php" ) and file_exists ( PQ . "start/types/$type.php") ) return $type;
    if ( file_exists ( PQ . "actions/types/$type.php" ) and file_exists ( PQ . "start/types/$item.php") ) return $item;

    return FALSE;

  }


?>
