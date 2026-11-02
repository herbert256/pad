<?php

include_once PAD . 'subs/ai/_lib/ai.php';
$ok = padAiJsonAnswer ( '{}', [ 'type' => 'object' ] ) === '' && padAiJsonAnswer ( '[]', [ 'type' => 'object' ] ) !== ''
   && padAiJsonAnswer ( '"é"', [ 'type' => 'string', 'maxLength' => 1 ] ) === ''
   && padAiJsonAnswer ( '[1,2,3]', [ 'type' => 'array', 'maxItems' => 2 ] ) !== ''
   && padAiJsonAnswer ( '4', [ 'type' => 'integer', 'maximum' => 3 ] ) !== ''
   && padAiJsonAnswer ( 'true', [ 'type' => 'boolean', 'enum' => [ FALSE ] ] ) !== ''
   && padAiJsonAnswer ( '{"nested":[0]}', [ 'type' => 'object', 'properties' => [ 'nested' => [ 'type' => 'array', 'items' => [ 'minimum' => 1 ] ] ] ] ) !== '';
$answer = $ok ? 'JSON constraints preserve Unicode and structural types' : 'FAIL';

?>
