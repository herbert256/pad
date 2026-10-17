import{d as u,m as s}from"./hooks.module-DB17ymvh.js";import"./preact.module-IRqqXl-z.js";function l({start:n,framework:e}){const[t,o]=u(n);return s`
    <div class="counter">
      <div class="counter-value">${t}</div>
      <div class="counter-buttons">
        <button onClick=${()=>o(t-1)} aria-label="One less">−</button>
        <button onClick=${()=>o(t+1)} aria-label="One more">+</button>
      </div>
      <p class="counter-note">${e}: useState, no build step</p>
    </div>`}export{l as default};
