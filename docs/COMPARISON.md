# PAD compared with other web frameworks

PAD is a template-driven PHP framework: the page's template decides what runs, the file tree
is the routing, and there are no controllers. The frameworks it is compared with here are
the full-stack ones a PHP, Ruby or Python developer would weigh it against - **Laravel**
(PHP), **Symfony** (PHP), **Ruby on Rails** and **Django** (Python).

The table lists what a full-stack framework is expected to ship and where PAD stands:
**yes** - PAD had it already; **new** - added in this round; **no** - still missing.

## Request, routing and responses

| Feature | Laravel / Symfony / Rails / Django | PAD |
|---------|------------------------------------|-----|
| Routing | Route files, attributes or URL confs | yes - the file tree; clean URLs and `[id]` segments (`lib/route.php`) |
| Middleware / filters | Middleware, before-actions, decorators | yes - `_guard.php` per directory, `_inits.php` / `_exits.php` |
| JSON / CSV APIs | API resources, serializers | yes - `$padExpose`, `?padFormat=json`, `Accept` |
| Partial responses (HTMX, Turbo) | Turbo frames, Livewire, htmx helpers | yes - `{fragment}`, `{live}` regions |
| Custom error pages | `errors/404.blade.php`, `public/404.html`, `404.html` | **new** - `_errors/404.pad`, `4xx.pad`, `5xx.pad` |
| Maintenance mode | `artisan down` with a secret, Rails maintenance gems | **new** - `pad down <app> --secret=x`, `pad up` |
| Health endpoint | Laravel 11 `/up`, Spring/Rails health checks | **new** - `$padHealth`, `?up` |
| CORS | `config/cors.php`, `django-cors-headers`, `rack-cors` | **new** - `$padCors`, preflight before the app |
| Security headers, CSP nonce | Middleware / `SecureHeaders` | yes - `$padSecurityHeaders`, `$padCsp`, `{nonce}` |
| Streaming / Server-Sent Events | `response()->eventStream`, `ActionController::Live` | no - `{flush}` sends early, but there is no event stream |
| WebSockets / broadcasting | Reverb / Echo, Action Cable, Channels | no |

## Templates and front end

| Feature | Others | PAD |
|---------|--------|-----|
| Layouts, blocks, components, slots | Blade, Twig, ERB partials, Django templates | yes - `{extends}`, `{block}`, `{slot}`, `_tags/*.pad` |
| Stacks, push | Blade `@push` / `@stack` | yes - `{push}`, `{stack}` |
| Escaping by default | Yes everywhere | yes - values are text, sanitize chain |
| Auth directives | Blade `@auth`, `@guest`, `@can` | **new** - `{auth}`, `{guest}`, `{can}`, `{cannot}` |
| Asset versioning | Vite `asset()`, Rails digests, `ManifestStaticFilesStorage` | **new** - `{asset 'app.css'}` → `?v=<content hash>` |
| Asset bundling (JS/CSS build) | Vite, Webpacker / jsbundling, Encore | no - PAD serves files as they are |
| Charts, QR codes, barcodes, calendars, Markdown | Packages | yes - built in, server-rendered SVG |

## Data

| Feature | Others | PAD |
|---------|--------|-----|
| Query layer | Eloquent, Doctrine, Active Record, Django ORM | yes - `db()` verbs, database tags, PAD Select with relations |
| MySQL and SQLite | Yes | yes |
| Migrations | `artisan migrate`, `rails db:migrate`, `manage.py migrate` | **new** - `_migrations/`, `pad migrate` with status, rollback, fresh, pretend |
| Seeders and factories | Seeders, factories + Faker, fixtures | **new** - `_seeds/`, `pad seed`, `padFactory`, `padFake*` |
| Object models with events and soft deletes | Eloquent models, AR callbacks | no - PAD works with rows, not model classes |
| Admin interface | Django admin, Filament, ActiveAdmin | no |

## Users and security

| Feature | Others | PAD |
|---------|--------|-----|
| CSRF | Yes | yes - `$padCsrf`, `{csrf}` |
| Validation | Form requests, validators, forms | yes - `padValidate`, `rules=` on `{form}` fields |
| Password hashing, encryption, signed URLs | Yes | yes - `padHash`, `padEncrypt`, `padSignedUrl` |
| Rate limiting | `RateLimiter`, `rack-attack` | yes - `padRateLimit` |
| Authentication (login, logout, remember-me) | Auth / Breeze, Devise, `django.contrib.auth` | **new** - `padLogin`, `padAttempt`, `padAuthRequire`, `padRedirectIntended`, signed remember-me |
| Password reset tokens | Built in | **new** - `padPasswordToken`, storage-free |
| Authorization (gates, policies) | Gates / policies, Pundit, permissions | **new** - `padGate`, `padGateBefore`, `padCan`, `padAuthorize` |
| Feature flags | Pennant, Flipper, django-waffle | **new** - `$padFeatures`, `padFeature`, `{feature}` |
| OAuth / social login | Socialite, OmniAuth, allauth | no |

## Background work and operations

| Feature | Others | PAD |
|---------|--------|-----|
| Mail with templates | Mailables, Action Mailer | yes - `{mail}`, `padMail` |
| Queued jobs | Queues, Active Job, Celery | **new** - `padQueue`, `_jobs/`, `pad work`, retries and failed jobs |
| Task scheduler | Scheduler, `whenever` / recurring jobs, celery beat | **new** - `_schedule.php`, `pad schedule --all` from one cron line |
| Queued mail | `Mail::queue` | **new** - `padMail ( ..., [ 'queue' => TRUE ] )` |
| Event hooks | Events / listeners, signals | yes - `_events/` |
| Caching (page, fragment, data) | Yes | yes - page cache, `{cache}`, `padRemember` |
| File storage abstraction (S3 ...) | Flysystem, Active Storage, storages | no - uploads go to `DATA/uploads/` |
| Notifications (mail, SMS, Slack ...) | Notifications, Noticed | no |
| Logging | Monolog, Rails logger | yes - `padLog` |
| i18n | Yes | yes - `_lang/`, `{trans}`, locale formatting |

## Tooling and testing

| Feature | Others | PAD |
|---------|--------|-----|
| Command-line tool | `artisan`, `rails`, `manage.py`, `bin/console` | yes - `pad` (new, serve, render, lint, export, test, sample) and now migrate, seed, work, queue, schedule, down, up |
| Application tests | PHPUnit / Pest, Minitest, Django tests | yes - `_tests/`, `pad test`, `{assert}` |
| HTTP fakes in tests | `Http::fake`, WebMock | **new** - `padCurlFake`, `padCurlRecorded` |
| Debug toolbar | Debugbar, web profiler, django-debug-toolbar | yes - `$padToolbar` |
| Live reload | Vite HMR | yes - `$padReload` |
| Editor support | IDE plugins, language servers | yes - LSP, Tree-sitter, VS Code, MCP server |

## Still missing

Real-time (WebSockets, Server-Sent Events), an asset build pipeline, model classes with
lifecycle events, an admin interface, OAuth login, a storage abstraction for cloud disks and
multi-channel notifications. Each is a large piece on its own; the ones closest to PAD's way
of working are Server-Sent Events (a `{live}` region that the server pushes to) and a storage
driver behind `padUpload`.
