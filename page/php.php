<?php
 
  $padCall = padApp . padPageGetName () . '.php';

  return padPageAddSet ( $padSet [$pad], "{call '$padCall'}" );

?>