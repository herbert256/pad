<?php

  if ( ! $padTraceTypes ['var'] )
    return;

  padTrace ( 'var', 'start', 'var=' . $padFld . ' options=' . padJson($padOpts) );

?>