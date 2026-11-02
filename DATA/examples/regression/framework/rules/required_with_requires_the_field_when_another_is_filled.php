<?php

  // required_with:field,... - the field required when one of the others is filled as
  // required judges it: 0 is filled, blanks are not, a list is with an item.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleRows     = ( include APPS . 'regression/pages/forms/_rules.php' ) ['required_with'];
  $ruleVerdicts = [];

  foreach ( $ruleRows as [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                     . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
