<?php

  // The restart page answers a bare 500 under this action - and so would a restart loop
  // that ran on until PHP's time limit ended it, the failure the guard exists to prevent:
  // the answer could not tell the two apart. Fetched here, the 500 has to come at once.

  $start = hrtime ( TRUE );
  $r     = padCurl ( $padHost . $padApp . '/?restart&padInclude' );
  $ms    = ( hrtime ( TRUE ) - $start ) / 1e6;

  $verdict = ( $r ['result'] == '500' and $ms < 5000 ) ? 'yes' : 'NO';

?>
