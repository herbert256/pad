<?php

  $padSeqInfo [$padSeqInitType] [] = $padSeqInitValue;

  if     ( $padSeqInitValue == 'sequence'  ) include "sequence/inits/$padSeqInitType/sequence.php";
  elseif ( $padSeqInitValue == 'pull'      ) include "sequence/inits/$padSeqInitType/pull.php";
  elseif ( $padSeqInitValue == 'action'    ) include "sequence/inits/action/action.php";
  elseif ( padSeqPlay ( $padSeqInitValue ) ) include "sequence/inits/play/play.php";

?>