<?php

  if ( ! isset ( $pad_parms_tag ['multiple'] ) )
    return pad_tag_error ("Parameter 'multiple' is required.");

  return include PAD_HOME . 'tags/sequence.php';

?>