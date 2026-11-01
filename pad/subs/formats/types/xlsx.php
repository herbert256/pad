<?php

  // xlsx - Excel spreadsheets (Office Open XML, SpreadsheetML), written and read with PHP's
  // zip extension: rows as a workbook of one sheet - a bold header row frozen at the top,
  // numbers as numbers, booleans as booleans, dates with a date format, every text a text
  // and never a formula - the same bytes for the same rows; and the first sheet of a
  // workbook as rows - shared and inline strings, numbers, booleans, dates (sheet= picks
  // another, by name or number). A format of bytes: read from a _data/ file as it is, never
  // run as PAD first; a page answer is an attachment.

  return [
    'name'      => 'XLSX',
    'mime'      => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'extension' => [ 'xlsx' ],
    'shape'     => 'rows',
    'lib'       => 'xlsx',
    'binary'    => TRUE,
    'options'   => [ 'sheet' ],
    'read'      => fn ( $bytes, $options ) => padXlsxRead ( $bytes, $options ),
    'write'     => fn ( $data, $options ) => padXlsxWrite ( padFormatRows ( $data ), $options ),
    'download'  => 'attachment'
  ];

?>
