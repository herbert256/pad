<?php
const PAD = __DIR__.'/../../pad/';
const APPS = __DIR__.'/../../apps/';
function padWidgetOnce($s){return false;}
function padUnprotectQuotes($s){return $s;}
require PAD.'subs/images/avatars/_lib/avatar.php';
function check($ok){if(!$ok)throw new Exception('Avatar assertion failed');}
foreach(['rings','mosaic','pixelbot'] as $kind){
 $a=padAvatarMake($kind,'secret@example.com',['label'=>'<Ada & Jo>']);
 check($a===padAvatarMake($kind,'secret@example.com',['label'=>'<Ada & Jo>']));
 check(!str_contains($a,'secret@') && str_contains($a,'&lt;Ada &amp; Jo&gt;'));
 $xml=new DOMDocument();check($xml->loadXML($a));
 check($a!==padAvatarMake($kind,'other',['label'=>'<Ada & Jo>']));
}
check(padAvatarMake('initials','Ada Lovelace',['size'=>48])===padAvatar('Ada Lovelace'));
check(padAvatarMake('identicon',' ADA ',['size'=>64])===padIdenticon(' ADA '));
try{padAvatarMake('rings','a',['palette'=>['url(x)','#ffffff']]);throw new Exception('Accepted unsafe color');}catch(InvalidArgumentException $e){}
// The old include path must remain safe after the canonical module has loaded.
include_once PAD.'subs/images/avatars/_lib/initials.php';
check(str_contains(padAvatarMake('initials','Ada'),'width="64"'));
check(str_contains(padAvatarMake('identicon','ada',['title'=>'Title','label'=>'Alias']),'aria-label="Title"'));
$expected=[
 'rings'=>'74c9375abaf5b6be4da14d0837e23ede3891504820e3340e8395ad8b44c04394',
 'mosaic'=>'ca6901911e35bd31a2655b7a089d523597717a9a016adb0a1b65d34755345bf7',
 'pixelbot'=>'9d74df77b32320536781adcc7b4b4fbcecce3d36771f714fa4a77f2a2039c027'
];
foreach($expected as $kind=>$hash){
 $options=['size'=>72,'label'=>'Ada <&>','palette'=>['#123456','#fedcba','#00aa77']];
 check(hash('sha256',padAvatarMake($kind,'account-17',$options))===$hash);
 check(padAvatarMake($kind,' Ada ')===padAvatarMake($kind,'Ada'));
 check(padAvatarMake($kind,'Ada')!==padAvatarMake($kind,'ada'));
 if(class_exists('Normalizer'))check(padAvatarMake($kind,"A\u{030A}sa")===padAvatarMake($kind,"Åsa"));
}
check(padAvatarMake('geometric',' ADA ',['style'=>'mosaic','label'=>'Ada'])===padAvatarGeometric('Ada',64,'mosaic','Ada'));
check(padAvatarMake('mosaic','Ada',['label'=>'Ada'])!==padAvatarMake('geometric','Ada',['style'=>'mosaic','label'=>'Ada']));
foreach(['avatar','identicon'] as $fallback){
 $local=padAvatarMake('gravatar','ada@example.org',['fallback'=>$fallback,'alt'=>'Ada']);
 check(str_contains($local,'<svg ')&&!str_contains($local,'gravatar.com'));
}
check(padAvatarMake('photo','missing.jpg',['alt'=>'Ada Lovelace'])===padAvatar('Ada Lovelace',64,'circle','Ada Lovelace'));
$remote=padAvatarMake('photo','https://example.org/Ada.png',['alt'=>'<Ada>','shape'=>'square']);
check(str_contains($remote,'src="https://example.org/Ada.png"')&&str_contains($remote,'&lt;Ada&gt;')&&str_contains($remote,'pad-avatar-photo-square'));
check(str_contains(padAvatarMake('rings','a',['size'=>9999]),'width="1024"'));
check(str_contains(padAvatarMake('geometric','a',['size'=>9999]),'width="2048"'));
foreach([['photo','fallback','invalid'],['geometric','style','invalid'],['gravatar','default','javascript:bad']] as [$kind,$key,$value]){
 try{padAvatarMake($kind,'a',[$key=>$value]);throw new Exception('Accepted invalid option');}catch(InvalidArgumentException $e){}
}
echo "avatars: eight styles, legacy helper parity, original artwork hashes, aliases and safe fallbacks passed\n";
