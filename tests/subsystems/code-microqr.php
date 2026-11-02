<?php
  define('PAD',dirname(__DIR__,2).'/pad/');
  function padUnprotectQuotes($value) { return $value; }
  require PAD.'subs/codes/_lib/barcode.php';
  require PAD.'subs/codes/_lib/microqr.php';
  function check($ok,$why) { if(!$ok) throw new Exception($why); }
  $fixtures=json_decode(file_get_contents(__DIR__.'/fixtures/microqr-zint.json'),true);
  foreach($fixtures as $i=>$case) {
    $code=padMicroQrEncode($case['text'],$case['options']);
    check(is_array($code),'encoding failed at '.$i);
    check(array_map(fn($row)=>implode('',$row),$code['matrix'])===$case['matrix'],'Zint matrix mismatch at '.$i);
    check($code['mask']===$case['mask'],'mask mismatch at '.$i);
  }
  $capacities=['numeric'=>[1=>['none'=>5],2=>['L'=>10,'M'=>8],3=>['L'=>23,'M'=>18],4=>['L'=>35,'M'=>30,'Q'=>21]],
    'alnum'=>[2=>['L'=>6,'M'=>5],3=>['L'=>14,'M'=>11],4=>['L'=>21,'M'=>18,'Q'=>13]],
    'byte'=>[3=>['L'=>9,'M'=>7],4=>['L'=>15,'M'=>13,'Q'=>9]],
    'kanji'=>[3=>['L'=>6,'M'=>4],4=>['L'=>9,'M'=>8,'Q'=>5]]];
  foreach($capacities as $mode=>$versions) foreach($versions as $v=>$levels) foreach($levels as $level=>$max) {
    $char=['numeric'=>'1','alnum'=>'A','byte'=>'a','kanji'=>'漢'][$mode];
    check(is_string(padMicroQrEncode(str_repeat($char,$max+1),['version'=>"M$v",'level'=>$level,'mode'=>$mode])),'overflow accepted');
  }
  check(padMicroQrEncode('12345')['version']==='M1','auto M1');
  check(padMicroQrEncode('123456')['version']==='M2','auto M2');
  check(padMicroQrEncode('abc')['version']==='M3','auto byte');
  check(padMicroQrEncode('漢字')['mode']==='kanji','auto Kanji');
  foreach([['',['level'=>'auto']],['abc',['version'=>'M1']],['1',['level'=>'H']],['1',['mask'=>4]],['x',['mode'=>'numeric']],['x',['version'=>'M2']],['é',['mode'=>'kanji']],['€',['mode'=>'byte','encoding'=>'ISO-8859-1']]] as [$text,$options])
    check(is_string(padMicroQrEncode($text,$options)),'invalid options accepted');
  $code=padMicroQrEncode('12345');
  $svg=padCodeMatrix($code['matrix'],2,128,'A < B & "C"','#000','#fff','pad-microqr');
  check(str_contains($svg,'role="img"') and str_contains($svg,'A &lt; B &amp; &quot;C&quot;'),'accessible SVG title escaping');
  echo 'Micro QR: '.count($fixtures)." Zint matrices/masks, all capacities, validation and SVG passed\n";
?>
