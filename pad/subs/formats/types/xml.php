<?php

  // xml - XML, read only: the reader is the engine's own, data/xml.php - an element's
  // attributes and children its fields, a repeated element a list, the names of every
  // namespace taken. There is no writer: rows have no one XML of their own - an element per
  // row or per field, attributes or children - so a page answers no XML (?page&padFormat=xml
  // is 406, as the pages suite's format/answers holds); the feeds and GPX are XML that is
  // written, by rss, atom and gpx.

  return [
    'name'      => 'XML',
    'mime'      => 'application/xml',
    'extension' => [ 'xml' ],
    'shape'     => 'data',
    'lib'       => 'data',
    'read'      => fn ( $text, $options ) => padFormatDataRead ( 'xml', $text )
  ];

?>
