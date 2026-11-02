(function(){if(window.padTreeviewReady)return;window.padTreeviewReady=true;
 function init(root){if(document.readyState==='loading')return;if(root.dataset.ready)return;root.dataset.ready='1';var tree=root.firstElementChild,links=Array.from(tree.querySelectorAll('a'));tree.setAttribute('role','tree');tree.setAttribute('aria-label',root.getAttribute('aria-label'));
 links.forEach(function(a,i){a.setAttribute('role','treeitem');a.tabIndex=i?-1:0;a.parentNode.setAttribute('role','none');var child=a.nextElementSibling;if(child){child.setAttribute('role','group');a.setAttribute('aria-expanded','true');}});
 function focus(a){links.forEach(function(x){x.tabIndex=-1;});a.tabIndex=0;a.focus();}
 function select(a){links.forEach(function(x){x.setAttribute('aria-selected',String(x===a));});root.dispatchEvent(new CustomEvent('pad-tree-select',{bubbles:true,detail:{id:a.dataset.treeId}}));}
 tree.addEventListener('click',function(e){var a=e.target.closest('a');if(a){focus(a);select(a);}});
 tree.addEventListener('keydown',function(e){var a=e.target.closest('a');if(!a||e.altKey||e.ctrlKey||e.metaKey)return;var visible=links.filter(function(x){return !x.closest('[hidden]');}),i=visible.indexOf(a),child=a.nextElementSibling,parent=a.parentNode.parentNode.parentNode.querySelector(':scope > a');
 if(e.key==='ArrowDown')focus(visible[Math.min(i+1,visible.length-1)]);else if(e.key==='ArrowUp')focus(visible[Math.max(i-1,0)]);else if(e.key==='Home')focus(visible[0]);else if(e.key==='End')focus(visible.at(-1));else if(e.key==='ArrowRight'&&child){if(child.hidden){child.hidden=false;a.setAttribute('aria-expanded','true');}else focus(child.querySelector('a'));}else if(e.key==='ArrowLeft'){if(child&&!child.hidden){child.hidden=true;a.setAttribute('aria-expanded','false');}else if(parent)focus(parent);}else if(e.key===' '){select(a);}else return;e.preventDefault();});
 }
 function scan(){document.querySelectorAll('.pad-treeview').forEach(init);}scan();document.addEventListener('DOMContentLoaded',scan);new MutationObserver(scan).observe(document.documentElement,{childList:true,subtree:true});
})();
