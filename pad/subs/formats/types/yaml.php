<?php

  // yaml - YAML 1.2, read and written with the PHP yaml extension: the reader is the
  // engine's own, data/yaml.php - true and false the only booleans, 9:30 a text and not 570 -
  // and the writer yaml_emit's document between --- and ..., a text that would read as
  // another kind of value quoted.

  return [
    'name'      => 'YAML',
    'mime'      => 'application/yaml',
    'extension' => [ 'yaml', 'yml' ],
    'shape'     => 'data',
    'lib'       => 'data',
    'read'      => fn ( $text, $options ) => padFormatDataRead ( 'yaml', $text ),
    'write'     => fn ( $data, $options ) => padFormatYamlWrite ( $data ),
    'download'  => 'inline'
  ];

?>
