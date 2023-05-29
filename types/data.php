<?php

  if ( padTagParm ('print') )
    include pad . '_options/print.php';

  return $padDataStore [$padTag [$pad]];
 
?>