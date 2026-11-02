<?php
  // Compatibility for the expansion's original helper; both kinds share one implementation.
  include_once PAD . 'subs/tables/formulas/_lib/text_extract.php';
  function padFormulaDelimited ( $args, $env, $after ) {
    return padFormulaTextExtract ( $args, $env, $after );
  }
?>
