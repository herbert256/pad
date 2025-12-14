<?php


  /**
   * Gets a tag property value.
   *
   * Checks for tag handler file, returns its result or checks
   * for positional name/value access in current data.
   *
   * @param string $field  The tag or position identifier.
   * @param int    $padIdx The level index to check.
   * @param int    $type   The request type (7 = check, 8 = value).
   * @param string $parm   Parameter ('name' or 'value' for positional).
   *
   * @return mixed TRUE/1 for check, value for get, INF if not found.
   */
  function padTag ( $field, $padIdx, $type, $parm ) {

    if ( file_exists ( PAD . "tag/".$field.".php" ) )
      if ( $type == 7 )
        return 1;
      else
        return include PAD . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) ) {

      if ( $type == 7 )
        return 1;

      $pos = 1;

      foreach( $GLOBALS ['padCurrent'] [$padIdx] as $key => $value )
        if ( $pos++ == $field )
          if ( $type == 7 )
            return TRUE;
          else
            return ( $parm == 'name') ? $key : $value;

    }

    return INF;


  }

?>