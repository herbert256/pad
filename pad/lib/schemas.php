<?php
  function padSchema ( $kind, $value, $schema, $options = [] ) {
    include_once PAD . 'subs/schemas/_lib/schemas.php';
    if ( ! is_string ( $kind ) || ! padKind ( 'schemas', $kind ) || $options !== [] ) {
      padError ( 'padSchema: expected a known kind and empty options; no coercion or remote references are supported' ); return NULL;
    }
    $definition = include PAD . "subs/schemas/types/$kind.php";
    return $definition ['call'] ( $value, $schema );
  }
?>
