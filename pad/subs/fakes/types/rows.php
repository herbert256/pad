<?php

  // {fake:rows 'customer:name, email, total:price', rows=3, seed=7}...{/fake:rows} -
  // Generate rows with several fields, optionally renaming each field as field:kind.
  // The tag dispatcher passes the specification to the shared fake-data pipeline.

  return padFakeRows ( padFakeOpt ( 'fields', 'name, email' ), padFakeOpt ( 'rows', 1 ), padFakeOpt ( 'seed' ), $padFakeOpts );

?>
