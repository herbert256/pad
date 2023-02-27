<?php

  global $padHstShort , $padHstPnt, $padOccur, $pad;

  if ( $padOccur[$pad] )
    if ( $padHstShort )
      $padHstPnt [$pad] ['occurrences'] [$padOccur[$pad]] [ "Eval: $eval" ]  = $result [$key] [0];
    else
      $padHstPnt [$pad] ['occurrences'] [$padOccur[$pad]] ['eval'] ["$eval"] = $result [$key] [0];
  else
    if ( $padHstShort )
      $padHstPnt [$pad] [ "Eval: $eval" ]  = $result [$key] [0];
    else
      $padHstPnt [$pad] ['eval'] ["$eval"] = $result [$key] [0];


?>