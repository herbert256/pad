<?php

  global $padHstShort , $padHstPnt, $padOccur, $pad;

  if ( $padOccur[$pad] )
    if ( $padHstShort )
      $padHstPnt [$pad] ['occurrences'] [$padOccur[$pad]] [ "Var: $padFirst$padFld" ] = $padVal;
    else
      $padHstPnt [$pad] ['occurrences'] [$padOccur[$pad]] ['fields'] ["$padFirst$padFld"] = $padVal;
  else
    if ( $padHstShort )
      $padHstPnt [$pad] [ "Var: $padFirst$padFld" ] = $padVal;
    else
      $padHstPnt [$pad] ['fields'] ["$padFirst$padFld"] = $padVal;

?>