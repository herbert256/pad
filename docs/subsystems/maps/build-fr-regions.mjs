// node build-fr-regions.mjs fr-regions.json output.json /path/to/ne_10m_...shp
// Needs mapshaper 0.6.121; set MAPSHAPER_MODULE to its absolute mapshaper.js if not installed locally.
import {readFileSync,writeFileSync,mkdtempSync,rmSync} from 'node:fs';
import {execFileSync} from 'node:child_process';
import {tmpdir} from 'node:os';
import {join,dirname} from 'node:path';
import {fileURLToPath} from 'node:url';
const parts=JSON.parse(readFileSync(process.argv[2])),tmp=mkdtempSync(join(tmpdir(),'pad-fr-map-')),here=dirname(fileURLToPath(import.meta.url));
try {
 const results={};
 for(const [name,cfg] of Object.entries(parts)){
  const config=join(tmp,name+'.json'),output=join(tmp,name+'-out.json');
  writeFileSync(config,JSON.stringify(cfg));
  execFileSync(process.execPath,[join(here,'build-outline.mjs'),config,output,process.argv[4]],{stdio:'inherit'});
  results[name]=JSON.parse(readFileSync(output));
 }
 const main=results.main,top=main._box[3]+20;main._insets=[];
 for(const [i,name] of ['GUA','MTQ','GUF','LRE','MAY'].entries()){
  const part=results[name],dx=i*200+25+(150-part._box[2])/2,dy=top+35+(150-part._box[3])/2;
  for(const [code,region] of Object.entries(part.regions)){
   region[3]=region[3].map((v,j)=>Math.round(v+(j%2?dy:dx)));
   for(const k of [4,5])region[k]=region[k].replace(/M(-?\d+),(-?\d+)/g,(_,x,y)=>'M'+Math.round(+x+dx)+','+Math.round(+y+dy));
   main.regions[code]=region;main._insets.push([i*200+10,top,180,190,region[1]]);
  }
 }
 main._box=[0,0,1000,top+200];main._source+=' French departments dissolved by trimmed region_cod into 18 regions; five overseas regions shown in individually scaled labeled insets. Source ISO regional codes also join through INSEE region-number aliases.';
 writeFileSync(process.argv[3],JSON.stringify(main));
}finally{rmSync(tmp,{recursive:true,force:true});}
