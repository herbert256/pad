// Native browser labels, validation descriptions and summary focus; no server needed.
const fs=require('node:fs'),path=require('node:path'),os=require('node:os'),cp=require('node:child_process'),assert=require('node:assert/strict');
const root=path.resolve(__dirname,'../../..'),tmp=fs.mkdtempSync(path.join(os.tmpdir(),'pad-application-a11y-'));
const source=path.join(tmp,'form.pad'),boot=path.join(tmp,'render.php'),page=path.join(tmp,'form.html');
fs.writeFileSync(source,`{input:form 'location'}
{input:coordinatepicker 'coords', label='Location', value=['latitude'=>10,'longitude'=>20]/}
{widget:errorsummary/}
<button type="reset" id="reset">Reset</button>
{/input:form}
{sheet:record 'quote', editable='B1', labels=['B1'=>'Unit price']}
Price,12
{/sheet:record}`);
const phpQuote=s=>"'"+s.replaceAll('\\','\\\\').replaceAll("'","\\'")+"'";
fs.writeFileSync(boot,`<?php $_SERVER['REQUEST_METHOD']='POST';$_POST=['padForm'=>'location','coords'=>['latitude'=>'91','longitude'=>'181']];$padFormErrors=['coords[latitude]'=>'Latitude outside range','coords[longitude]'=>'Longitude outside range'];$argv=['editors/render.php','tags','--source='.${phpQuote(source)}];require ${phpQuote(root+'/editors/render.php')};`);
const html=cp.execFileSync('php',[boot],{cwd:root,encoding:'utf8'}).replace(/<script[^>]*src="[^"]*"[^>]*>[\s\S]*?<\/script>/g,'').replace(/<link[^>]*>/g,'');
assert.ok(html.startsWith('<!DOCTYPE html>'),html.slice(0,500));fs.writeFileSync(page,html);
const chrome=process.env.PAD_CHROME||'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const child=cp.spawn(chrome,['--headless=new','--no-sandbox','--disable-gpu','--no-first-run','--no-default-browser-check','--remote-debugging-pipe','--user-data-dir='+path.join(tmp,'profile')],{stdio:['ignore','ignore','pipe','pipe','pipe']});
let serial=0,buffer='',errors='',pending=new Map();child.stderr.on('data',d=>errors+=d.toString());
child.stdio[4].on('data',data=>{buffer+=data.toString();let end;while((end=buffer.indexOf('\0'))>=0){const msg=JSON.parse(buffer.slice(0,end));buffer=buffer.slice(end+1);if(msg.id&&pending.has(msg.id)){const {resolve,reject}=pending.get(msg.id);pending.delete(msg.id);msg.error?reject(Error(JSON.stringify(msg.error))):resolve(msg.result);}}});
function send(method,params={},sessionId){const id=++serial;return new Promise((resolve,reject)=>{pending.set(id,{resolve,reject});child.stdio[3].write(JSON.stringify({id,method,params,...(sessionId?{sessionId}:{})})+'\0');});}
const timeout=setTimeout(()=>{console.error('Timed out: '+errors.slice(-1000));child.kill();process.exit(1);},25000);
(async()=>{try{
 const {targetId}=await send('Target.createTarget',{url:'about:blank'}),{sessionId}=await send('Target.attachToTarget',{targetId,flatten:true}),call=(m,p={})=>send(m,p,sessionId);
 await call('Page.enable');await call('Page.navigate',{url:'file://'+page});
 for(let i=0;i<80;i++){const r=await call('Runtime.evaluate',{expression:'document.readyState',returnByValue:true});if(r.result.value==='complete')break;await new Promise(resolve=>setTimeout(resolve,25));}
 const {nodes}=await call('Accessibility.getFullAXTree');fs.writeFileSync(path.join(tmp,'accessibility.json'),JSON.stringify(nodes,null,2));
 const byName=(role,name)=>nodes.find(n=>!n.ignored&&n.role?.value===role&&n.name?.value===name);
 for(const axis of ['Latitude','Longitude']){const n=byName('spinbutton',axis);assert.ok(n,axis+' distinct accessible name');assert.equal(n.description?.value,axis+' outside range');assert.equal(n.properties.find(p=>p.name==='invalid')?.value?.value,'true');}
 assert.ok(byName('group','Location'),'fieldset group label');assert.ok(byName('textbox','Unit price (B1)'),'record custom accessible name');
 const result=await call('Runtime.evaluate',{expression:`JSON.stringify((()=>{
  const input=document.getElementById('coords-latitude');document.querySelector('a[href="#coords-latitude"]').click();const summaryFocus=document.activeElement===input;
  document.querySelector('label[for="coords-longitude"]').click();const nativeLabelFocus=document.activeElement.id==='coords-longitude';
  input.value='45';document.getElementById('reset').click();const reset=input.value==='91';
  const values=Object.fromEntries(new FormData(input.form));return {summaryFocus,nativeLabelFocus,reset,latitude:values['coords[latitude]'],longitude:values['coords[longitude]'],invalid:!input.checkValidity()};
 })())`,returnByValue:true});
 const state=JSON.parse(result.result.value);assert.deepEqual(state,{summaryFocus:true,nativeLabelFocus:true,reset:true,latitude:'91',longitude:'181',invalid:true});fs.writeFileSync(path.join(tmp,'interaction.json'),JSON.stringify(state,null,2));
 console.log('Application accessibility browser: AX names/error descriptions, native summary/label focus, reset, form values and bounds passed; evidence '+tmp);
 await send('Browser.close');
}finally{clearTimeout(timeout);child.kill();}})().catch(error=>{console.error(error);process.exitCode=1;});
