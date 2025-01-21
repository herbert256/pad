<?php

  include 'occurrence/init.php';

  if ( $padInfo )
    include 'events/occurStart.php';

  include 'occurrence/table.php';
  include 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include 'callback/row.php' ;

?>