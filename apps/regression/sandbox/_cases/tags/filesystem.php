<?php

  // {dir} and {files}: a directory as this level's data. {dir} takes the path as given and
  // answers bare entry names; {files} resolves against a base and answers a path, file, ext,
  // item and dir field per entry. Both list the scan/ directory of this application, which
  // holds exactly its index pair.

  return [

    [ 'dir lists a directory, one occurrence per entry',
      <<<'PAD'
      {dir '{$caseScanDir}'}{$dir}{notLast} {/notLast}{/dir}
      PAD,
      'index.pad index.php',
      [ 'caseScanDir' => APP . 'scan' ] ],

    [ 'files scans against a base and fields each entry',
      <<<'PAD'
      {files 'scan', base='app', onlyFiles}{$item}.{$ext}{notLast} {/notLast}{/files}
      PAD,
      'index.pad index.php' ],

  ];

?>
