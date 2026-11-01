<?php

  // jsonseq - JSON text sequences (RFC 7464), read and written: a record a row - an RS
  // (0x1E), a JSON text, a line feed - as jq --seq writes them and a GeoJSON text sequence
  // (RFC 8142) is made of; a record that is no JSON, or a number cut short, is the error
  // under the strict check and passed over on the lenient walk, as the RFC has a parser
  // recover (subs/formats/_lib/jsonseq.php). Known on sight: a text that opens with an RS.

  // The RFC registers no file extension: the format is named - format='jsonseq',
  // padFormat=jsonseq - or read by its media type, and an answer names no file.

  return [
    'name'      => 'JSON text sequence',
    'mime'      => 'application/json-seq',
    'extension' => [],
    'shape'     => 'rows',
    'lib'       => 'jsonseq',
    'read'      => fn ( $text, $options ) => padJsonSeqRead ( $text ),
    'write'     => fn ( $data, $options ) => padJsonSeqWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'csv', fn ( $text ) => str_starts_with ( $text, "\x1E" ) ]
  ];

?>
