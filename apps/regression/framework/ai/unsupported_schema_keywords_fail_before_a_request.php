<?php

$padCheckSyntax = FALSE;
padCurlFake ( [] );
$result = padAi ( 'JSON please', [ 'provider' => 'ollama', 'schema' => [ 'type' => 'object', 'unknownKeyword' => TRUE ] ] );
$ok = $result === '' && str_contains ( padAiError (), 'unsupported keyword' ) && count ( padCurlRecorded () ) === 0;
$answer = $ok ? 'unsupported schemas fail before transport' : 'FAIL';

?>
