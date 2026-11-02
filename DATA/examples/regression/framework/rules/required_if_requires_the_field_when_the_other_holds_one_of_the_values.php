<?php

  // required_if:field,value - the field required, as required judges it, when the other field
  // holds one of the values as written: an absent or empty other field holds none, blanks
  // around it are trimmed, case counts, a list holds a value when one of its items does,
  // kinds finds kinds[] and kinds[] kinds, user[kind] is the field the form sends, an array
  // with keys of its own is no value, and a list field is required as required requires it.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleRows     = ( include APPS . 'regression/pages/forms/_rules.php' ) ['required_if'];
  $ruleVerdicts = [];

  foreach ( $ruleRows as [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                     . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
