<?php

  // not_in:a,b - none of the values as written, the mirror of in: - case counts, the value
  // trimmed, an empty field passes - and a list field when no item is one of them.
  // The rows are the shared table of the Pages suite's forms/_rules.php, which its
  // client_parity holds the browser's checker to.

  $ruleRows     = ( include APPS . 'regression/pages/forms/_rules.php' ) ['not_in'];
  $ruleVerdicts = [];

  foreach ( $ruleRows as [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                     . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
