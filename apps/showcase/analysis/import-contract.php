<?php
  // A local import and a provider response are validated against one nested contract.
  // No provider is called: this uses the same response boundary as padAi(json=true).
  $importSchema = [ 'type' => 'object', 'required' => [ 'name', 'items' ], 'properties' => [
    'name' => [ 'type' => 'string', 'minLength' => 1 ],
    'items' => [ 'type' => 'array', 'items' => [ 'type' => 'object', 'required' => [ 'quantity' ], 'properties' => [ 'quantity' => [ 'type' => 'integer', 'minimum' => 1 ] ], 'additionalProperties' => FALSE ] ]
  ], 'additionalProperties' => FALSE ];
  $importText = '{"name":"Local order","items":[{"quantity":2}]}';
  $importValue = json_decode ( $importText, FALSE, 64, JSON_THROW_ON_ERROR );
  $importCheck = padSchema ( 'jsonschema', $importValue, $importSchema );
  include_once PAD . 'subs/ai/_lib/json.php';
  $previousAiJson = padAiStore ( 'json' );
  $aiBoundaryError = padAiJsonAnswer ( $importText, $importSchema );
  padAiStore ( 'json', $previousAiJson );
  $badImport = padSchema ( 'jsonschema', json_decode ( '{"name":"Bad order","items":[{"quantity":0}]}' ), $importSchema );
  $contractRows = [ [ 'boundary' => 'JSON import', 'valid' => $importCheck ['valid'] ? 'yes' : 'no', 'issue' => '' ],
    [ 'boundary' => 'AI answer validator', 'valid' => $aiBoundaryError === '' ? 'yes' : 'no', 'issue' => $aiBoundaryError ],
    [ 'boundary' => 'Invalid local import', 'valid' => 'no', 'issue' => $badImport ['issues'] [0] ['message'] ] ];
?>
