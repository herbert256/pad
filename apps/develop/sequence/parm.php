<?php

  $parm = developSequenceParm ( $type );

  if ( $parm )
    file_put_contents ( PT . "$type/flags/parm", 1, LOCK_EX );

?>
