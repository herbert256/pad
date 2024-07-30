<?php

  foreach ( $GLOBALS as $k => $v )
    if ( padValidStore ($k) )
      unset ( $GLOBALS [$k] );
  
?>