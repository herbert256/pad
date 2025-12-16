<?php



  function padFindContinueBreak ( $parm ) {

    global $pad, $padName, $padTag;

    if ( $parm and is_numeric ($parm) and $parm < 0 )
      return $pad + $parm;

    if ( $parm )
      for ( $key = $pad-1; $key >=0 ; $key-- )
        if ( $padName [$key] == $parm )
          return $key;

    if ( $parm and is_numeric ($parm) )
      for ( $key = $pad-1; $key >=0 ; $key-- )
        if ( $key == $parm )
          return $key;

    for ( $key = $pad-1; $key >=0 ; $key-- )
      if ( $padTag [$key] <> 'if' and $padTag [$key] <> 'case' )
        return $key;

    return $pad - 1;

  }



  /**
   * Splits string at first pipe not inside quotes.
   *
   * Respects single and double quoted strings, returning the
   * part before the pipe and the part after.
   *
   * @param string $input The string to split.
   *
   * @return array Two-element array: [before_pipe, after_pipe].
   */
  function padPipeSplit ($input) {

    $inSingle = false;
    $inDouble = false;
    $length   = strlen($input);
    $splitPos = null;

    for ($i = 0; $i < $length; $i++) {

        $ch = $input[$i];

        if ($ch === '\\' && $i + 1 < $length && ($input[$i + 1] === "'" || $input[$i + 1] === '"')) {
            $i++;
      } elseif ($ch === "'" && !$inDouble) {
            $inSingle = !$inSingle;
        } elseif ($ch === '"' && !$inSingle) {
            $inDouble = !$inDouble;
        } elseif ($ch === '|' && !$inSingle && !$inDouble) {
            $splitPos = $i;
            break;
        }

    }

    if ( $splitPos === null)
        return [ $input, '' ];

    $left  = substr($input, 0, $splitPos);
    $right = substr($input, $splitPos + 1);

    return [$left, $right];

  }


  /**
   * Replaces current tag in output with a value.
   *
   * Substitutes the tag between padStart and padEnd positions
   * in the current level's output buffer.
   *
   * @param string $value The value to insert.
   *
   * @return void
   *
   * @global array $padOut   Output buffer per level.
   * @global array $padStart Tag start positions per level.
   * @global array $padEnd   Tag end positions per level.
   * @global int   $pad      Current processing level.
   */
  function padLevel ( $value ) {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr ( $padOut [$pad], 0, $padStart [$pad] )
                   . $value
                   . substr ( $padOut [$pad], $padEnd [$pad]+1 );

  }


  /**
   * Checks if current tag is a comment (enclosed in #).
   *
   * Comments have format {#...#} - starts and ends with #.
   *
   * @return bool TRUE if tag is a comment.
   *
   * @global string $padBetween Content between { and }.
   */
  function padCommentCheck () {

    global $padBetween;

    return ( str_starts_with( $padBetween, '#' ) and str_ends_with($padBetween, '#') );

  }

  /**
   * Processes a comment tag by removing it from output.
   *
   * @return void
   */
  function padCommentGo () {

    return padLevelNo ( '' );

  }


  /**
   * Replaces tag with escaped version (won't be reprocessed).
   *
   * Uses &open; and &close; entities to prevent re-parsing.
   *
   * @param string $no The content to escape.
   *
   * @return void
   */
  function padLevelNo ( $no ) {

    padLevel ( "&open;$no&close;" );

  }


  /**
   * Escapes current single tag to prevent reprocessing.
   *
   * @return void
   *
   * @global string $padBetween Current tag content.
   */
  function padLevelNoSingle () {

    padLevelNo ( $GLOBALS ['padBetweenOrg'] );

  }


  /**
   * Escapes current paired tag to prevent reprocessing.
   *
   * @return void
   *
   * @global array $padOut   Output buffer per level.
   * @global array $padStart Tag start positions.
   * @global array $padEnd   Tag end positions.
   * @global int   $pad      Current level.
   */
  function padLevelNoPair () {

    global $padOut, $padStart, $padEnd, $pad;

    padLevelNo ( substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 ) );

  }


  /**
   * Extracts content between current tag delimiters.
   *
   * Sets padBetween and padOrgSet to the tag content.
   *
   * @return void
   *
   * @global array  $padOut     Output buffer per level.
   * @global array  $padStart   Tag start positions.
   * @global array  $padEnd     Tag end positions.
   * @global int    $pad        Current level.
   * @global string $padBetween Output: Extracted content.
   * @global string $padOrgSet  Output: Original content backup.
   */
  function padLevelBetween () {

    global $padOut, $padStart, $padEnd, $pad, $padBetween, $padOrgSet;

    $padBetween = substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );

    $padOrgSet = $padBetween;

  }


  /**
   * Escapes closing brace to prevent tag parsing.
   *
   * Replaces } with &close; entity at current end position.
   *
   * @return void
   *
   * @global array $padOut  Output buffer per level.
   * @global array $padEnd  Tag end positions.
   * @global int   $pad     Current level.
   */
  function padLevelNoOpen () {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );

  }


  /**
   * Finds the opening brace for current tag.
   *
   * Searches backwards from end position to find matching {.
   *
   * @return void
   *
   * @global array $padOut   Output buffer per level.
   * @global array $padStart Output: Tag start positions.
   * @global array $padEnd   Tag end positions.
   * @global int   $pad      Current level.
   */
  function padLevelStart () {

    global $padOut, $padStart, $padEnd, $pad;

    $padStart [$pad] = strrpos ( $padOut [$pad], '{', $padEnd [$pad] - strlen ( $padOut [$pad] ) );

  }


  /**
   * Finds the closing brace for next tag.
   *
   * Searches forward from beginning to find first }.
   *
   * @return void
   *
   * @global array $padOut  Output buffer per level.
   * @global array $padEnd  Output: Tag end positions.
   * @global int   $pad     Current level.
   */
  function padLevelEnd () {

    global $padOut, $padStart, $padEnd, $pad;

    $padEnd [$pad] = strpos ( $padOut [$pad], '}' );

  }


?>