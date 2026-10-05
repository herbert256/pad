<?php

  // padCaseWhen: whether a {when ...} of a {case} answers the case's value. A when may list
  // several values - {when 'red', 'orange'} - and answers when any of them is equal; each is
  // its own expression, split at the commas outside quotes and brackets. padCaseWhenValues
  // gives the list itself, for the strict scan of a case's branches.

  function padCaseWhenValues ( $when ) {

    $values = [];

    foreach ( padParseOptions ( $when ) as $one )
      if ( trim ( $one ) !== '' )
        $values [] = padEval ( $one );

    return $values;

  }

  function padCaseWhen ( $basis, $when ) {

    foreach ( padCaseWhenValues ( $when ) as $value )
      if ( $basis == $value )
        return TRUE;

    return FALSE;

  }

?>
