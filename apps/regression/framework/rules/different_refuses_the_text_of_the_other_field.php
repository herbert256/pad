<?php

  // different:field - not the text of the other field, the mirror of same: - the other found
  // where PHP files it and trimmed, case counting; one absent, empty or a list holds no text
  // the value could be. The rows are the shared table of the Pages suite's forms/_rules.php,
  // which its client_parity holds the browser's checker to.

  $ruleRows     = ( include APPS . 'regression/pages/forms/_rules.php' ) ['different'];
  $ruleVerdicts = [];

  foreach ( $ruleRows as [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = "$ruleField $ruleList " . json_encode ( $ruleData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' : '
                     . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
