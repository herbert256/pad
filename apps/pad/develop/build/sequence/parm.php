<?php

  $parm = pqParm ( $type );

  if ( $parm )
    filePutFile ( PAD ."sequence/types/$type/flags/parm", 1 );

?>
