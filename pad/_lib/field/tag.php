<?php

  function padTag ( $field, $padIdx, $type, $parm ) {
  
    if ( file_exists ( pad . "tag/".$field.".php" ) )
      if ( $type == 7 ) 
        return 1;
      else
        return include pad . "tag/$field.php";

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