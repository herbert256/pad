<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/format.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$padCheckSyntax=TRUE;
$rows=[['name'=>"é\t\n\r  text",'value'=>12.5,'flag'=>TRUE],['name'=>'=SUM(A1:A2)','value'=>NULL,'flag'=>FALSE]];$bytes=padFormatWrite('ods',$rows,['sheet'=>'Data']);check(padFormatRead('ods',$bytes,['sheet'=>'Data'])===$rows);check($bytes===padFormatWrite('ods',$rows,['sheet'=>'Data']));
// Independent package fixture: constructed directly from ODF namespace/scalar records, not PAD's writer.
function fixture($cells){$xml='<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" office:version="1.3"><office:body><office:spreadsheet><table:table table:name="External">'.$cells.'</table:table></office:spreadsheet></office:body></office:document-content>';$f=tempnam(sys_get_temp_dir(),'ods-test-');$z=new ZipArchive();$z->open($f,ZipArchive::OVERWRITE);$z->addFromString('mimetype','application/vnd.oasis.opendocument.spreadsheet');$z->setCompressionName('mimetype',ZipArchive::CM_STORE);$z->addFromString('content.xml',$xml);$z->close();$b=file_get_contents($f);unlink($f);return $b;}
$b=fixture('<table:table-row table:number-rows-repeated="2"><table:table-cell office:value-type="string"><text:p>a<text:s text:c="2"/>b</text:p></table:table-cell><table:table-cell office:value-type="boolean" office:boolean-value="true"/><table:table-cell office:value-type="float" office:value="42" table:formula="of:=SUM([.A1])"/></table:table-row>');rejects(fn()=>padFormatRead('ods',$b,['header'=>'none']));$r=padFormatRead('ods',$b,['header'=>'none','formulas'=>'cached']);check(count($r)===2&&$r[0]===['column1'=>'a  b','column2'=>TRUE,'column3'=>42]);check(padFormatRead('ods',$b,['header'=>'none','formulas'=>'text'])[0]['column3']==='of:=SUM([.A1])');rejects(fn()=>padFormatRead('ods',fixture('<table:table-row table:number-rows-repeated="1000000"><table:table-cell/></table:table-row>'),['header'=>'none']));rejects(fn()=>padFormatWrite('ods',[['x'=>INF]]));check(substr($bytes,30,8)==='mimetype');
rejects(fn()=>padFormatRead('ods',fixture('<table:table-row><table:table-cell office:value-type="date" office:date-value="2026-02-31"/></table:table-row>'),['header'=>'none']));
$b=fixture('<table:table-row-group><table:table-row><table:table-cell office:value-type="float" office:value="7"/></table:table-row></table:table-row-group>');check(padFormatRead('ods',$b,['header'=>'none'])===[['column1'=>7]]);
echo "formats:ods fixtures passed\n";
