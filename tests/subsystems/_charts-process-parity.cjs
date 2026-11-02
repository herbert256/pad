const assert=require('node:assert/strict'),fs=require('node:fs'),vm=require('node:vm'),cp=require('node:child_process');
globalThis.window=undefined;vm.runInThisContext(fs.readFileSync(__dirname+'/../../www/assets/charts/core.js','utf8'));for(const f of ['process-frame',process.argv[2]])vm.runInThisContext(fs.readFileSync(__dirname+'/../../www/assets/charts/'+f+'.js','utf8'));
const enc=v=>v!==null&&typeof v==='object'?{m:Object.entries(v).map(([k,x])=>[k,enc(x)])}:v;
const kind=process.argv[2],cases=JSON.parse(fs.readFileSync(__dirname+'/charts-'+kind+'.json','utf8'));
const norm=s=>s.replace(/<style[\s\S]*?<\/style>/g,'').replace(/pad-chart-[a-z0-9-]+-(title|desc)/g,'ID-$1').replace(/id="[^"]+"/g,'id="ID"');
for(const c of cases){c.kind=kind;const php=cp.execFileSync('php',[__dirname+'/_charts-process-fixture.php'],{input:JSON.stringify(c),encoding:'utf8'});let js;try{js=PadChart.render({...c,rows:enc(c.rows),id:'test',check:true});}catch(e){js='ERROR:'+e.message;}if(php.startsWith('ERROR:'))assert(js.startsWith('ERROR:'),js);else assert.equal(norm(js).replace(/test-(title|desc)/g,'ID-$1'),norm(php));}console.log(kind+' PHP/browser SVG parity '+cases.length+' fixtures OK');
