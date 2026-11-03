#!/usr/bin/env python3
"""Real HTTP workflow tests. Isolated databases; Python standard library + PHP PDO SQLite."""
from pathlib import Path
from html.parser import HTMLParser
import http.cookiejar
import json
import re
import socket
import sqlite3
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[2]
APPS = ('serviceDesk', 'equipment', 'stockroom')
checks = 0

def check(condition, message):
    global checks
    assert condition, message
    checks += 1
    print('PASS', message, flush=True)

class Forms(HTMLParser):
    def __init__(self, text):
        super().__init__(); self.forms = []; self.current = None; self.feed(text)
    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == 'form':
            self.current = {}; self.forms.append(self.current)
        elif tag == 'input' and self.current is not None and a.get('type') == 'hidden' and a.get('name'):
            self.current[a['name']] = a.get('value', '')
    def handle_endtag(self, tag):
        if tag == 'form': self.current = None
    def named(self, name):
        return next(x.copy() for x in self.forms if x.get('padForm') == name)

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *args): return None

class Client:
    def __init__(self, base):
        self.base = base
        self.opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), NoRedirect)
    def request(self, path, data=None):
        encoded = urllib.parse.urlencode(data, doseq=True).encode() if data is not None else None
        try: response = self.opener.open(self.base + path, encoded, timeout=20)
        except urllib.error.HTTPError as e: response = e
        text = response.read().decode('utf-8', errors='replace')
        if response.code >= 500: raise AssertionError(f'{path}: {response.code}: {text[:2500]}')
        return response.code, text, response.headers
    def get(self, path):
        code, text, headers = self.request(path)
        check(code == 200, 'GET ' + path)
        return text
    def post(self, path, form, values, hidden=None):
        data = hidden if hidden is not None else Forms(self.get(path)).named(form)
        return self.request(path, data | values)

def main():
    with tempfile.TemporaryDirectory(prefix='pad-advanced-apps-') as temp:
        tmp = Path(temp); data = tmp / 'data'; data.mkdir()
        with socket.socket() as sock:
            sock.bind(('127.0.0.1', 0)); port = sock.getsockname()[1]
        router = tmp / 'router.php'
        router.write_text('''<?php
$root = %s;
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($root . '/www' . $path)) return false;
$padApp = explode('/', trim($path, '/'))[0];
if (!in_array($padApp, ['serviceDesk','equipment','stockroom'], true)) { http_response_code(404); exit; }
$padHome=$root; $padApps="$root/apps/"; $padData=%s; $padRoot='/';
$padSetConfig=['ErrorAction'=>'boot'];
$_SERVER['SCRIPT_NAME']="/$padApp/index.php";
$_SERVER['SCRIPT_FILENAME']="$root/www/$padApp/index.php";
include "$root/pad/pad.php";
''' % (json.dumps(str(ROOT)), json.dumps(str(data) + '/')))
        log = (tmp / 'server.log').open('w+')
        server = subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t',str(ROOT/'www'),str(router)], stdout=log, stderr=log)
        try:
            for _ in range(100):
                if server.poll() is not None:
                    log.seek(0); raise RuntimeError(log.read())
                try:
                    with socket.create_connection(('127.0.0.1',port), .2): break
                except OSError: time.sleep(.05)
            client = Client(f'http://127.0.0.1:{port}')
            def scalar(app, sql, args=()):
                with sqlite3.connect(data/app/(app+'.sqlite')) as db:
                    return db.execute(sql,args).fetchone()[0]
            for app in APPS:
                for page in (ROOT/'apps'/app).glob('*.pad'):
                    if page.stem.startswith('_'): continue
                    client.get(f'/{app}/?{page.stem}&id=1')
                for field in ['q','state','priority','low','resource']:
                    client.get(f'/{app}/?index&{field}[]=x')
                for page in {'serviceDesk':['ticket'],'equipment':['booking'],'stockroom':['item']}[app]:
                    for bad in ['0','-1','999999','abc','1%20OR%201=1']:
                        code,_,_=client.request(f'/{app}/?{page}&id={bad}')
                        check(code==404, f'{app} refuses missing/invalid id {bad}')
                    code,_,_=client.request(f'/{app}/?{page}&id[]=1')
                    check(code==404, f'{app} refuses array id')
            # Service desk: validation, escaping, SQL binding, optimistic writes and history.
            ticket = dict(subject="O'Brien <script>alert(1)</script> {php:phpinfo/}",requester='Demo Tester',email='test@example.test',description='A useful support request.',priority='high')
            before=scalar('serviceDesk','SELECT count(*) FROM tickets')
            code,text,_=client.post('/serviceDesk/?new','new-ticket',ticket|{'email':'invalid'})
            check(code==200 and scalar('serviceDesk','SELECT count(*) FROM tickets')==before, 'invalid email cannot create ticket')
            code,_,_=client.request('/serviceDesk/?new', ticket|{'padForm':'new-ticket'})
            check(code==403, 'CSRF-free ticket POST is refused')
            code,_,headers=client.post('/serviceDesk/?new','new-ticket',ticket)
            check(code==302 and 'ticket' in headers['Location'], 'ticket creates and redirects')
            tid=scalar('serviceDesk','SELECT max(id) FROM tickets'); path=f'/serviceDesk/?ticket&id={tid}'
            text=client.get(path)
            check('&lt;script&gt;' in text and '<script>alert(1)' not in text and '{php:phpinfo/}' in text, 'stored HTML and PAD syntax remain escaped text')
            old=Forms(text).named('update-ticket')
            code,_,_=client.post(path,'update-ticket',{'status':'active','owner':'Alex','priority':'urgent'},old)
            check(code==302 and scalar('serviceDesk','SELECT version FROM tickets WHERE id=?',(tid,))==2,'ticket update increments version')
            code,text,_=client.post(path,'update-ticket',{'status':'resolved','owner':'Robin','priority':'normal'},old)
            check(code==200 and 'changed in another window' in text and scalar('serviceDesk','SELECT status FROM tickets WHERE id=?',(tid,))=='active','stale ticket update cannot overwrite')
            code,_,_=client.post(path,'add-note',{'body':"Ready for handover. O'Brien confirmed."})
            check(code==302 and scalar('serviceDesk','SELECT count(*) FROM activity WHERE ticket_id=?',(tid,))==3,'ticket note and update audit persist')
            code,_,_=client.post(path,'update-ticket',{'status':'resolved','owner':'Alex','priority':'high'})
            check(code==302 and scalar('serviceDesk','SELECT status FROM tickets WHERE id=?',(tid,))=='resolved','ticket can be resolved')
            text=client.get('/serviceDesk/?index&q='+urllib.parse.quote("' OR 1=1 --"))
            check('No matching requests' in text,'search SQL metacharacters stay literal')
            # Equipment: exact dates, overlap, adjacency and valid state transitions.
            booking=dict(resource='Cinema camera A',borrower='Demo Tester',email='test@example.test',purpose='Workflow verification',start_day='2032-04-10',end_day='2032-04-12')
            count=scalar('equipment','SELECT count(*) FROM bookings')
            for bad in [{'end_day':'2032-04-09'},{'start_day':'2032-02-30'},{'start_day':'10 April 2032'},{'resource':'Not in register'}]:
                code,_,_=client.post('/equipment/?book','reserve',booking|bad)
                check(code==200 and scalar('equipment','SELECT count(*) FROM bookings')==count,'invalid reservation rejected '+str(bad))
            code,_,_=client.post('/equipment/?book','reserve',booking)
            check(code==302,'reservation creates and redirects')
            bid=scalar('equipment','SELECT max(id) FROM bookings')
            code,text,_=client.post('/equipment/?book','reserve',booking|{'start_day':'2032-04-11'})
            check(code==200 and 'unavailable' in text and scalar('equipment','SELECT count(*) FROM bookings')==count+1,'overlapping reservation cannot be saved')
            code,_,_=client.post('/equipment/?book','reserve',booking|{'start_day':'2032-04-12','end_day':'2032-04-13'})
            check(code==302,'adjacent reservation is allowed')
            bp=f'/equipment/?booking&id={bid}'; hidden=Forms(client.get(bp)).named('change-booking')
            code,_,_=client.post(bp,'change-booking',{'next_status':'returned'},hidden)
            check(code==200 and scalar('equipment','SELECT status FROM bookings WHERE id=?',(bid,))=='reserved','cannot return before collection')
            code,_,_=client.post(bp,'change-booking',{'next_status':'collected'},hidden)
            check(code==302,'reservation can be collected')
            code,_,_=client.post(bp,'change-booking',{'next_status':'returned'})
            check(code==302 and scalar('equipment','SELECT count(*) FROM history WHERE booking_id=?',(bid,))==3,'return persists with complete history')
            code,_,_=client.post(bp,'change-booking',{'next_status':'collected'},hidden)
            check(code==200 and scalar('equipment','SELECT status FROM bookings WHERE id=?',(bid,))=='returned','completed booking cannot be reopened by stale form')
            # Stockroom: duplicate SKU, integer money, idempotency, stock floor, settings conflicts.
            item=dict(sku='TEST-01',name='Test item',category='IT',location='T-01',supplier='Test Supplier',reorder_level=5,unit_cost='12.34')
            code,_,_=client.post('/stockroom/?new','new-item',item)
            check(code==302,'stock item creates')
            iid=scalar('stockroom','SELECT max(id) FROM items'); ip=f'/stockroom/?item&id={iid}'
            check(scalar('stockroom','SELECT unit_cost FROM items WHERE id=?',(iid,))==1234,'cost stored as integer cents')
            before=scalar('stockroom','SELECT count(*) FROM items')
            code,text,_=client.post('/stockroom/?new','new-item',item|{'sku':'test-01'})
            check(code==200 and 'already exists' in text and scalar('stockroom','SELECT count(*) FROM items')==before,'case-normalized duplicate SKU refused')
            hidden=Forms(client.get(ip)).named('movement')
            code,_,_=client.post(ip,'movement',{'direction':'receive','quantity':10,'note':'Opening receipt'},hidden)
            check(code==302 and scalar('stockroom','SELECT stock FROM items WHERE id=?',(iid,))==10,'receipt atomically raises stock')
            code,text,_=client.post(ip,'movement',{'direction':'receive','quantity':10,'note':'Opening receipt'},hidden)
            check(code==200 and scalar('stockroom','SELECT stock FROM items WHERE id=?',(iid,))==10,'duplicate submission cannot receive twice')
            before=scalar('stockroom','SELECT count(*) FROM movements')
            for values in [{'quantity':11},{'quantity':-2},{'quantity':'1.5'},{'direction':'invent'},{'quantity[]':'2','quantity':''}]:
                code,_,_=client.post(ip,'movement',dict(direction='issue',quantity=2,note='Invalid issue')|values)
                check(code==200 and scalar('stockroom','SELECT count(*) FROM movements')==before,'invalid/oversized issue cannot change stock '+str(values))
            code,_,_=client.post(ip,'movement',dict(direction='issue',quantity=7,note='Project use'))
            check(code==302 and scalar('stockroom','SELECT stock FROM items WHERE id=?',(iid,))==3,'valid issue lowers stock')
            check(scalar('stockroom','SELECT stock=(SELECT sum(delta) FROM movements WHERE item_id=items.id) FROM items WHERE id=?',(iid,))==1,'stock equals its movement journal')
            settings=Forms(client.get(ip)).named('item-settings')
            values=dict(location='T-02',supplier='New Supplier',reorder_level=7,unit_cost='10.25')
            code,_,_=client.post(ip,'item-settings',values,settings)
            check(code==302,'stock settings update')
            code,text,_=client.post(ip,'item-settings',values|{'location':'STALE'},settings)
            check(code==200 and 'changed in another window' in text and scalar('stockroom','SELECT location FROM items WHERE id=?',(iid,))=='T-02','stale item settings cannot overwrite')
            text=client.get('/stockroom/?reports')
            check('TEST-01' in text and '11' in text,'reorder report includes the low item and target quantity')
            # A new cookie jar sees persisted records, while GET never performs writes.
            other=Client(client.base)
            check('Test item' in other.get(ip),'data persists across browser sessions')
            before=scalar('stockroom','SELECT stock FROM items WHERE id=?',(iid,))
            client.get(ip+'&padForm=movement&quantity=100&direction=receive&note=GET')
            check(scalar('stockroom','SELECT stock FROM items WHERE id=?',(iid,))==before,'GET cannot mutate stock')
            # More than one page: all matching records remain reachable with stable filters.
            for app in APPS:
                with sqlite3.connect(data/app/(app+'.sqlite')) as db:
                    for n in range(30):
                        if app == 'serviceDesk':
                            db.execute("INSERT INTO tickets(subject,requester,email,description,priority) VALUES(?, 'Pager', 'pager@example.test', 'Paging fixture', 'normal')", (f'Pagination {n:02d}',))
                        elif app == 'equipment':
                            db.execute("INSERT INTO bookings(resource_id,borrower,email,purpose,start_day,end_day) VALUES(1, 'Pager', 'pager@example.test', ?, date('2040-01-01', ?), date('2040-01-02', ?))", (f'Pagination {n:02d}', f'+{n*3} days', f'+{n*3} days'))
                        else:
                            db.execute("INSERT INTO items(sku,name,category,location,supplier,reorder_level,unit_cost) VALUES(?, ?, 'Office', 'P-01', 'Pager', 5, 100)", (f'PAGE-{n:02d}', f'Pagination {n:02d}'))
                detail = {'serviceDesk':'ticket','equipment':'booking','stockroom':'item'}[app]
                query = 'state=reserved' if app == 'equipment' else 'q=Pagination'
                prefix = f'/{app}/?index&{query}'
                first = client.get(prefix)
                second = client.get(prefix+'&p=2')
                pattern = r'\?'+detail+r'&amp;id=(\d+)'
                first_ids = set(re.findall(pattern, first))
                second_ids = set(re.findall(pattern, second))
                check(len(first_ids)==25 and 5 <= len(second_ids) < 25 and not first_ids & second_ids, app+' has nonoverlapping result pages')
                check('rel="next"' in first and 'rel="prev"' in second and query in second, app+' pager preserves filters')
                last = client.get(prefix+'&p=999999999')
                check(set(re.findall(pattern,last))==second_ids, app+' clamps an out-of-range page')
                bad = client.get(prefix+'&p[]=2')
                check(set(re.findall(pattern,bad))==first_ids, app+' safely normalizes an array page parameter')
            for app in APPS:
                forms_path={'serviceDesk':'new','equipment':'book','stockroom':'new'}[app]
                client.get(f'/{app}/?{forms_path}')
                check(not list((ROOT/'apps'/app).glob('*.php')) and not (ROOT/'apps'/app/'_lib').exists(),app+' has no PHP page/controller library')
            print(f'\n{checks} checks passed. Temporary database workflow verified.',flush=True)
        finally:
            server.terminate()
            try: server.wait(timeout=5)
            except subprocess.TimeoutExpired: server.kill();server.wait()
            log.close()

if __name__ == '__main__': main()
