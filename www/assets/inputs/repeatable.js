PadInputs.register('repeatable',function(frame,cfg){
 var rows=frame.querySelector('[data-rows]'),add=frame.querySelector('[data-add]'),tpl=frame.querySelector('template'),status=frame.querySelector('[role=status]'),next=cfg.next||0;
 function update(){add.disabled=rows.children.length>=cfg.max;add.hidden=false;rows.querySelectorAll('[data-remove]').forEach(function(b){b.hidden=false;b.disabled=rows.children.length<=cfg.min;});}
 add.addEventListener('click',function(){if(rows.children.length>=cfg.max)return;var fragment=tpl.content.cloneNode(true),n=next++;fragment.querySelectorAll('[id],[name],[for]').forEach(function(el){['id','name','for'].forEach(function(a){if(el.hasAttribute(a))el.setAttribute(a,el.getAttribute(a).replaceAll('__INDEX__',n));});});fragment.querySelector('legend span').textContent=n+1;rows.appendChild(fragment);update();rows.lastElementChild.querySelector('input').focus();status.textContent='Row added';});
 rows.addEventListener('click',function(e){var b=e.target.closest('[data-remove]');if(!b||rows.children.length<=cfg.min)return;var row=b.closest('.pi-repeat-row'),focus=row.nextElementSibling||row.previousElementSibling;row.remove();update();if(focus)focus.querySelector('input').focus();else add.focus();status.textContent='Row removed';});
 update();
});
