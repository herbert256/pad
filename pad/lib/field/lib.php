<?php


  /**
   * Gets the level index for a named scope.
   *
   * Searches for a level by name, returns numeric levels directly,
   * handles negative relative levels, and falls back to current level.
   *
   * @param string $search The level name, number, or tag to find.
   *
   * @return int The level index.
   *
   * @global int   $pad     Current processing level.
   * @global array $padName Names per level.
   * @global array $padTag  Tag names per level.
   */
  function padFieldGetLevel  ( $search ) {

    global $pad, $padName, $padTag;

    if ( trim($search) == '' )
      return $pad;

    for ( $i=$pad; $i; $i-- )
      if ( $padName [$i] == $search )
        return $i;

    if ( $search == 'PHP' )
      return 0;

    if ( is_numeric($search) and $search < 0 )
      return $pad + $search;

    if ( is_numeric($search) )
      return $search;

    for ( $i=$pad; $i; $i-- )
      if ( $padTag [$i] == $search )
        return $i;

    return $pad;

  }


  /**
   * Finds first level with a non-control tag.
   *
   * Walks up the level stack to find the first level whose
   * tag is not 'if' or 'case' and type is not 'tag'.
   *
   * @param int $flag If set, start from level below current.
   *
   * @return int The level index.
   *
   * @global int   $pad     Current processing level.
   * @global array $padType Types per level.
   * @global array $padTag  Tag names per level.
   */
  function padFieldFirstParmTag ($flag=0) {

    global $pad, $padType, $padTag;

    $start = ($flag) ? $pad-1 : $pad;

    for ($i=$start; $i; $i--) // ToDo: Array van make en split/after verwerken
      if ( $padTag [$i] <> 'if' and $padTag [$i] <> 'case' and $padType[$i] <> 'tag' )
        return $i;

    return $pad - 1;

  }


  /**
   * Finds first level whose type is not 'tag'.
   *
   * Walks up from current level minus offset to find first
   * level that is not a tag type.
   *
   * @param int $lvl Offset from current level to start.
   *
   * @return int The level index.
   *
   * @global int   $pad     Current processing level.
   * @global array $padType Types per level.
   */
  function padFieldFirstNonTag ($lvl=0) {

    global $pad, $padType;

    $start = $pad-$lvl;

    for ($i=$start; $i; $i--)
      if ( $padType[$i] <> 'tag' )
        return $i;

    return $pad - 1;

  }


?>