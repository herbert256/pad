<?php

  if ( ! isset ( $padPrm [$pad] ['page'] ) )
    if ( ! isset ( $padPrm [$pad] ['start'] ) )
      if ( ! isset ( $padPrm [$pad] ['end'] ) )
        include 'handling/page.php';

?>