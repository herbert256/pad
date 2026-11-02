<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}function padEnv($k,$default=''){return $default;}
function padMakeSafe($s,$n){return substr($s,0,$n);}function padError($e){throw new InvalidArgumentException($e);}
function padLog($m,$level='info',$context=[]){return TRUE;}function padCurlFaking(){return TRUE;}
function padCurl($request){$GLOBALS['requests'][]=$request;return ['result'=>$GLOBALS['status'],'data'=>json_encode($GLOBALS['response'])];}
require PAD.'lib/subsystem.php';require PAD.'lib/ai.php';require PAD.'subs/ai/_lib/ai.php';
function check($v,$why){if(!$v)throw new RuntimeException($why);}
$requests=[];$status=200;$response=['choices'=>[['message'=>['content'=>'{"ok":true}']]]];
$schema=['type'=>'object','properties'=>['ok'=>['type'=>'boolean']],'required'=>['ok'],'additionalProperties'=>FALSE];
$base=['provider'=>'openai','key'=>'fixture-only','ttl'=>0,'schema'=>$schema];
check(padAi('Return a flag',$base)==='{"ok":true}','local mode response');
check(!isset(json_decode($requests[0]['post'],TRUE)['response_format']),'local mode preserves request body');
check(padAi('Return a flag',$base+['native'=>TRUE])==='{"ok":true}','native mode response');
$body=json_decode($requests[1]['post'],TRUE);check($body['response_format']['json_schema']['strict']===TRUE&&$body['response_format']['json_schema']['schema']===$schema,'OpenAI schema wire shape');
$response=['choices'=>[['message'=>['content'=>'{"ok":"yes"}']]]];
check(padAi('Return a flag',$base+['native'=>TRUE])===''&&str_contains(padAiError(),'must be boolean'),'invalid native answer still validated');
$response=['choices'=>[['message'=>['content'=>'{"ok":true}']]]];
padAi('Return a flag',['provider'=>'openai','key'=>'fixture','json'=>TRUE,'native'=>TRUE]);
$body=json_decode(end($requests)['post'],TRUE);check($body['response_format']===['type'=>'json_object']&&str_contains($body['messages'][0]['content'],'JSON'),'JSON mode instruction');
$count=count($requests);check(padAi('Return a flag',['provider'=>'cohere','native'=>TRUE,'json'=>TRUE])===''&&str_contains(padAiError(),'nativeJson')&&count($requests)===$count,'unsupported native fails before request');
$bad=$schema;unset($bad['additionalProperties']);check(padAi('Flag',array_replace($base,['schema'=>$bad,'native'=>TRUE]))===''&&count($requests)===$count,'strict schema restrictions');
$response=['message'=>['content'=>'{"ok":true}']];check(padAi('Flag',['provider'=>'ollama','schema'=>$schema,'native'=>TRUE])!=='','Ollama response');
check(json_decode(end($requests)['post'],TRUE)['format']===$schema,'Ollama schema wire shape');
$response=['content'=>[['type'=>'text','text'=>'{"ok":true}']]];check(padAi('Flag',['provider'=>'anthropic','key'=>'fixture','schema'=>$schema,'native'=>TRUE])!=='','Anthropic response');
check(json_decode(end($requests)['post'],TRUE)['output_config']['format']['schema']===$schema,'Anthropic schema wire shape');
$empty=padAiWireSchema(['type'=>'object','properties'=>[]]);check(str_contains(json_encode($empty),'"properties":{}'),'empty properties are object');
$all=padAiCapabilities();check(count($all)===count(padAiProviders())&&$all['openai']['nativeSchema']&&$all['azure']['nativeSchema']&&!$all['cohere']['nativeSchema'],'explicit adapter capability map');
$ask=['provider'=>'openai','model'=>'fixture','system'=>'','prompt'=>'x','tokens'=>10,'json'=>TRUE,'schema'=>$schema];
check(padAiCacheKey($ask,'https://example.test')!==padAiCacheKey($ask+['native'=>TRUE],'https://example.test'),'cache separation');
$status=429;$response=['error'=>['message'=>'fixture limit']];check(padAi('Flag',$base+['native'=>TRUE])===''&&str_contains(padAiError(),'fixture limit'),'transport error propagation');
echo "AI capabilities: opt-in wire bodies, unsupported modes, strict/local validation, cache separation and errors passed\n";
