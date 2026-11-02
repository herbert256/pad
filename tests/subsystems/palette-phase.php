<?php
// Run directly with php; no web server required.
define('PAD', dirname ( __DIR__, 2 ) . '/pad/');
define('DATA', '/nonexistent-pad-test-data/');
require PAD . 'lib/dirs.php';
require PAD . 'lib/subsystem.php';
require PAD . 'subs/palettes/_lib/palette.php';
function verify($ok, $message) { if (!$ok) throw new RuntimeException($message); }
$data=padPaletteData('phase');
verify($data['type']==='cyclic', 'classification');
foreach ([1, 2, 9, 64, 256] as $n) { $c=padPaletteColors('phase', $n); verify(count($c)===$n, 'count'); foreach ($c as $v) verify((bool)preg_match('/^#[0-9a-f]{6}$/', $v), 'hex'); }
verify(padPaletteRound($data['colors'],0)===padPaletteRound($data['colors'],1),'wrap'); foreach([8,64,256] as $n){$c=padPaletteColors('phase',$n); $dist=fn($a,$b)=>sqrt(array_sum(array_map(fn($x,$y)=>($x-$y)**2,padPaletteRgb($a),padPaletteRgb($b)))); $steps=[];for($i=1;$i<$n;$i++)$steps[]=$dist($c[$i-1],$c[$i]);verify($dist(end($c),$c[0])<=max($steps)*1.2+2,'seam no larger than other steps');}
echo "palette phase: OK\n";
