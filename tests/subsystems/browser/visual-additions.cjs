// Explicit browser checks: node tests/subsystems/browser/visual-additions.cjs
const fs=require('node:fs'),path=require('node:path'),os=require('node:os'),cp=require('node:child_process'),assert=require('node:assert/strict');
const root=path.resolve(__dirname,'../../..'),tmp=fs.mkdtempSync(path.join(os.tmpdir(),'pad-visual-browser-'));
const families={chart:['ribbon','lollipop','ternary'],diagram:['rack','petri'],game:['2048','breakout'],toy:['fourier','waveinterference'],icon:['calendar-check','file-search','route','network'],image:['socialcard','median'],pattern:['penrose','interlace'],notation:['pgn','shogi'],printable:['packinglist','attendance'],sound:['pluck','brush'],avatar:['composition']};
let source='';for(const [family,names]of Object.entries(families))for(const name of names){const s=fs.readFileSync(path.join(root,'apps/tags/examples',family,name+'.pad'),'utf8');source+='<section id="test-'+name+'"><h2>'+family+':'+name+'</h2>'+s+'</section>\n';if(family==='chart')source+='<section id="client-'+name+'">'+s.replace('{chart:'+name,'{chart:'+name+' client,')+'</section>\n';}
source+='<section id="second-2048">{game:2048 seed=19/}</section>';
const input=path.join(tmp,'all.pad');fs.writeFileSync(input,source);
let html=cp.execFileSync('php',['editors/render.php','tags','--source='+input],{cwd:root,encoding:'utf8',maxBuffer:20e6});assert.ok(html.startsWith('<!DOCTYPE html>'),html.slice(0,1000));
html=html.replace(/<script[^>]*src="[^"]*"[^>]*>[\s\S]*?<\/script>/g,'').replace(/<link[^>]*>/g,'').replace(/src="(\/[^" ]+)"/g,(_,p)=>'src="file://'+root+'/www'+p+'"');
const script=String.raw`
window.addEventListener('load',async function(){const checks=[];function ok(name,value){checks.push({name,ok:!!value});}function test(name,fn){try{ok(name,fn());}catch(e){checks.push({name,ok:false,error:String(e)});}}const q=s=>document.querySelector(s),all=s=>Array.from(document.querySelectorAll(s));
function button(root,text){return Array.from(root.querySelectorAll('button')).find(b=>b.textContent===text);}
function shape(svg){return svg.outerHTML.replace(/pad-chart-[a-z0-9]+(?:-\d+)?/g,'chart').replace(/<style[\s\S]*?<\/style>/g,'');}
for(const kind of ['ribbon','lollipop','ternary'])test(kind+' client and server geometry',()=>shape(q('#test-'+kind+' svg'))===shape(q('#client-'+kind+' svg')));
test('all game instances enhanced',()=>all('.pad-game.is-ready').length===3);
const first=q('#test-2048 .pad-game'),second=q('#second-2048 .pad-game'),cells=root=>Array.from(root.querySelectorAll('.pg-2048-cell')).map(e=>e.textContent).join(',');const original=cells(first),other=cells(second);
test('2048 keyboard move',()=>{first.dispatchEvent(new KeyboardEvent('keydown',{key:'ArrowLeft',bubbles:true,cancelable:true}));return cells(first)!==original;});
test('2048 independent instances',()=>cells(second)===other);
test('2048 deterministic reset',()=>{button(first,'Reset').click();return cells(first)===original;});
const breakout=q('#test-breakout .pad-game');test('Breakout begins paused',()=>button(breakout,'Play')&&breakout.querySelector('canvas').width===480);
test('Breakout explicit play and pause',()=>{button(breakout,'Play').click();const playing=button(breakout,'Pause');if(!playing)return false;playing.click();return !!button(breakout,'Play');});
test('Breakout pointer and keyboard reset',()=>{const c=breakout.querySelector('canvas');c.dispatchEvent(new PointerEvent('pointerdown',{clientX:100,clientY:100,pointerId:1}));breakout.dispatchEvent(new KeyboardEvent('keydown',{key:'ArrowRight',bubbles:true}));button(breakout,'Reset').click();return breakout.querySelector('.pg-status').textContent.includes('3 lives');});
for(const kind of ['fourier','waveinterference'])test(kind+' starts paused and has keyboard-accessible sliders',()=>q('#test-'+kind+' .pad-toy').getAttribute('data-state')==='paused'&&all('#test-'+kind+' input[type=range]').length>0);
test('Fourier harmonic slider updates numeric error',()=>{const s=q('#test-fourier input[type=range]');s.value=1;s.dispatchEvent(new Event('input',{bubbles:true}));return q('#test-fourier').textContent.includes('1 harmonics');});
test('Wave phase slider updates units',()=>{const s=all('#test-waveinterference input[type=range]')[1];s.value=90;s.dispatchEvent(new Event('input',{bubbles:true}));return q('#test-waveinterference').textContent.includes('90°');});
test('socialcard PNG has declared dimensions and decoded',()=>{const im=q('#test-socialcard img');return im.naturalWidth===1200&&im.naturalHeight===630&&im.alt.includes('practical');});
test('notation initial board and piece inventories rendered',()=>q('#test-pgn .pad-chess')&&q('#test-shogi svg desc').textContent.includes('sente king'));
test('packing and attendance printable pages',()=>all('#test-packinglist .pp-sheet').length===1&&all('#test-attendance .pp-sheet').length===1);
test('printable forms carry repeated header content',()=>q('#test-packinglist').textContent.includes('Quantity')&&q('#test-attendance').textContent.includes('11-02'));
test('sound samples exposed without playback',()=>PadSound.render('pluck').length>0&&PadSound.render('brush').length>0);
test('no client chart error',()=>!q('.pad-chart-error'));
test('Breakout pauses on hidden-tab event',()=>{button(breakout,'Play').click();Object.defineProperty(document,'hidden',{configurable:true,value:true});document.dispatchEvent(new Event('visibilitychange'));const paused=!!button(breakout,'Play');delete document.hidden;document.dispatchEvent(new Event('visibilitychange'));return paused;});
const toy=q('#test-fourier .pad-toy'),kit=toy.padToyKit;kit.play();toy.remove();await new Promise(resolve=>setTimeout(resolve,30));test('toy unmount stops its loop',()=>!kit.playing&&!kit.raf);
button(breakout,'Play').click();breakout.remove();await new Promise(resolve=>setTimeout(resolve,30));test('game unmount cancels play',()=>!!button(breakout,'Play'));

const output=document.createElement('pre');output.id='visual-results';output.textContent=JSON.stringify(checks);document.body.appendChild(output);
});`;
const chrome=process.env.PAD_CHROME||'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';let total=0;
for(const reduced of [false,true]){const file=path.join(tmp,reduced?'reduced.html':'normal.html');fs.writeFileSync(file,html.replace('<head>','<head><script>'+script+'</script>'));const result=cp.spawnSync(chrome,['--headless=new','--no-sandbox','--disable-gpu','--no-first-run','--user-data-dir='+path.join(tmp,reduced?'profile-reduced':'profile-normal'),'--virtual-time-budget=3000','--dump-dom',...(reduced?['--force-prefers-reduced-motion']:[]),'file://'+file],{encoding:'utf8',maxBuffer:30e6,timeout:30000});fs.writeFileSync(path.join(tmp,reduced?'reduced.log':'normal.log'),result.stderr||'');fs.writeFileSync(path.join(tmp,reduced?'reduced-dom.html':'normal-dom.html'),result.stdout||'');assert.equal(result.status,0,'Chrome failed: '+result.error+' '+result.stderr);const match=result.stdout.match(/<pre id="visual-results">([\s\S]*?)<\/pre>/);assert.ok(match,'Missing browser results at '+tmp);const checks=JSON.parse(match[1].replaceAll('&amp;','&').replaceAll('&lt;','<').replaceAll('&gt;','>')),failed=checks.filter(x=>!x.ok);console.log((reduced?'reduced':'normal')+': '+(checks.length-failed.length)+'/'+checks.length);if(failed.length)console.log(failed);assert.equal(failed.length,0,'Browser failures: '+tmp);total+=checks.length;}
console.log(total+' browser checks passed; evidence '+tmp);
