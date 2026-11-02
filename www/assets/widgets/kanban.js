(function(){if(window.padKanbanReady)return;window.padKanbanReady=true;
 function init(root){if(root.dataset.ready)return;root.dataset.ready='1';var drag=null,status=root.querySelector('[role=status]');
 function move(card,to){var from=card.closest('section'),dest=Array.from(root.querySelectorAll('section[data-column]')).find(function(s){return s.dataset.column===to;});if(!dest||from===dest)return;
 var detail={id:card.dataset.card,from:from.dataset.column,to:to};if(!root.dispatchEvent(new CustomEvent('pad-kanban-beforemove',{bubbles:true,cancelable:true,detail:detail}))){card.querySelector('select').value=from.dataset.column;return;}
 dest.querySelector('ul').appendChild(card);card.querySelector('select').value=to;card.querySelector('select').focus();status.textContent=card.querySelector('strong').textContent+' moved to '+dest.querySelector('h3').textContent;root.dispatchEvent(new CustomEvent('pad-kanban-move',{bubbles:true,detail:detail}));}
 root.querySelectorAll('[data-card]').forEach(function(card){card.draggable=true;card.querySelector('label').hidden=false;card.querySelector('select').addEventListener('change',function(){move(card,this.value);});card.addEventListener('dragstart',function(e){drag=card;e.dataTransfer.setData('text/plain',card.dataset.card);});card.addEventListener('dragend',function(){drag=null;});});
 root.querySelectorAll('section').forEach(function(s){s.addEventListener('dragover',function(e){if(drag)e.preventDefault();});s.addEventListener('drop',function(e){if(!drag)return;e.preventDefault();move(drag,s.dataset.column);drag=null;});});
 }
 function scan(){document.querySelectorAll('.pad-kanban').forEach(init);}scan();document.addEventListener('DOMContentLoaded',scan);new MutationObserver(scan).observe(document.documentElement,{childList:true,subtree:true});
})();
