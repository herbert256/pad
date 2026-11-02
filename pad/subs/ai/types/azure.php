<?php
  // Azure v1 chat completions: model is the deployment name; dated deployment APIs are separate.
  $endpoint = padEnv ( 'AZURE_OPENAI_ENDPOINT', '' );
  $endpoint = is_string ( $endpoint ) ? rtrim ( $endpoint, '/' ) : '';
  $provider = include __DIR__ . '/openai.php';
  $provider ['name'] = 'Azure OpenAI';
  $provider ['key'] = 'AZURE_OPENAI_API_KEY';
  $provider ['model'] = padEnv ( 'AZURE_OPENAI_DEPLOYMENT', '' );
  $provider ['url'] = $endpoint . '/openai/v1/chat/completions';
  $provider ['headers'] = fn ( $ask ) => [ 'api-key' => $ask ['key'] ];
  $provider ['check'] = function ( $ask, $url ) use ( $endpoint ) {
    $url ??= $endpoint . '/openai/v1/chat/completions';
    if ( ! is_string ( $url ) or ! preg_match ( '~^https://[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.(?:openai\.azure\.com|services\.ai\.azure\.com)/openai/v1/chat/completions$~iD', $url ) )
      return 'Azure requires AZURE_OPENAI_ENDPOINT as an https Azure resource origin; url must use /openai/v1/chat/completions without api-version';
    if ( ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9._-]{0,127}$/D', $ask ['model'] ) ) return 'Azure model must be its deployment name';
    return '';
  };
  return $provider;
?>
