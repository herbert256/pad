<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padLog($message,$level,$context){$GLOBALS['logged']=[$message,$level,$context];return $GLOBALS['logResult'];}
$definition=include PAD.'subs/channels/types/log.php';$send=$definition['send'];$logResult=TRUE;
$message=['name'=>'example','text'=>'private body','data'=>['token'=>'secret'],'id'=>'123'];
if($send($message,['id'=>'user','body'=>'0'])!==TRUE||isset($logged[2]['body'])||isset($logged[2]['data']))throw new Exception('default log content');
$send($message,['id'=>'user','body'=>'1']);if($logged[2]['body']!=='private body')throw new Exception('opt-in body');
$logResult=FALSE;if($send($message,['id'=>'user','body'=>'0'])===TRUE)throw new Exception('write failure hidden');
echo "channel log: minimal metadata, opt-in body, failure propagation passed\n";
