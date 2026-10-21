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

  // A value spliced into the case's value or a when's - {case '{$q}'} - holds its syntax
  // characters as the stand-ins of padProtect (lib/encoding.php), which only the page's exit
  // puts back: {case '{$q}'}{when 'ann@x.com'} missed for q = ann@x.com. Compared as the
  // characters they are, as the comparison operators of an expression compare them
  // (padEvalScalar).

  function padCaseWhen ( $basis, $when ) {

    $text = fn ( $one ) => ( is_string ( $one ) and str_contains ( $one, "\xEE" ) ) ? padUnprotect ( $one ) : $one;

    $basis = $text ( $basis );

    foreach ( padCaseWhenValues ( $when ) as $value )
      if ( $basis == $text ( $value ) )
        return TRUE;

    return FALSE;

  }

?>
