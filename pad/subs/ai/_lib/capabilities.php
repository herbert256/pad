<?php
  // Capabilities describe the PAD adapter, not every model or custom endpoint behind it.
  function padAiKindCapabilities ( $kind ) {
    return ($kind['capabilities'] ?? []) + [
      'text'=>TRUE, 'localJson'=>TRUE, 'localSchema'=>TRUE,
      'nativeJson'=>FALSE, 'nativeSchema'=>FALSE, 'streaming'=>FALSE, 'tools'=>FALSE
    ];
  }

  // No constraints are removed or optional fields made required to satisfy a provider.
  function padAiNativeSchemaError ( $schema, $protocol, $path='schema', $depth=0 ) {
    if ($depth>32) return "$path exceeds the native schema depth limit";
    if (!isset($schema['type'])) return "$path.type is required for native structured output";
    if ($depth===0 && in_array($protocol,['openai','anthropic'],TRUE) && $schema['type']!=='object')
      return "$path must have object type for this native adapter";
    if ($protocol==='anthropic') foreach(['minimum','maximum','minLength','maxLength','minItems','maxItems'] as $key)
      if(array_key_exists($key,$schema))return "$path.$key is not supported by this native adapter; use local validation";
    if ($schema['type']==='object' && in_array($protocol,['openai','anthropic'],TRUE)) {
      if (($schema['additionalProperties']??NULL)!==FALSE) return "$path.additionalProperties must be false for this native adapter";
      if ($protocol==='openai') {
        $properties=array_map('strval',array_keys($schema['properties']??[]));$required=$schema['required']??[];
        sort($properties);sort($required);
        if($properties!==$required)return "$path.required must contain every property exactly once for this native adapter";
      }
    }
    if($schema['type']==='array'&&!isset($schema['items']))return "$path.items is required for native structured output";
    foreach($schema['properties']??[] as $key=>$child)
      if(($error=padAiNativeSchemaError($child,$protocol,"$path.properties.$key",$depth+1))!=='')return $error;
    if(isset($schema['items']))return padAiNativeSchemaError($schema['items'],$protocol,"$path.items",$depth+1);
    return '';
  }

  function padAiWireSchema ( $schema ) {
    if(isset($schema['properties'])) {
      foreach($schema['properties'] as $name=>$child)$schema['properties'][$name]=padAiWireSchema($child);
      $schema['properties']=(object)$schema['properties'];
    } elseif(($schema['type']??'')==='object') $schema['properties']=new stdClass;
    if(isset($schema['items']))$schema['items']=padAiWireSchema($schema['items']);
    return (object)$schema;
  }

  function padAiNativeBody ( $body, $ask, $kind ) {
    if(empty($ask['native']))return $body;
    $schema=$ask['schema']?padAiWireSchema($ask['schema']):NULL;
    switch($kind['nativeProtocol']??'') {
      case 'openai': $body['response_format']=$schema
        ? ['type'=>'json_schema','json_schema'=>['name'=>'pad_response','strict'=>TRUE,'schema'=>$schema]]
        : ['type'=>'json_object'];break;
      case 'ollama': $body['format']=$schema??'json';break;
      case 'anthropic': $body['output_config']['format']=['type'=>'json_schema','schema'=>$schema];break;
    }
    return $body;
  }
?>
