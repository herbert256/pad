<?php
 define('PAD',dirname(__DIR__,2).'/pad/');function padDirList($p){return scandir($p);}require PAD.'lib/subsystem.php';require PAD.'subs/themes/_lib/theme.php';$count=0;
 foreach(padThemeTypes() as $name){foreach(padThemeContrasts($name) as $pair=>$value){if($value<padThemeNeeds($name))throw new Exception("$name $pair: $value");$count++;}}
 foreach(padPaletteTypes() as $name){$meta=padPaletteMetadata($name);if(!$meta||!isset($meta['light'],$meta['dark'],$meta['usage']))throw new Exception('Missing metadata '.$name);if(padPaletteSelect($name,$meta['type'],3)!==padPaletteColors($name,3))throw new Exception('Interpolation changed');}
 foreach([['viridis','categorical',3],['okabe-ito','categorical',100],['rdbu','cyclic',3]] as [$name,$type,$steps]){try{padPaletteSelect($name,$type,$steps);throw new Exception('Expected palette mismatch');}catch(InvalidArgumentException $e){}}
 if(in_array('--source',$argv,TRUE))foreach(padThemeTypes() as $name){
  echo "{widget:theme '$name'}<h2>$name</h2><p class=\"matrix-text\">Readable interface text</p><a class=\"matrix-link\" href=\"#matrix\">Focusable link</a>\n";
  echo "{input:unitpicker 'matrix-$name', value=12, label='Quantity'/}\n{input:unitpicker 'disabled-$name', value=12, disabled, label='Disabled quantity'/}\n{widget:stat 42, label='Completed', baseline='Yesterday: 40', delta=2/}\n";
  echo '<p class="matrix-status" style="color:var(--ok);background:var(--ok-bg)">Operational</p><p class="matrix-status" style="color:var(--bad);background:var(--bad-bg)">Error: action required</p>{/widget:theme}',"\n";
 }else echo "Theme matrix: $count contrast pairs and ".count(padPaletteTypes())." palette metadata records passed\n";
