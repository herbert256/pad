<?php

  // The trace of an evaluation of a bare function name, and of one the validation refused,
  // after one that went through every stage.

  $evalTraceWas = $padEvalTrace;
  $padEvalTrace = TRUE;

  padEval ( '1 + 2' );
  padEval ( 'upper', 'ab' );

  $evalTraceName = str_contains ( json_encode ( $GLOBALS ['_eval'] ), '"upper"' ) ? 'upper' : 'other';
  $evalTraceKept = str_contains ( json_encode ( $GLOBALS ['_eval'] ), '"+"' ) ? 'kept' : 'gone';

  padEval ( '1 + 2' );
  $evalTraceCheck = $padCheckSyntax;
  $padCheckSyntax = FALSE;
  padEval ( '(' );
  $padCheckSyntax = $evalTraceCheck;

  $evalTraceFail = str_contains ( json_encode ( $GLOBALS ['_eval'] ), '"+"' ) ? 'kept' : 'gone';

  $padEvalTrace = $evalTraceWas;

?>
