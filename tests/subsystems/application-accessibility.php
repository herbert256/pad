<?php
// Full PAD renders with adversarial child errors and custom record labels.
$root=dirname(__DIR__,2);
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
function renderCase($source,$errors=[],$posted=[],$method='POST'){
 global $root;$file=tempnam(sys_get_temp_dir(),'pad-a11y-source-');$boot=tempnam(sys_get_temp_dir(),'pad-a11y-boot-');
 file_put_contents($file,$source);file_put_contents($boot,'<?php $_SERVER["REQUEST_METHOD"]='.var_export($method,TRUE).';$_POST='.var_export($posted,TRUE).';$padFormErrors='.var_export($errors,TRUE).';$argv='.var_export(['editors/render.php','tags','--source='.$file],TRUE).';require '.var_export($root.'/editors/render.php',TRUE).';');
 $process=proc_open([PHP_BINARY,$boot],[1=>['pipe','w'],2=>['pipe','w']],$pipes,$root);$html=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);$status=proc_close($process);unlink($file);unlink($boot);verify($status===0&&$err==='','PAD render: '.substr($err.$html,0,1000));
 $doc=new DOMDocument();$before=libxml_use_internal_errors(TRUE);$doc->loadHTML($html);libxml_clear_errors();libxml_use_internal_errors($before);return [$doc,new DOMXPath($doc),$html];
}
[$doc,$xp,$html]=renderCase("{input:coordinatepicker 'coords'/}\n{widget:errorsummary/}",['coords[latitude]'=>'Latitude <outside> range','coords[longitude]'=>'Longitude outside range']);
foreach(['latitude','longitude'] as $axis){
 $input=$xp->query('//input[@name="coords['.$axis.']"]')->item(0);verify($input instanceof DOMElement,'coordinate input');
 verify($input->getAttribute('id')==='coords-'.$axis,'child address matches normal form ID');
 verify(!$input->hasAttribute('aria-label')&&!$input->hasAttribute('aria-labelledby'),'group name does not override axis label');
 verify($xp->query('//label[@for="coords-'.$axis.'"]')->item(0)->textContent===ucfirst($axis),'distinct visible native label');
 verify($input->getAttribute('aria-invalid')==='true','child error marks own input');
 $error=$input->getAttribute('aria-describedby');verify($xp->query('//*[@id="'.$error.'"]')->length===1,'error description exists');
 verify($xp->query('//a[@href="#coords-'.$axis.'"]')->length===1,'summary points at child');
}
verify(!str_contains($html,'<outside>'),'validation message escaped');
[$doc,$xp]=renderCase("{input:coordinatepicker 'delivery[coords]', label='Destination', disabled, required, step=0.25/}");
foreach(['latitude','longitude'] as $axis){$input=$xp->query('//input[@name="delivery[coords]['.$axis.']"]')->item(0);verify($input->getAttribute('id')==='delivery-coords-'.$axis,'nested child ID');verify($input->hasAttribute('disabled')&&$input->hasAttribute('required'),'shared native state');verify($input->getAttribute('step')==='0.25','author step retained for both axes');}
[$doc,$xp]=renderCase("{input:coordinatepicker 'point', id='custom', aria-describedby='help', value=['latitude'=>['bad'],'longitude'=>0]/}<p id='help'>Help</p>",['point[latitude]'=>'Correct latitude']);
$input=$xp->query('//input[@id="custom-latitude"]')->item(0);verify($input->getAttribute('aria-describedby')==='help custom-latitude-error','explicit help retained with child error');verify($input->getAttribute('value')==='','nonscalar default has no warning');verify($xp->query('//input[@id="custom-longitude"]')->item(0)->getAttribute('value')==='0','zero default retained');
[$doc,$xp]=renderCase("{input:coordinatepicker 'point', aria-label='Map position', value=['latitude'=>10,'longitude'=>20]/}",[],['point'=>['latitude'=>'91','longitude'=>['malformed']]]);
verify($xp->query('//fieldset[@aria-label="Map position"]')->length===1,'explicit shared accessible name belongs to group');verify($xp->query('//input[@id="point-latitude"]')->item(0)->getAttribute('value')==='91','invalid scalar post kept for correction');verify($xp->query('//input[@id="point-longitude"]')->item(0)->getAttribute('value')==='20','posted array is ignored safely');
[$doc,$xp]=renderCase("{input:coordinatepicker 'point'/}",['point[latitude]'=>'Wrong'],[],'GET');verify($xp->query('//input[@aria-invalid]')->length===0,'errors are suppressed before post');
[$doc,$xp]=renderCase("{input:form 'other'}{input:coordinatepicker 'point'/}{/input:form}",['point[latitude]'=>'Wrong'],['padForm'=>'sent']);verify($xp->query('//input[@aria-invalid]')->length===0,'errors stay within form that posted');
$record="{sheet:record 'quote', editable='B1:B2', labels=['B1'=>'Unit price','B2'=>'Quantity']}\nPrice,12\nQuantity,3\nTotal,=B1*B2\n{/sheet:record}";
[$doc,$xp]=renderCase($record);verify($xp->query('//input[@name="padSheet[quote][B1]"]')->item(0)->getAttribute('aria-label')==='Unit price (B1)','record label names input');
[$doc,$xp]=renderCase(str_replace(["sheet:record",", labels=['B1'=>'Unit price','B2'=>'Quantity']"],['sheet:grid',''],$record));verify($xp->query('//input[@name="padSheet[quote][B1]"]')->item(0)->getAttribute('aria-label')==='B1, Price','legacy grid label remains');
echo "Application accessibility: child errors/summary links, distinct coordinate labels, nested names, native attributes, post scoping, scalar defaults and record labels passed\n";
