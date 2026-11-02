<?php
// Run directly with php; no web server required.
define('PAD', dirname(__DIR__, 3) . '/pad/');
define('DATA', '/nonexistent-pad-test-data/');
require PAD . 'lib/dirs.php';
require PAD . 'lib/subsystem.php';
require PAD . 'subs/palettes/_lib/palette.php';
function verify($ok, $message) { if (!$ok) throw new RuntimeException($message); }
$data=padPaletteData('amber');
verify($data['type']==='sequential', 'classification');
foreach ([1, 2, 9, 64, 256] as $n) { $c=padPaletteColors('amber', $n); verify(count($c)===$n, 'count'); foreach ($c as $v) verify((bool)preg_match('/^#[0-9a-f]{6}$/', $v), 'hex'); }
$l=array_map('padPaletteLuminance', padPaletteColors('amber',256)); for($i=1;$i<count($l);$i++) verify($l[$i]<$l[$i-1], 'strict luminance order');
echo "palette amber: OK\n";
