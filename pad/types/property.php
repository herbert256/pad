<?php

  if ( $padParm )
    return padTagValue ( $padParm . ':' . $padTag[$pad], 1 );
  else
    return padTagValue ( $padTag[$pad], 1 );

?>