<?php

  // Resolves tag properties - the field lookups padField routes here as types 7 and 8, and
  // the retries when a plain name matched nothing else.
  //
  // A name matching a file in properties/ is a built-in iteration property (first, last,
  // current, count, even, odd and the rest); the file is included against level $padIdx and
  // its return value is the answer. Failing that, when padField split a trailing :name or
  // :value off the reference and passed it as $parm, the remaining $field is read as a
  // 1-based column number into that level's current row and the matching key or value is
  // returned. INF when neither applies.

  function padTag ( $field, $padIdx, $type, $parm ) {

    if ( file_exists ( PAD . "properties/".$field.".php" ) )
      if ( $type == 7 )
        return 1;
      else
        return include PAD . "properties/$field.php";

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