<?php

  // ini - INI files, read and written: [sections] of key = value lines, ; and # comments,
  // quoted values, key[] lists and key[name] records. A section is a record of its keys, a key
  // before any section a field of the whole; a value is text, INI having no types. Not known
  // on sight - a key = value line read as CSV before: name it, format='ini', or keep it in a
  // .ini file.

  // INI has no media type of its own: an answer is sent as text/plain, which a browser shows,
  // and no Accept header asks for it - text/plain is no request for INI.

  return [
    'name'      => 'INI',
    'mime'      => 'text/plain',
    'accept'    => [],
    'extension' => [ 'ini' ],
    'shape'     => 'data',
    'lib'       => 'ini',
    'read'      => fn ( $text, $options ) => padIniRead ( $text ),
    'write'     => fn ( $data, $options ) => padIniWrite ( $data ),
    'download'  => 'inline'
  ];

?>
