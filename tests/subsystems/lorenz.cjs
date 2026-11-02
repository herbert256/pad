const a=require('node:assert/strict'),m=require('../../www/assets/toys/lorenz.js'),p={sigma:10,rho:28,beta:8/3};
const v=m.deriv([1,1,1],p);a.equal(v[0],0);a.equal(v[1],26);a.ok(Math.abs(v[2]+5/3)<1e-12);a.deepEqual(m.rk4([0,0,0],p,.01),[0,0,0]);
let s=[1,1,1];for(let i=0;i<10000;i++)s=m.rk4(s,p,.005);a.ok(s.every(v=>Number.isFinite(v)&&Math.abs(v)<100));
let x=[1,1,1],y=x.slice();for(let i=0;i<100;i++)x=m.rk4(x,p,.005);for(let i=0;i<200;i++)y=m.rk4(y,p,.0025);a.ok(Math.hypot(...x.map((v,i)=>v-y[i]))<.001);console.log('lorenz: equations, equilibrium, long finite run and step convergence passed');
