<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  function padMakeSafe ( $s, $n ) { return substr($s,0,$n); }
  $padCheckSyntax = TRUE;
  require PAD . 'lib/subsystem.php'; require PAD . 'subs/filters/_lib/filter.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'filter fixture failed' ); }
  function pixelImage($pixel) { $i=imagecreatetruecolor(1,1); imagealphablending($i,false); imagesetpixel($i,0,0,$pixel); return $i; }
  $def=padFilterKind('emboss');
  foreach([1,1.5,2] as $scale) { $out=$def['apply'](pixelImage((70<<24)|0xffcc33),['strength'=>2],$scale); check(imagecolorat($out,0,0)===((70<<24)|0x808080)); }
  $source=imagecreatetruecolor(3,3); imagealphablending($source,false);
  for($y=0;$y<3;$y++) for($x=0;$x<3;$x++) imagesetpixel($source,$x,$y,(30<<24)|(($x*100)<<16)|(($x*100)<<8)|($x*100));
  $out=$def['apply']($source,['strength'=>1],1); check((imagecolorat($out,1,1)&255)>128 and (imagecolorat($out,1,1)>>24)===30);
  check((imagecolorat($source,1,1)&255)===100);
  echo "filter:emboss pixel, alpha and option fixtures passed\n";
?>
