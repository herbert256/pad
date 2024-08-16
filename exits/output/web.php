<?php

  if ( $padStop == '200' and $padWebEtag304 and ( $padClientEtag ?? '' ) == $padEtag )
    $padStop = 304;

  padWebSend ( $padStop );

?>