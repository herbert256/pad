<?php

  if ( ! isset ( $padPrm [$pad] ['page'] ) )
    if ( ! isset ( $padPrm [$pad] ['start'] ) )
      if ( ! isset ( $padPrm [$pad] ['end'] ) )
        include PAD . 'handling/types/page.php';

?>
