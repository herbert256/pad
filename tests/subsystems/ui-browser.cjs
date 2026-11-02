/* Reusable isolated Chrome checks. Run: node tests/subsystems/ui-browser.cjs
   Requires PHP, Chrome (or PAD_CHROME) and all integrated subsystem registry entries. */
const fs=require('node:fs'),path=require('node:path'),os=require('node:os'),cp=require('node:child_process'),assert=require('node:assert/strict');
const root=path.resolve(__dirname,'../..'),tmp=fs.mkdtempSync(path.join(os.tmpdir(),'pad-ui-browser-'));
const families={avatar:['styles'],input:['currency','unitpicker','repeatable'],widget:['treeview','kanban','imagecompare'],printable:['planner','flashcards','booklet'],section:['casestudy','careers','roadmap'],effect:['flipnumber','ripple'],game:['minesweeper','connectfour','sokoban'],toy:['lorenz','optics','pathfinding'],calendar:['timegrid','resources']};
let source='';for(const [family,names] of Object.entries(families))for(const name of names)source+='<section id="test-'+name+'"><h2>'+family+':'+name+'</h2>'+fs.readFileSync(path.join(root,'apps/tags/examples',family,name+'.pad'),'utf8')+'</section>\n';
const input=path.join(tmp,'all.pad');fs.writeFileSync(input,source);
let html=cp.execFileSync('php',['editors/render.php','tags','--source='+input],{cwd:root,env:{...process.env,PAD_HOME:root},encoding:'utf8',maxBuffer:20e6});assert.ok(html.startsWith('<!DOCTYPE html>'),html.slice(0,500));
html=html.replace(/<script[^>]*src="[^"]*"[^>]*>[\s\S]*?<\/script>/g,'').replace(/<link[^>]*>/g,'').replace(/src="(\/assets\/[^"]+)"/g,(_,p)=>'src="file://'+root+'/www'+p+'"');
const test=fs.readFileSync(path.join(__dirname,'ui-browser.js'),'utf8'),chrome=process.env.PAD_CHROME||'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const flags=['--headless=new','--no-sandbox','--disable-gpu','--no-first-run','--user-data-dir='+path.join(tmp,'profile'),'--virtual-time-budget=3500','--dump-dom'];
let count=0;
for(const reduced of [false,true]){
 const file=path.join(tmp,reduced?'reduced.html':'normal.html');fs.writeFileSync(file,html.replace('<head>','<head><script>window.expectedReduced='+reduced+';'+test+'</script>'));
 const result=cp.spawnSync(chrome,[...flags,...(reduced?['--force-prefers-reduced-motion']:[]),'file://'+file],{encoding:'utf8',maxBuffer:30e6,timeout:30000});fs.writeFileSync(path.join(tmp,reduced?'reduced.log':'normal.log'),result.stderr||'');assert.equal(result.status,0,'Chrome failed: '+result.error);
 const match=result.stdout.match(/<pre id="ui-results">([\s\S]*?)<\/pre>/);assert.ok(match,'Missing browser result; logs at '+tmp);
 const checks=JSON.parse(match[1].replaceAll('&amp;','&').replaceAll('&lt;','<').replaceAll('&gt;','>'));const failed=checks.filter(c=>!c.ok);console.log((reduced?'reduced motion':'normal motion')+': '+(checks.length-failed.length)+'/'+checks.length);if(failed.length)console.log(JSON.stringify(failed,null,2));assert.equal(failed.length,0,'Browser failures; fixture at '+file);count+=checks.length;
}
const native=path.join(tmp,'native.html');fs.writeFileSync(native,html.replace(/<script[^>]*>[\s\S]*?<\/script>/g,''));const nojs=cp.spawnSync(chrome,[...flags,'file://'+native],{encoding:'utf8',maxBuffer:30e6,timeout:30000});fs.writeFileSync(path.join(tmp,'native-dom.html'),nojs.stdout);fs.writeFileSync(path.join(tmp,'native.log'),nojs.stderr||'');console.log('No-script fixture: '+tmp);assert.equal(nojs.status,0);assert.ok(nojs.stdout.includes('name="distance[value]"'));assert.ok(nojs.stdout.includes('name="lines[0][description]"'));assert.ok(!/<(?:div|section)[^>]*class="[^\"]*pad-(?:input|game|toy)[^\"]*is-ready/.test(nojs.stdout),'No-script fixture unexpectedly enhanced');assert.ok(nojs.stdout.includes('Room A'));console.log('no JavaScript: native nested fields, content and disabled enhancement passed');
console.log(count+' browser assertions passed; fixtures and logs: '+tmp);
