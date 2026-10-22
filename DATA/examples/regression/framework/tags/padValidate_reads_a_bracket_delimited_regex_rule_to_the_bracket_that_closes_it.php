<?php

  // A regex: rule between ( ) - or { }, [ ], < > - runs to the bracket that closes the
  // opening one, as PCRE reads it, nested brackets and escaped ones counted: the | of the
  // group's alternation is part of the pattern, the | after it starts the next rule.

  $nested = json_encode ( [ padValidate ( [ 'size' => 'required|regex:((S)|M)' ], [ 'size' => 'M' ] ),
                            padValidate ( [ 'size' => 'regex:((S)|M)|max:1' ], [ 'size' => 'x' ] ),
                            padValidate ( [ 'size' => 'regex:{^(\{|a)$}|required' ], [ 'size' => '{' ] ),
                            padValidate ( [ 'size' => 'regex:[^([ab]|c)$]i|max:1' ], [ 'size' => 'B' ] ),
                            padValidateSplit ( 'regex:((a)|b)|required' ) ] );

?>
