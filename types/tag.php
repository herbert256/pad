<?php

  if ( $padParm )
    return padTagValue ( $padParm . ':' . $padTag[$pad] );
  else
    return padTagValue ( $padTag[$pad] );

?>