<?php

  // The lenient walk reads data of a type with no reader as untyped data - and a type that
  // is a path is one: ../../apps/hello/index was part of the reader's include path, ran the
  // hello application's PHP and gave no rows.

  $padCheckSyntax = FALSE;

  $t = '../../apps/hello/index';

?>
