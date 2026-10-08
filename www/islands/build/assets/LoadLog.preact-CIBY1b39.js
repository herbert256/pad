import{d as i,h as l,m as e}from"./hooks.module-DB17ymvh.js";import{m as a}from"./main-2Gb33P07.js";import"./preact.module-IRqqXl-z.js";function g(){const[o,s]=i(a);return l(()=>{s(a());const t=n=>s(d=>[...d,n.detail]);return window.addEventListener("pad:island",t),()=>window.removeEventListener("pad:island",t)},[]),e`
    <ol class="load-log">
      ${o.length===0&&e`<li class="muted">Waiting for the first island ...</li>`}
      ${o.map(t=>e`<li class="rise"><code>${t.at.toLocaleTimeString()}</code> <strong>${t.name}</strong> mounted - its code took ${t.ms} ms <span class="badge">${t.element.getAttribute("data-load")||"eager"}</span></li>`)}
    </ol>`}export{g as default};
