<?php
  // log - Local diagnostic delivery. Body logging is opt-in; arbitrary message data is never logged.
  return ['settings'=>['id'=>['to'=>'id|email','default'=>'anonymous'],'body'=>['default'=>'0','pattern'=>'/^[01]$/D','is'=>'0 or 1']],
    'send'=>function($message,$settings){
      $context=['channel'=>'log','notification'=>substr((string)($message['name']??''),0,128),
        'recipient'=>substr((string)$settings['id'],0,128),'id'=>substr((string)($message['id']??''),0,128)];
      if((string)$settings['body']==='1')$context['body']=substr((string)($message['text']??''),0,4096);
      return padLog('Notification {notification} for {recipient}','info',$context) ? TRUE : 'the notification could not be written to the local log';
    }];
?>
