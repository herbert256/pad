<?php

  if ( $name )
    return padFindNames ( $padDataStore [$name], $names );
  else
    return padFindNames ( $padDataStore, $names );

?>