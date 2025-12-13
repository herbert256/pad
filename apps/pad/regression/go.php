<?php

  if ( $sequence )
    padRedirect ( 'sequence/regression', [ 'go' => 'regression' ] );
  else  
    padRedirect ( "regression" );

?>