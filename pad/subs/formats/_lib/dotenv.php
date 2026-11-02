<?php
  function padDotenvOptions($options) {
    if(array_diff(array_keys($options),['duplicates'])||!in_array($options['duplicates']??'error',['error','last'],TRUE))
      padFormatFail(0,'Dotenv accepts duplicates=error or last');
  }
  function padDotenvRead($text,$options=[]) {
    padDotenvOptions($options);
    if(strlen($text)>1048576||!preg_match('//u',$text))padFormatFail(0,'Dotenv requires UTF-8, at most 1 MiB');
    if(str_contains($text,"\0"))padFormatFail(0,'NUL is not a configuration value');
    $out=[];
    foreach(preg_split('/\r\n|\n|\r/',$text) as $index=>$line) {
      $line=trim($line);if($line===''||$line[0]==='#')continue;
      if(!preg_match('/^(?:export[ \t]+)?([A-Za-z_][A-Za-z0-9_]*)[ \t]*=[ \t]*(.*)$/D',$line,$m))padFormatFail($index+1,'Expected KEY=value');
      [$all,$key,$value]=$m;
      if(array_key_exists($key,$out)&&($options['duplicates']??'error')==='error')padFormatFail($index+1,'Duplicate key '.$key);
      if($value!==''&&$value[0]==='"') {
        if(!preg_match('/^("(?:[^"\\\\]|\\\\.)*")[ \t]*(?:#.*)?$/D',$value,$q))padFormatFail($index+1,'Invalid double-quoted value');
        try{$value=json_decode($q[1],TRUE,8,JSON_THROW_ON_ERROR);}catch(JsonException $e){padFormatFail($index+1,'Invalid quoted escape');}
      }elseif($value!==''&&$value[0]==="'") {
        if(!preg_match("/^'([^']*)'[ \\t]*(?:#.*)?$/D",$value,$q))padFormatFail($index+1,'Invalid single-quoted value');
        $value=$q[1];
      }else $value=rtrim(preg_replace('/[ \t]+#.*$/','',$value));
      if(str_contains($value,"\0"))padFormatFail($index+1,'NUL is not a configuration value');
      $out[$key]=$value;
    }
    return $out;
  }
  function padDotenvWrite($data,$options=[]) {
    padDotenvOptions($options);
    if(!is_array($data)||count($data)>10000)padFormatFail(0,'Dotenv needs at most 10000 named string values');
    $out='';foreach($data as $key=>$value){
      if(!is_string($key)||!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D',$key)||!is_string($value)||str_contains($value,"\0"))padFormatFail(0,'Dotenv keys are identifiers and values are strings without NUL');
      try{$out.=$key.'='.json_encode($value,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";}
      catch(JsonException $e){padFormatFail(0,'Dotenv values require UTF-8');}
      if(strlen($out)>1048576)padFormatFail(0,'Dotenv exceeds 1 MiB');
    }return $out;
  }
?>
