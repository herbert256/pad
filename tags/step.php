<?php

  if ( ! isset ( $pad_parms_tag ['step'] ) )
    return pad_tag_error ("Parameter 'step' is required.");

  return include PAD_HOME . 'tags/sequence.php';

?>