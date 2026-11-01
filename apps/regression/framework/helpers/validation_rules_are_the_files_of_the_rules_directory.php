<?php

  // The validation rules are the files of pad/subs/rules/types/ - padRuleTypes reads them there,
  // as padValidate and the template's rules= do - and the browser's checker carries the
  // script of every rule that has one in pad/subs/rules/assets/, each registered once.

  $ruleFiles = array_map ( fn ( $file ) => basename ( $file, '.php' ), glob ( PAD . 'subs/rules/types/*.php' ) );
  $ruleJs    = array_map ( fn ( $file ) => basename ( $file, '.js' ),  glob ( PAD . 'subs/rules/assets/*.js' ) );

  $ruleList    = ( padRuleTypes () === $ruleFiles ) ? 'the files of subs/rules/types' : 'another list';
  $ruleScripts = ( substr_count ( padValidateCode (), "addRule('" ) == count ( array_intersect ( $ruleFiles, $ruleJs ) ) ) ? 'every script once' : 'not every script';

?>
