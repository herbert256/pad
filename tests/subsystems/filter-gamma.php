<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padError ( $m ) { throw new InvalidArgumentException ( $m ); }
  function padMakeSafe ( $s, $n ) { return substr($s,0,$n); }
  $padCheckSyntax = TRUE;
  require PAD . 'lib/subsystem.php'; require PAD . 'subs/images/filters/_lib/filter.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'filter fixture failed' ); }
  function pixelImage($pixel) { $i=imagecreatetruecolor(1,1); imagealphablending($i,false); imagesetpixel($i,0,0,$pixel); return $i; }
  $def=padFilterKind('gamma'); $pixel=(63<<24)|0x4080ff;
  check(imagecolorat($def['apply'](pixelImage($pixel),['gamma'=>1],1),0,0)===$pixel);
  $out=imagecolorat($def['apply'](pixelImage($pixel),['gamma'=>2],2),0,0);
  check(($out>>24)===63 and (($out>>16)&255)===128 and (($out>>8)&255)===181 and ($out&255)===255);
  foreach([NAN,INF,0,-1] as $bad) { try { padFilterOption('gamma','gamma',$def['options']['gamma'],$bad); throw new Exception('invalid gamma accepted'); } catch(InvalidArgumentException $e) {} }
  echo "filter:gamma pixel, alpha and option fixtures passed\n";
?>
