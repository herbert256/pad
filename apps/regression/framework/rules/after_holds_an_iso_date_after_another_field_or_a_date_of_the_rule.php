<?php

  // after:other - the mirror of before: strictly after the other field's moment or a date of
  // the rule; a lower-case t is no ISO date-time here.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleRows     = ( include APPS . 'regression/pages/forms/_rules.php' ) ['after'];
  $ruleVerdicts = [];

  foreach ( $ruleRows as [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                     . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
