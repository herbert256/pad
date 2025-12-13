<?php


  /**
   * Gets a parameter value from a specific level.
   *
   * Retrieves a parameter by field name at the given level index.
   * Returns TRUE for check operations (type 7), otherwise the value.
   *
   * @param string $fld  The parameter field name.
   * @param int    $idx  The level index to check.
   * @param int    $type The request type (7 = check, other = value).
   *
   * @return mixed TRUE for type 7 if exists, value otherwise, INF if not found.
   */
  function padParm ( $fld, $idx, $type ) {

    if ( isset ( $GLOBALS ['padPrm'] [$idx] [$fld] ) )
      if ( $type == 7 )
        return TRUE;
      else
        return $GLOBALS ['padPrm'] [$idx] [$fld];

    return INF;

  }

?>