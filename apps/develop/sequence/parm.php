<?php

  $parm = pqParm ( $type );

  if ( $parm )
    padFilePut ( PT . "$type/flags/parm", 1 );

?>