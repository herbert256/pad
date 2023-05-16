<?php

  $padReturn = padFileGetContents ( padApp . padPageGetName () . '.html' );

  return padPageAddSet ( $padSet [$pad], $padReturn );

?>