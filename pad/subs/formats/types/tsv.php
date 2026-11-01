<?php

  // tsv - tab separated values (IANA text/tab-separated-values), read and written: a header
  // line of field names, then a row per line, the cells split at the tabs. TSV has no
  // quoting, so a tab, a newline, a carriage return and a backslash inside a cell are written
  // \t, \n, \r and \\ and read back. Known on sight: a first line with tabs and no comma or
  // quote, and a tab on the line behind it.

  return [
    'name'      => 'TSV',
    'mime'      => 'text/tab-separated-values',
    'extension' => [ 'tsv', 'tab' ],
    'shape'     => 'rows',
    'lib'       => 'tsv',
    'read'      => fn ( $text, $options ) => padTsvRead ( $text ),
    'write'     => fn ( $data, $options ) => padTsvWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'csv', fn ( $text ) => (bool) preg_match ( '/^[^,"\n]*\t[^,"\n]*\r?\n[^\n]*\t/', $text ) ],
    'download'  => 'inline'
  ];

?>
