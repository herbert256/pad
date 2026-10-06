<?php

  echo 'crumb:' . ( isset ( $crumb  ) ? 'variable' : 'none' )
     . ' pq:'   . ( isset ( $pqTrap ) ? 'variable' : 'none' )
     . ' _:'    . ( isset ( $_trap  ) ? 'variable' : 'none' );

?>
