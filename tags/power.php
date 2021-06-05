<?php

  if ( ! isset ( $pad_parms_tag ['power'] ) )
    return pad_tag_error ("Parameter 'power' is required.");

  return include PAD_HOME . 'tags/sequence.php';

?>