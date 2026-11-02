<?php
// Minimal real-library bootstrap for standalone drawing regression probes.
define('PAD',dirname ( __DIR__, 2 ).'/pad/');define('DATA','/nonexistent-pad-test-data/');
require PAD.'lib/dirs.php';require PAD.'lib/subsystem.php';require PAD.'lib/encoding.php';require PAD.'lib/chart.php';require PAD.'lib/security.php';require PAD.'subs/diagrams/_lib/diagram.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
function svgValid($svg){$d=new DOMDocument();verify($d->loadXML($svg),'valid SVG');return $d;}
function visualNormalize($svg){return preg_replace('/pad-chart-[a-f0-9]{8}(?:-\d+)?/','chart',$svg);}
