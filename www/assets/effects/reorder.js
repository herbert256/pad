(function(){'use strict';
// Caller gives a complete permutation. DOM order changes synchronously, animation is visual only.
window.padReorder=function(node,keys){
 if(!node||!node.matches('[data-pad-reorder]')||!Array.isArray(keys))throw Error('A reorder container and key array are required');
 var children=Array.from(node.children),map=new Map();if(children.length>500||keys.length!==children.length)throw Error('Reorder requires at most 500 children and every key');
 children.forEach(function(el){var key=el.getAttribute('data-pad-key');if(!key||map.has(key))throw Error('Reorder keys must be nonempty and unique');map.set(key,el);});
 if(new Set(keys).size!==keys.length||keys.some(k=>!map.has(k)))throw Error('Unknown or duplicate reorder key');
 var before=new Map(children.map(el=>[el,el.getBoundingClientRect()])),active=document.activeElement;
 keys.forEach(key=>node.appendChild(map.get(key)));
 if(active&&node.contains(active))active.focus({preventScroll:true});
 if(matchMedia('(prefers-reduced-motion: reduce)').matches)return;
 var duration=Math.max(0,Math.min(1000,Number(node.dataset.padReorder)||0));
 children.forEach(function(el){if(!el.isConnected||!el.animate)return;var a=before.get(el),b=el.getBoundingClientRect(),dx=Math.max(-2000,Math.min(2000,a.left-b.left)),dy=Math.max(-2000,Math.min(2000,a.top-b.top));el.getAnimations().forEach(x=>x.cancel());el.animate([{transform:'translate('+dx+'px,'+dy+'px)'},{transform:'none'}],{duration:duration,easing:'ease-out'});});
};})();
