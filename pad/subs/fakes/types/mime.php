<?php
  // mime - Seeded choice from a bounded MIME allowlist; no file inspection.
  $list=padFakeOpt('values',['text/plain','text/html','text/csv','application/json','application/pdf','image/png','image/jpeg','audio/mpeg']);
  if(!is_array($list)||!array_is_list($list)||!$list||count($list)>256){padError('fake:mime needs 1..256 MIME values');return NULL;}
  foreach($list as $item)if(!is_string($item)||strlen($item)>128||!preg_match('~^[A-Za-z0-9!#$&^_.+-]+/[A-Za-z0-9!#$&^_.+-]+$~D',$item)){padError('fake:mime values must be MIME type/subtype names');return NULL;}
  return $list[padFakeNumber(0,count($list)-1)];
?>
