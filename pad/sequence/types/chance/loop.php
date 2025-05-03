<?php

  if ( $pqLoop > 0 ) {

    $pqModulo = ( $pqLoop % $pqParm ) + 1;

    if  ( $pqModulo == mt_rand ( 1, $pqParm ) ) return $pqLoop;
    else                                        return FALSE;

  } else

    return $pqLoop;


?>