<?php

  if ( ! isset ( $padBoolStore [ padTagParm('bool') ] ) )
    return padMakeFlag ( padTagParm('bool') );
  else
    return $padBoolStore [ padTagParm('bool') ];

?>
