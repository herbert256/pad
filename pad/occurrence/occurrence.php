<?php

  include PAD . 'occurrence/init.php';

  if ( $padInfo )
    include PAD . 'events/occurStart.php';

  include PAD . 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include PAD . 'callback/row.php' ;

?>
