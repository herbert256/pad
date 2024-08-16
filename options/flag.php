<?php

  if ( ! isset ( $padFlagStore [ padTagParm('flag') ] ) )
    return padMakeFlag ( padTagParm('flag') );
  else
    return $padFlagStore [ padTagParm('flag') ];

?>