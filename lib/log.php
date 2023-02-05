<?php

  function padLog ( $type, $parm1='', $parm2='', $parm3='' ) {
    
    $log = 
      padFixedLenghtRight ($type,     10) .
      padFixedLenghtRight ($parm1 ,   15) .
      padFixedLenghtRight ($parm2,    20) .
      padFixedLenghtRight ($parm3,    60);

    padFilePutContents ( $GLOBALS ['padLogFile'], trim($log), 1 );

  }


?>