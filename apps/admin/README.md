# Admin

## Introduction

The web console of the PAD installation: every application with what it has, its files and pages, the pad command from the browser, maintenance mode, queues, schedules, logs, error dumps, the mail outbox, DATA/ storage, PHP, the PAD settings, git and its own users - behind a login, this machine only by default.

## Usage

Open `http://localhost/pad/admin/`. The first visit asks for the first user - there is no
default password, and only this machine may make that user; more are added on the
**Users** page. The console's users are its own (`DATA/admin/users.json`), not the editor's.

## Pages

| Page | What it does |
|------|--------------|
| Dashboard | Tiles for applications, apps down, jobs queued and failed, error dumps, log lines today and mails; the system, the disk as a gauge, the files changed last, DATA/ by directory |
| Applications | Every application with its description, page count and what it has (guard, .env, migrations, jobs, tests ...), filterable; the regression test applications on request. The successor of the `apps` application |
| Application | One application: facts, file kinds, its PAD parts, its `_config/` settings, its pages (open, render, view), its state under DATA/, maintenance down/up, its README |
| Files | The files of `apps/<app>/` or `www/<app>/`, coloured by kind, Markdown rendered; dot files never, passwords and keys redacted |
| Search | Text in the sources of one application or all of them |
| Console | A fixed list of pad commands - lint, test, render, migrate (status, pretend, run, rollback), seed, queue, work, schedule, types, down, help - run for an application, output shown; a history of every run |
| Maintenance | The applications that are down, taking one down (secret, retry, message), bringing one up |
| Queues | Per application and queue the jobs due, delayed and running; the failed jobs with why and their data; retry, flush, work once |
| Schedule | Every `_schedule.php` as `pad schedule --list` shows it, run what is due, the cron line |
| Logs | The `padLog` files of every application, one read from its end with level and text filters; PHP's own error log |
| Error dumps | The reports under `DATA/dumps/`, each file of one in a sandboxed frame; remove one or all |
| Mail outbox | The mails of the 'file' transport: headers, text, the HTML in a sandboxed frame, the source |
| Storage | DATA/ per directory with what it holds; empty the caches and leftovers - never what an application relies on |
| PHP | Version, settings, OPcache, the extensions PAD uses, phpinfo () |
| Settings | Every `$pad*` default of `pad/config/config.php` with its comment and the applications that change it |
| Git | Branch, upstream, uncommitted changes, the last commits |
| Users | The console's users: add, new password, remove |

## Security

- `_guard.php`: a web request must come from this machine (loopback, nothing forwarded, a
  local Host name) and be logged in. `$adminRemote` / `$adminHosts` in `_config/config.php`
  open it up.
- Every change is a post with the CSRF token; no request value becomes a variable.
- The console runs only the commands of its list, as the web server's user;
  `$adminCommands = FALSE` switches them off.
- A new password ends the user's other sessions; five login tries a minute per address.

## Files

| File | Description |
|------|-------------|
| `_lib/auth.php` | Who may reach the console, its users |
| `_lib/apps.php` | The applications, their pages, parts and settings |
| `_lib/tools.php` | Sizes, safe paths, running pad and git, redaction, the history |
| `_lib/mail.php` | Reading back the `.eml` files |
| `www/admin/admin.css`, `admin.js` | The look, and the table filters and confirm questions |
