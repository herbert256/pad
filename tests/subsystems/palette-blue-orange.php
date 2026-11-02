<?php
// Run directly with php; no web server required.
define('PAD', dirname ( __DIR__, 2 ) . '/pad/');
define('DATA', '/nonexistent-pad-test-data/');
require PAD . 'lib/dirs.php';
require PAD . 'lib/subsystem.php';
require PAD . 'subs/themes/palettes/_lib/palette.php';
function verify($ok, $message) { if (!$ok) throw new RuntimeException($message); }
$data=padPaletteData('blue-orange');
verify($data['type']==='diverging', 'classification');
foreach ([1, 2, 9, 64, 256] as $n) { $c=padPaletteColors('blue-orange', $n); verify(count($c)===$n, 'count'); foreach ($c as $v) verify((bool)preg_match('/^#[0-9a-f]{6}$/', $v), 'hex'); }
verify(padPaletteColors('blue-orange',9)[4]==='#f2f2f2','neutral midpoint'); $l=array_map('padPaletteLuminance',$data['colors']); for($i=0;$i<4;$i++) { verify(abs($l[$i]-$l[8-$i])<0.025,'balanced arms'); verify($l[$i]<$l[$i+1],'arm order'); }
echo "palette blue-orange: OK\n";
