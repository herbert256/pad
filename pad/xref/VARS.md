# PAD Framework Variables Reference

This document lists all `$pad*` variables used in the PAD framework and the PHP files where they appear.

Access types:
- **global** - Declared with `global $varname`
- **$GLOBALS** - Accessed via `$GLOBALS['varname']`
- **direct** - Direct variable access (local scope or already global)

---

### `$pad`

- `at/any/tags.php` (global)
- `at/_lib/at.php` (global)
- `at/_lib/lib.php` (global)
- `build/build.php` (direct)
- `events/base.php` (direct)
- `events/build.php` (direct)
- `events/data.php` (direct)
- `events/flags.php` (direct)
- `events/levelStart.php` (direct)
- `events/optional.php` (direct)
- `events/options.php` (direct)
- `events/parms.php` (direct)
- `events/parse.php` (direct)
- `events/resultOcc.php` (direct)
- `events/store.php` (direct)
- `events/tag.php` (direct)
- `events/true.php` (direct)
- `events/type.php` (direct)
- `handling/handling.php` (direct)
- `handling/negative/exits.php` (direct)
- `handling/negative/inits.php` (direct)
- `handling/types/dedup.php` (direct)
- `handling/types/end.php` (direct)
- `handling/types/first.php` (direct)
- `handling/types/page.php` (direct)
- `handling/types/random.php` (direct)
- `handling/types/reverse.php` (direct)
- `handling/types/row.php` (direct)
- `handling/types/rows.php` (direct)
- `handling/types/shuffle.php` (direct)
- `handling/types/sort.php` (direct)
- `handling/types/splice.php` (direct)
- `handling/types/start.php` (direct)
- `handling/types/trim.php` (direct)
- `info/_lib/_lib.php` (global)
- `info/types/trace/end.php` (global)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/occur/end.php` (direct)
- `info/types/trace/occur/start.php` (direct)
- `info/types/trace/start.php` (direct)
- `info/types/xml/event.php` (direct)
- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/parent.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)
- `inits/vars.php` (direct)
- `level/base.php` (direct)
- `level/close.php` (direct)
- `level/data.php` (direct)
- `level/end.php` (direct)
- `level/flags.php` (direct)
- `level/function.php` (direct)
- `level/go.php` (direct)
- `level/level.php` (direct)
- `level/name.php` (direct)
- `level/pair.php` (direct)
- `level/parms/option.php` (direct)
- `level/parms/parameter.php` (direct)
- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)
- `level/pipes/after.php` (direct)
- `level/pipes/before.php` (direct)
- `level/set.php` (direct)
- `level/setup.php` (direct)
- `level/split.php` (direct)
- `level/start_end/end1.php` (direct)
- `level/start_end/end2.php` (direct)
- `level/start_end/start1.php` (direct)
- `level/start_end/start2.php` (direct)
- `level/start.php` (direct)
- `level/tag.php` (direct)
- `lib/callback.php` (global)
- `lib/data.php` (global)
- `lib/db.php` (global)
- `lib/dump.php` (global, $GLOBALS)
- `lib/field/field.php` ($GLOBALS)
- `lib/field/level.php` (global)
- `lib/field/lib.php` (global)
- `lib/field/prefix.php` ($GLOBALS)
- `lib/level.php` (global)
- `lib/other.php` (global, $GLOBALS)
- `lib/table.php` (global)
- `occurrence/end.php` (direct)
- `occurrence/init.php` (direct)
- `occurrence/occurrence.php` (direct)
- `occurrence/set.php` (direct)
- `occurrence/table.php` (direct)
- `options/before.php` (direct)
- `options/callback.php` ($GLOBALS)
- `options/go/options.php` (direct)
- `options/go/reset.php` (direct)
- `options/toBool.php` (direct)
- `options/toContent.php` (direct)
- `options/toData.php` (direct)
- `sequence/actions/inits.php` (direct)
- `sequence/actions/types/randomize.php` (direct)
- `sequence/actions/types/trim.php` (direct)
- `sequence/exits/done.php` (direct)
- `sequence/exits/extra/actions.php` (direct)
- `sequence/exits/extra/chain.php` (direct)
- `sequence/exits/extra/org.php` (direct)
- `sequence/exits/extra/plays.php` (direct)
- `sequence/exits/extra/pull.php` (direct)
- `sequence/exits/extra/set.php` (direct)
- `sequence/exits/info/options.php` (direct)
- `sequence/exits/return/given.php` (direct)
- `sequence/exits/return/names.php` (direct)
- `sequence/exits/return.php` (direct)
- `sequence/exits/return/return.php` (direct)
- `sequence/exits/start.php` (direct)
- `sequence/exits/store/store.php` (direct)
- `sequence/inits/check/check.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/find/loop.php` (direct)
- `sequence/inits/find/parm/inits.php` (direct)
- `sequence/inits/find/parm/quick.php` (direct)
- `sequence/inits/name.php` (direct)
- `sequence/inits/parms.php` (direct)
- `sequence/inits/set.php` (direct)
- `sequence/inits/tag.php` (direct)
- `sequence/plays/inits.php` (direct)
- `sequence/sequence/tag.php` (direct)
- `start/code.php` (direct)
- `start/end/dat.php` (direct)
- `start/enter/ajax.php` (direct)
- `start/enter/code.php` (direct)
- `start/enter/function.php` (global)
- `start/enter/get.php` (direct)
- `start/enter/page.php` (direct)
- `start/enter/redirect.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/function.php` (direct)
- `start/level.php` (direct)
- `start/pad.php` (direct)
- `start/start/dat.php` (direct)
- `start/start/level.php` (direct)
- `start/start/resetPad.php` (direct)
- `tags/case.php` (direct)
- `tags/check.php` (direct)
- `tags/curl.php` (direct)
- `tags/decrement.php` (direct)
- `tags/echo.php` (direct)
- `tags/file.php` (direct)
- `tags/go/data.php` (direct)
- `tags/go/store.php` (direct)
- `tags/if.php` (direct)
- `tags/increment.php` (direct)
- `tags/record.php` (direct)
- `tags/restart.php` (direct)
- `tags/set.php` (direct)
- `tags/switch.php` (direct)
- `tags/tidy.php` (direct)
- `tags/trace.php` (direct)
- `tags/while.php` (direct)
- `types/app.php` (direct)
- `types/array.php` (direct)
- `types/bool.php` (direct)
- `types/constant.php` (direct)
- `types/data.php` (direct)
- `types/field.php` (direct)
- `types/function.php` (direct)
- `types/go/local.php` ($GLOBALS)
- `types/go/table.php` (direct)
- `types/level.php` (direct)
- `types/local.php` (direct)
- `types/pad.php` (direct)
- `types/php.php` (direct)
- `types/property.php` (direct)
- `types/script.php` (direct)
- `types/table.php` (direct)
- `walk/end.php` (direct)
- `walk/next.php` (direct)

### `$pad1`

- `lib/db.php` (direct)

### `$pad2`

- `lib/db.php` (direct)

### `$pad3`

- `lib/db.php` (direct)

### `$padAfterBase`

- `info/types/trace/_lib.php` (global)

### `$padApp`

- `events/callEnd.php` ($GLOBALS)
- `info/types/stats/end.php` (global)
- `inits/vars.php` (direct)

### `$padArray`

- `events/flags.php` (direct)
- `level/data.php` (direct)
- `level/flags.php` (direct)
- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `options/go/reset.php` (direct)
- `walk/next.php` (direct)

### `$padAt`

- `lib/field/at.php` (direct)

### `$padBase`

- `build/build.php` (direct)
- `events/base.php` (direct)
- `events/build.php` (direct)
- `events/resultOcc.php` (direct)
- `events/true.php` (direct)
- `events/type.php` (direct)
- `info/types/trace/_lib.php` (global)
- `level/base.php` (direct)
- `level/go.php` (direct)
- `level/pipes/before.php` (direct)
- `level/setup.php` (direct)
- `level/split.php` (direct)
- `level/start_end/end1.php` (direct)
- `level/start_end/end2.php` (direct)
- `level/start_end/start1.php` (direct)
- `level/start_end/start2.php` (direct)
- `level/start.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `occurrence/init.php` (direct)
- `options/go/options.php` (direct)
- `start/code.php` (direct)
- `start/enter/code.php` (direct)
- `start/enter/sandbox.php` (direct)
- `tags/go/store.php` (direct)

### `$padBaseBase`

- `level/pair.php` (direct)
- `level/tag.php` (direct)

### `$padBaseSet`

- `inits/level.php` (direct)
- `level/pair.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)

### `$padBasis`

- `tags/case.php` (direct)

### `$padBeforeBase`

- `info/types/trace/_lib.php` (global)

### `$padBetween`

- `events/parse.php` (direct)
- `info/types/trace/level/start.php` (direct)
- `inits/level.php` (direct)
- `level/between.php` (direct)
- `level/close.php` (direct)
- `level/level.php` (direct)
- `level/pair.php` (direct)
- `level/pipes/start.php` (direct)
- `level/tag.php` (direct)
- `level/var.php` (direct)
- `lib/level.php` (global, $GLOBALS)

### `$padBetweenCheck`

- `level/pair.php` (direct)
- `level/pipes/end.php` (direct)

### `$padBoolStore`

- `eval/single/flag.php` ($GLOBALS)
- `lib/type.php` ($GLOBALS)
- `options/bool.php` (direct)
- `options/toBool.php` (direct)
- `types/bool.php` (direct)

### `$padBuild`

- `build/page.php` (direct)

### `$padBuildBase`

- `build/base.php` (direct)
- `build/build.php` (direct)

### `$padBuildBaseNow`

- `build/base.php` (direct)

### `$padBuildCall`

- `build/page.php` (direct)

### `$padBuildDir`

- `build/base.php` (direct)
- `build/dirs.php` (direct)
- `build/_lib.php` (direct)

### `$padBuildDirs`

- `build/base.php` (direct)
- `build/dirs.php` (direct)
- `build/_lib.php` (direct)
- `build/page.php` (direct)

### `$padBuildExit`

- `build/base.php` (direct)

### `$padBuildFalse`

- `build/page.php` (direct)
- `build/split.php` (direct)

### `$padBuildInit`

- `build/base.php` (direct)

### `$padBuildLib`

- `build/build.php` (direct)
- `build/_lib.php` (direct)

### `$padBuildMrg`

- `build/dirs.php` (direct)

### `$padBuildNow`

- `build/dirs.php` (direct)

### `$padBuildPage`

- `build/build.php` (direct)

### `$padBuildPos`

- `build/dirs.php` (direct)

### `$padBuildTrue`

- `build/page.php` (direct)
- `build/split.php` (direct)

### `$padCache`

- `cache/inits.php` (direct)
- `config/config.php` (direct)
- `exits/exits.php` (direct)
- `inits/cache.php` (direct)

### `$padCacheAge`

- `cache/hit.php` (direct)
- `cache/inits.php` (direct)

### `$padCacheClient`

- `cache/inits.php` (direct)

### `$padCacheClientAge`

- `config/cache.php` (direct)
- `lib/output.php` (global, $GLOBALS)

### `$padCacheDbConnect`

- `cache/types/db.php` (global)

### `$padCacheDbDatabase`

- `cache/types/db.php` (global)
- `config/cache.php` (direct)

### `$padCacheDbHost`

- `cache/types/db.php` (global)
- `config/cache.php` (direct)

### `$padCacheDbPassword`

- `cache/types/db.php` (global)
- `config/cache.php` (direct)

### `$padCacheDbUser`

- `cache/types/db.php` (global)
- `config/cache.php` (direct)

### `$padCacheEtag`

- `cache/exits.php` (direct)
- `cache/inits.php` (direct)

### `$padCacheFile`

- `cache/types/file.php` (global, $GLOBALS)
- `config/cache.php` (direct)

### `$padCacheFileMode`

- `config/cache.php` (direct)

### `$padCacheMax`

- `cache/inits.php` (direct)

### `$padCacheMemory`

- `cache/types/memory.php` (global)

### `$padCacheMemoryHost`

- `cache/types/memory.php` (global)
- `config/cache.php` (direct)

### `$padCacheMemoryPort`

- `cache/types/memory.php` (global)
- `config/cache.php` (direct)

### `$padCacheProxyAge`

- `config/cache.php` (direct)
- `lib/output.php` (global, $GLOBALS)

### `$padCacheServerAge`

- `cache/inits.php` (direct)
- `cache/types/memory.php` (global)
- `config/cache.php` (direct)
- `exits/exits.php` (direct)

### `$padCacheServerGzip`

- `cache/exits.php` (direct)
- `config/cache.php` (direct)
- `exits/output.php` (direct)
- `lib/output.php` (global, $GLOBALS)

### `$padCacheServerNoData`

- `cache/inits.php` ($GLOBALS)
- `cache/types/db.php` ($GLOBALS)
- `cache/types/file.php` ($GLOBALS)
- `cache/types/memory.php` (global)
- `config/cache.php` (direct)

### `$padCacheServerType`

- `cache/inits.php` (direct)
- `config/cache.php` (direct)

### `$padCacheStop`

- `cache/hit.php` (direct)
- `exits/output.php` (direct)
- `inits/vars.php` (direct)
- `lib/output.php` ($GLOBALS)

### `$padCacheUrl`

- `cache/exits.php` (direct)
- `cache/inits.php` (direct)

### `$padCall`

- `build/_lib.php` (direct)
- `build/page.php` (direct)
- `call/_call.php` (direct)
- `call/_once.php` (direct)
- `call/_tryOnce.php` (direct)
- `call/_try.php` (direct)
- `eval/parms/app.php` (direct)
- `events/cache.php` (direct)
- `events/callEnd.php` (direct)
- `events/call.php` (direct)
- `events/callStart.php` (direct)
- `get/go/call.php` (direct)
- `options/callback.php` (direct)
- `options/go/options.php` (direct)
- `types/go/local.php` (direct)
- `types/go/tag.php` (direct)

### `$padCallback`

- `callback/exit.php` (direct)
- `callback/init.php` (direct)
- `callback/row.php` (direct)

### `$padCallbackType`

- `lib/callback.php` (direct)

### `$padCallOB`

- `call/any.php` (direct)
- `call/_exit.php` (direct)
- `call/_init.php` (direct)
- `call/obNoOne.php` (direct)
- `call/ob.php` (direct)
- `call/_return.php` (direct)
- `types/go/tag.php` (direct)

### `$padCallPHP`

- `build/page.php` (direct)
- `call/any.php` (direct)
- `call/_call.php` (direct)
- `call/_exit.php` (direct)
- `call/_init.php` (direct)
- `call/noOne.php` (direct)
- `call/obNoOne.php` (direct)
- `call/_once.php` (direct)
- `call/_return.php` (direct)
- `types/go/tag.php` (direct)

### `$padCallStart`

- `events/callEnd.php` (direct)
- `events/callStart.php` (direct)

### `$padCheck`

- `options/data.php` (direct)

### `$padChk`

- `tags/case.php` (direct)
- `tags/if.php` (direct)

### `$padClientDate`

- `cache/inits.php` (direct)
- `inits/client.php` (direct)

### `$padClientEtag`

- `exits/output/web.php` (direct)
- `inits/client.php` (direct)

### `$padClientGzip`

- `inits/client.php` (direct)
- `lib/output.php` (global, $GLOBALS)

### `$padClosePad`

- `level/close.php` (direct)

### `$padContent`

- `events/go.php` (direct)
- `get/content.php` (direct)
- `level/base.php` (direct)
- `level/go.php` (direct)
- `options/close.php` (direct)
- `options/glue.php` (direct)
- `options/go/options.php` (direct)
- `options/go/reset.php` (direct)
- `options/ignore.php` (direct)
- `options/open.php` (direct)
- `options/print.php` (direct)
- `options/quote.php` (direct)
- `options/tidy.php` (direct)
- `options/toData.php` (direct)
- `tags/case.php` (direct)
- `tags/code.php` (direct)
- `tags/file.php` (direct)
- `tags/go/store.php` (direct)
- `tags/if.php` (direct)
- `tags/ignore.php` (direct)
- `tags/sandbox.php` (direct)
- `tags/tidy.php` (direct)
- `types/function.php` (direct)
- `walk/end.php` (direct)

### `$padContentData`

- `level/go.php` (direct)

### `$padContentStore`

- `eval/single/content.php` ($GLOBALS)
- `get/content.php` (direct)
- `lib/other.php` ($GLOBALS)
- `lib/type.php` ($GLOBALS)
- `options/toContent.php` (direct)

### `$padContentType`

- `config/output/download.php` (direct)
- `config/output/web.php` (direct)
- `exits/output/download.php` (direct)
- `lib/output.php` ($GLOBALS)

### `$padCookies`

- `config/config.php` (direct)
- `inits/cookies.php` (direct)

### `$padCount`

- `level/data.php` (direct)
- `level/setup.php` (direct)

### `$padCurl`

- `tags/curl.php` (direct)

### `$padCurrent`

- `at/any/tag.php` (global)
- `at/groups/current.php` (global)
- `info/types/trace/occur/start.php` (direct)
- `level/function.php` (direct)
- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `lib/field/level.php` (global)
- `lib/field/prefix.php` ($GLOBALS)
- `lib/field/tag.php` ($GLOBALS)
- `lib/other.php` (global)
- `occurrence/init.php` (direct)
- `occurrence/set.php` (direct)
- `occurrence/table.php` (direct)
- `tag/fields.php` ($GLOBALS)
- `tag/firstFieldName.php` ($GLOBALS)
- `tag/firstFieldValue.php` ($GLOBALS)

### `$padData`

- `at/any/tag.php` (global)
- `at/groups/level.php` ($GLOBALS)
- `events/data.php` (direct)
- `events/flags.php` (direct)
- `handling/handling.php` (direct)
- `handling/negative/exits.php` (direct)
- `handling/negative/inits.php` (direct)
- `handling/types/dedup.php` (direct)
- `handling/types/first.php` (direct)
- `handling/types/page.php` (direct)
- `handling/types/random.php` (direct)
- `handling/types/reverse.php` (direct)
- `handling/types/row.php` (direct)
- `handling/types/shuffle.php` (direct)
- `handling/types/sort.php` (direct)
- `handling/types/splice.php` (direct)
- `handling/types/start.php` (direct)
- `handling/types/trim.php` (direct)
- `info/types/trace/_lib.php` (global)
- `level/data.php` (direct)
- `level/end.php` (direct)
- `level/setup.php` (direct)
- `level/start_end/end2.php` (direct)
- `level/start_end/start1.php` (direct)
- `level/start_end/start2.php` (direct)
- `level/start.php` (direct)
- `lib/table.php` (global)
- `occurrence/init.php` (direct)
- `options/before.php` (direct)
- `options/go/reset.php` (direct)
- `options/toData.php` (direct)
- `sequence/exits/extra/actions.php` (direct)
- `sequence/exits/extra/chain.php` (direct)
- `sequence/exits/extra/org.php` (direct)
- `sequence/exits/extra/plays.php` (direct)
- `sequence/exits/extra/pull.php` (direct)
- `sequence/exits/extra/set.php` (direct)
- `sequence/exits/return/given.php` (direct)
- `sequence/exits/return/names.php` (direct)
- `sequence/exits/return.php` (direct)
- `sequence/exits/return/return.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/sequence/tag.php` (direct)
- `start/end/dat.php` (direct)
- `tag/count.php` (global)
- `tag/data.php` ($GLOBALS)
- `tag/first.php` (global)
- `tag/keys.php` ($GLOBALS)
- `tag/last.php` (global)
- `tag/remaining.php` (global)
- `tags/go/data.php` (direct)
- `tags/go/store.php` (direct)
- `walk/next.php` (direct)

### `$padDataDefaultEnd`

- `config/config.php` (direct)
- `level/var.php` (direct)

### `$padDataDefaultStart`

- `config/config.php` (direct)
- `level/var.php` (direct)

### `$padDataStore`

- `at/_lib/at.php` (global, $GLOBALS)
- `at/_lib/lib.php` (global)
- `at/types/_lib/check.php` (global)
- `at/types/_lib/new.php` (global)
- `eval/single/data.php` ($GLOBALS)
- `lib/type.php` ($GLOBALS)
- `options/data.php` (direct)
- `options/toData.php` (direct)
- `sequence/exits/data.php` (direct)
- `sequence/inits/direct.php` (global)
- `tags/count.php` (direct)
- `types/data.php` (direct)

### `$padDbRowsFound`

- `lib/db.php` (global)

### `$padDedup`

- `handling/types/dedup.php` (direct)

### `$padDefault`

- `level/data.php` (direct)
- `level/setup.php` (direct)

### `$padDeleteLvl`

- `level/setup.php` (direct)
- `lib/other.php` (global)
- `tags/set.php` (direct)

### `$padDeleteOcc`

- `level/setup.php` (direct)
- `lib/other.php` (global)

### `$padDir`

- `build/dirs.php` (direct)
- `inits/page.php` (direct)
- `lib/other.php` (global, $GLOBALS)
- `start/enter/page.php` (direct)
- `tags/dir.php` (direct)

### `$padDirMode`

- `cache/types/file.php` (global, $GLOBALS)
- `config/config.php` (direct)
- `lib/file.php` (global, $GLOBALS)

### `$padDisplayErrors`

- `error/boot.php` (direct)
- `error/types/php.php` (direct)

### `$padDone`

- `level/setup.php` (direct)
- `lib/other.php` ($GLOBALS)
- `lib/table.php` (global)
- `sequence/plays/add.php` (direct)

### `$padElse`

- `events/flags.php` (direct)
- `info/types/trace/_lib.php` (global)
- `level/base.php` (direct)
- `level/data.php` (direct)
- `level/flags.php` (direct)
- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `options/go/reset.php` (direct)
- `options/toBool.php` (direct)

### `$padEnd`

- `events/parse.php` (direct)
- `level/level.php` (direct)
- `level/pair.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)
- `lib/level.php` (global)

### `$padEndBase`

- `info/types/trace/_lib.php` (global)
- `level/end.php` (direct)
- `level/setup.php` (direct)
- `level/start_end/end1.php` (direct)
- `level/start_end/end2.php` (direct)

### `$padErrorAction`

- `config/config.php` (direct)
- `inits/error.php` (direct)

### `$padErrorLevel`

- `config/config.php` (direct)
- `error/error.php` (direct)

### `$padErrorLog`

- `config/config.php` (direct)
- `error/types/pad.php` (global, $GLOBALS)

### `$padErrorReport`

- `config/config.php` (direct)
- `error/types/pad.php` (global, $GLOBALS)

### `$padErrorReporting`

- `error/boot.php` (direct)
- `error/types/php.php` (direct)

### `$padErrorTry`

- `config/config.php` (direct)
- `inits/config.php` (direct)
- `try/try.php` ($GLOBALS)

### `$padEtag`

- `cache/exits.php` (direct)
- `cache/inits.php` (direct)
- `exits/exits.php` (direct)
- `exits/output/web.php` (direct)
- `info/types/track/_lib.php` (global, $GLOBALS)
- `inits/vars.php` (direct)
- `lib/other.php` ($GLOBALS)
- `lib/output.php` (global, $GLOBALS)

### `$padEvalCnt`

- `inits/vars.php` (direct)

### `$padEvalNextKey`

- `eval/type/parms.php` (direct)

### `$padEventsOption`

- `events/options.php` (direct)

### `$padException`

- `error/error.php` (global, $GLOBALS)
- `lib/dump.php` ($GLOBALS)

### `$padExceptionError`

- `error/error.php` (global)

### `$padExceptionFile`

- `error/error.php` (global)

### `$padExceptionLine`

- `error/error.php` (global)

### `$padExceptionText`

- `error/error.php` (global)
- `lib/dump.php` ($GLOBALS)

### `$padExec`

- `types/script.php` (direct)

### `$padExecArgs`

- `types/script.php` (direct)

### `$padExecOut`

- `types/script.php` (direct)

### `$padExecReturn`

- `types/script.php` (direct)

### `$padExplode`

- `handling/types/splice.php` (direct)

### `$padExtPag`

- `start/enter/ajax.php` (direct)
- `start/enter/get.php` (direct)

### `$padExtQry`

- `start/enter/ajax.php` (direct)
- `start/enter/get.php` (direct)

### `$padFalse`

- `events/false.php` (direct)
- `info/types/trace/_lib.php` (global)
- `level/base.php` (direct)
- `level/go.php` (direct)
- `level/setup.php` (direct)
- `level/split.php` (direct)
- `level/start.php` (direct)

### `$padFast`

- `inits/fast.php` (direct)

### `$padFastLink`

- `config/config.php` (direct)
- `lib/api.php` (global)

### `$padFibonacci`

- `sequence/types/fibonacci/go.php` (direct)

### `$padField`

- `tags/decrement.php` (direct)
- `tags/increment.php` (direct)

### `$padFile`

- `exits/output/download.php` (direct)

### `$padFileDate`

- `config/output/file.php` (direct)
- `lib/other.php` (global)
- `tags/file.php` (direct)

### `$padFileDir`

- `config/output/file.php` (direct)
- `lib/other.php` (global)
- `tags/file.php` (direct)

### `$padFileExtension`

- `config/output/file.php` (direct)
- `lib/other.php` (global)
- `tags/file.php` (direct)

### `$padFileMode`

- `config/config.php` (direct)
- `lib/file.php` (global, $GLOBALS)

### `$padFileName`

- `config/output/file.php` (direct)
- `lib/other.php` (global)
- `tags/file.php` (direct)

### `$padFileNextPage`

- `config/output/file.php` (direct)
- `exits/output/file.php` (direct)

### `$padFiles`

- `tags/files.php` (direct)

### `$padFilesArray`

- `tags/files.php` (direct)

### `$padFilesBase`

- `tags/files.php` (direct)

### `$padFilesDir`

- `tags/files.php` (direct)

### `$padFilesDirectory`

- `tags/files.php` (direct)

### `$padFilesExclude`

- `tags/files.php` (direct)

### `$padFilesExt`

- `tags/files.php` (direct)

### `$padFilesFile`

- `tags/files.php` (direct)

### `$padFilesGroup`

- `tags/files.php` (direct)

### `$padFilesIncludeHidden`

- `tags/files.php` (direct)

### `$padFilesIterator`

- `tags/files.php` (direct)

### `$padFilesMask`

- `tags/files.php` (direct)

### `$padFilesName`

- `tags/files.php` (direct)

### `$padFilesOnlyDirs`

- `tags/files.php` (direct)

### `$padFilesOnlyFiles`

- `tags/files.php` (direct)

### `$padFilesPath`

- `tags/files.php` (direct)

### `$padFilesRecursive`

- `tags/files.php` (direct)

### `$padFilesScan`

- `tags/files.php` (direct)

### `$padFileTimeStamp`

- `config/output/file.php` (direct)
- `lib/other.php` (global)
- `tags/file.php` (direct)

### `$padFileUniqId`

- `config/output/file.php` (direct)
- `lib/other.php` (global)
- `tags/file.php` (direct)

### `$padFirst`

- `level/between.php` (direct)
- `level/level.php` (direct)
- `level/var.php` (direct)

### `$padFld`

- `events/var_start.php` (direct)
- `level/var.php` (direct)

### `$padFldChk`

- `level/var.php` (direct)

### `$padFmtDate`

- `config/config.php` (direct)
- `functions/date.php` ($GLOBALS)

### `$padFmtTime`

- `config/config.php` (direct)

### `$padFmtTimestamp`

- `config/config.php` (direct)

### `$padForceDataName`

- `at/_lib/at.php` ($GLOBALS)
- `level/setup.php` (direct)
- `lib/data.php` (global)

### `$padForceTagName`

- `at/_lib/at.php` ($GLOBALS)
- `level/name.php` (direct)
- `level/setup.php` (direct)
- `tags/go/data.php` (direct)

### `$padFunctionData`

- `start/enter/function.php` (direct)

### `$padFunctionPad`

- `start/enter/function.php` (direct)

### `$padFunctionReturn`

- `start/enter/function.php` (direct)

### `$padGetCall`

- `get/go/call.php` (direct)
- `get/include.php` (direct)
- `get/page.php` (direct)

### `$padGetData`

- `get/go/call.php` (direct)

### `$padGetName`

- `eval/type/type.php` (direct)
- `get/content.php` (direct)
- `get/include.php` (direct)
- `get/page.php` (direct)
- `level/go.php` (direct)
- `options/content.php` (direct)
- `options/data.php` (direct)
- `options/go/options.php` (direct)
- `options/go/reset.php` (direct)
- `types/data.php` (direct)

### `$padGetPad`

- `get/go/call.php` (direct)

### `$padGetPhp`

- `get/go/call.php` (direct)

### `$padGiven`

- `level/setup.php` (direct)
- `level/split.php` (direct)

### `$padGo`

- `inits/host.php` (direct)
- `lib/curl.php` ($GLOBALS)
- `lib/field/field.php` ($GLOBALS)
- `lib/other.php` (global)
- `lib/page.php` ($GLOBALS)

### `$padGoExt`

- `inits/host.php` (direct)
- `lib/api.php` (global)
- `lib/curl.php` ($GLOBALS)
- `lib/page.php` ($GLOBALS)

### `$padGzip`

- `config/config.php` (direct)
- `lib/output.php` (global, $GLOBALS)

### `$padHand`

- `handling/handling.php` (direct)

### `$padHandBoth`

- `handling/types/trim.php` (direct)

### `$padHandCnt`

- `handling/handling.php` (direct)
- `handling/types/first.php` (direct)
- `handling/types/trim.php` (direct)

### `$padHandCount`

- `handling/types/splice.php` (direct)
- `handling/types/start.php` (direct)

### `$padHandEnd`

- `handling/types/page.php` (direct)
- `handling/types/row.php` (direct)
- `handling/types/start.php` (direct)

### `$padHandKeysNew`

- `handling/negative/exits.php` (direct)

### `$padHandKeysOld`

- `handling/negative/exits.php` (direct)

### `$padHandLeft`

- `handling/types/trim.php` (direct)

### `$padHandName`

- `events/handling.php` (direct)
- `handling/handling.php` (direct)
- `handling/types/first.php` (direct)
- `handling/types/splice.php` (direct)

### `$padHandNegative`

- `handling/handling.php` (direct)

### `$padHandOld`

- `handling/negative/exits.php` (direct)
- `handling/negative/inits.php` (direct)

### `$padHandOldKey`

- `handling/negative/exits.php` (direct)

### `$padHandP1`

- `handling/types/splice.php` (direct)

### `$padHandP2`

- `handling/types/splice.php` (direct)

### `$padHandPage`

- `handling/types/page.php` (direct)

### `$padHandParm`

- `handling/handling.php` (direct)
- `handling/types/splice.php` (direct)

### `$padHandRand`

- `handling/types/random.php` (direct)

### `$padHandRandCount`

- `handling/types/random.php` (direct)

### `$padHandRandDuplicates`

- `handling/types/random.php` (direct)

### `$padHandRandKeys`

- `handling/types/random.php` (direct)

### `$padHandRandOrderly`

- `handling/types/random.php` (direct)

### `$padHandRight`

- `handling/types/trim.php` (direct)

### `$padHandRows`

- `handling/types/page.php` (direct)
- `handling/types/start.php` (direct)

### `$padHandStart`

- `handling/types/page.php` (direct)
- `handling/types/row.php` (direct)
- `handling/types/start.php` (direct)

### `$padHeaders`

- `info/types/track/_lib.php` ($GLOBALS)
- `lib/dump.php` ($GLOBALS)
- `lib/other.php` ($GLOBALS)

### `$padHit`

- `events/flags.php` (direct)
- `info/types/trace/_lib.php` (global)
- `level/flags.php` (direct)
- `level/go.php` (direct)
- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `options/go/reset.php` (direct)

### `$padHost`

- `inits/host.php` (direct)
- `lib/api.php` (global)
- `lib/curl.php` ($GLOBALS)
- `lib/page.php` ($GLOBALS)
- `tags/curl.php` (direct)

### `$padHR`

- `info/types/stats/end.php` (global)
- `inits/inits.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padHttpHost`

- `inits/host.php` (direct)

### `$padI`

- `info/types/trace/occur/end.php` (direct)
- `info/types/trace/occur/start.php` (direct)
- `tag/fields.php` (direct)

### `$padIdx`

- `at/any/tag.php` (direct)
- `at/any/tags.php` (direct)
- `at/groups/current.php` (direct)
- `at/groups/function.php` (direct)
- `at/groups/level.php` (direct)
- `at/groups/options.php` (direct)
- `at/groups/parameters.php` (direct)
- `at/groups/saved.php` (direct)
- `at/groups/tables.php` (direct)
- `at/groups/variables.php` (direct)
- `at/_lib/at.php` (direct)
- `lib/field/tag.php` (direct)
- `tag/count.php` (direct)
- `tag/current.php` (direct)
- `tag/data.php` (direct)
- `tag/done.php` (direct)
- `tag/even.php` (direct)
- `tag/fields.php` (direct)
- `tag/firstFieldName.php` (direct)
- `tag/firstFieldValue.php` (direct)
- `tag/key.php` (direct)
- `tag/keys.php` (direct)
- `tag/last.php` (direct)
- `tag/name.php` (direct)
- `tag/option.php` (direct)
- `tag/options.php` (direct)
- `tag/parameter.php` (direct)
- `tag/parameters.php` (direct)
- `tag/remaining.php` (direct)
- `tag/variable.php` (direct)
- `tag/variables.php` (direct)

### `$padIf`

- `tags/case.php` (direct)
- `tags/if.php` (direct)

### `$padIgnored`

- `inits/clean.php` (direct)
- `lib/dump.php` (direct)
- `lib/exit.php` (direct)
- `start/enter/restart.php` (direct)

### `$padIncCheck`

- `lib/other.php` (direct)

### `$padIncDir`

- `lib/other.php` (direct)

### `$padIncDirs`

- `lib/other.php` (direct)

### `$padInclude`

- `build/base.php` (direct)
- `inits/vars.php` (direct)
- `lib/other.php` ($GLOBALS)
- `start/enter/page.php` (direct)

### `$padInfo`

- `at/_lib/at.php` ($GLOBALS)
- `build/build.php` (direct)
- `build/split.php` (direct)
- `call/_call.php` ($GLOBALS)
- `call/_init.php` ($GLOBALS)
- `call/_once.php` ($GLOBALS)
- `config/config.php` (direct)
- `config/info/go.php` (direct)
- `eval/fast.php` ($GLOBALS)
- `eval/type/type.php` ($GLOBALS)
- `handling/handling.php` (direct)
- `info/end/config.php` ($GLOBALS)
- `info/start/config.php` (direct)
- `info/start/tag.php` (direct)
- `inits/config.php` (direct)
- `inits/configSet.php` (direct)
- `inits/info.php` (direct)
- `inits/vars.php` (direct)
- `level/end.php` (direct)
- `level/flags.php` (direct)
- `level/go.php` (direct)
- `level/parms/parms.php` (direct)
- `level/setup.php` (direct)
- `level/split.php` (direct)
- `level/start_end/end1.php` (direct)
- `level/start_end/start1.php` (direct)
- `level/start.php` (direct)
- `lib/curl.php` ($GLOBALS)
- `lib/db.php` ($GLOBALS)
- `lib/dump.php` (direct)
- `lib/field/field.php` ($GLOBALS)
- `lib/file.php` ($GLOBALS)
- `lib/output.php` (global, $GLOBALS)
- `occurrence/end.php` (direct)
- `occurrence/occurrence.php` (direct)
- `sequence/build/vars.php` (direct)
- `sequence/exits/info.php` (direct)
- `sequence/exits/start.php` (direct)
- `sequence/inits/direct.php` (global)
- `tags/go/store.php` (direct)
- `walk/end.php` (direct)
- `walk/next.php` (direct)

### `$padInfoCnt`

- `info/end/config.php` ($GLOBALS)
- `info/end/tag.php` (direct)
- `info/start/config.php` (direct)
- `info/start/tag.php` (direct)
- `inits/vars.php` (direct)
- `lib/info.php` (global)

### `$padInfoList`

- `inits/config.php` (direct)
- `inits/configSet.php` (direct)

### `$padInfoStarted`

- `info/start/config.php` (direct)
- `lib/exit.php` ($GLOBALS)

### `$padInfoStats`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/stats.php` (direct)
- `info/end/config.php` ($GLOBALS)
- `info/start/config.php` (direct)
- `inits/info.php` (direct)
- `lib/info.php` ($GLOBALS)
- `lib/output.php` (global, $GLOBALS)

### `$padInfoTmp`

- `events/levelStart.php` (direct)

### `$padInfoTrace`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/base.php` ($GLOBALS)
- `events/build.php` ($GLOBALS)
- `events/cache.php` ($GLOBALS)
- `events/call.php` ($GLOBALS)
- `events/curl.php` ($GLOBALS)
- `events/data.php` ($GLOBALS)
- `events/eval/after.php` ($GLOBALS)
- `events/eval/end.php` ($GLOBALS)
- `events/eval/error.php` ($GLOBALS)
- `events/eval/fast.php` ($GLOBALS)
- `events/eval/go.php` ($GLOBALS)
- `events/eval/parse.php` ($GLOBALS)
- `events/eval/start.php` ($GLOBALS)
- `events/false.php` (direct)
- `events/fieldEnd.php` ($GLOBALS)
- `events/fieldStart.php` ($GLOBALS)
- `events/flags.php` ($GLOBALS)
- `events/get.php` ($GLOBALS)
- `events/levelEnd.php` ($GLOBALS)
- `events/levelStart.php` ($GLOBALS)
- `events/occurEnd.php` ($GLOBALS)
- `events/occurStart.php` ($GLOBALS)
- `events/parms.php` ($GLOBALS)
- `events/parse.php` ($GLOBALS)
- `events/put.php` ($GLOBALS)
- `events/resultOcc.php` ($GLOBALS)
- `events/sequence.php` ($GLOBALS)
- `events/setup.php` ($GLOBALS)
- `events/sql.php` ($GLOBALS)
- `events/stats.php` ($GLOBALS)
- `events/store.php` ($GLOBALS)
- `events/true.php` (direct)
- `events/type.php` (direct)
- `events/var_end.php` ($GLOBALS)
- `events/var_start.php` ($GLOBALS)
- `info/end/config.php` ($GLOBALS)
- `info/start/config.php` (direct)
- `inits/info.php` (direct)
- `lib/info.php` ($GLOBALS)

### `$padInfoTraceAddLine`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceBuild`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/build.php` (direct)

### `$padInfoTraceCall`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/cache.php` (direct)
- `events/call.php` ($GLOBALS)

### `$padInfoTraceChilds`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/occur/end.php` (direct)

### `$padInfoTraceCnt`

- `info/types/trace/end.php` (global)
- `info/types/trace/_lib.php` ($GLOBALS)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceContent`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/base.php` (direct)
- `events/false.php` (direct)
- `events/resultOcc.php` (direct)
- `events/true.php` (direct)
- `events/type.php` (direct)

### `$padInfoTraceCurl`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/curl.php` ($GLOBALS)

### `$padInfoTraceData`

- `events/eval/parse.php` (global)

### `$padInfoTraceDataLvl`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/data.php` (direct)

### `$padInfoTraceDataOcc`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/occur/start.php` (direct)

### `$padInfoTraceDefault`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/data.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/occur/start.php` (direct)

### `$padInfoTraceDir`

- `info/types/trace/end.php` (global)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/_lib.php` (global, $GLOBALS)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceDouble`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/base.php` (direct)
- `events/false.php` (direct)
- `events/resultOcc.php` (direct)
- `events/true.php` (direct)
- `events/type.php` (direct)

### `$padInfoTraceDump`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/end.php` (global)

### `$padInfoTraceEndLvl`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceEndOcc`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceEval`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/eval/after.php` ($GLOBALS)
- `events/eval/end.php` ($GLOBALS)
- `events/eval/error.php` ($GLOBALS)
- `events/eval/fast.php` ($GLOBALS)
- `events/eval/go.php` ($GLOBALS)
- `events/eval/parse.php` ($GLOBALS)
- `events/eval/start.php` ($GLOBALS)

### `$padInfoTraceEvalData`

- `events/eval/after.php` (global)
- `events/eval/end.php` (global)
- `events/eval/error.php` (global)
- `events/eval/go.php` (global)
- `events/eval/parse.php` (direct)
- `events/eval/start.php` (global)

### `$padInfoTraceFalse`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/false.php` (direct)

### `$padInfoTraceFALSE`

- `config/info/none.php` (direct)

### `$padInfoTraceField`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/fieldEnd.php` ($GLOBALS)
- `events/fieldStart.php` ($GLOBALS)

### `$padInfoTraceFlags`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/flags.php` (direct)

### `$padInfoTraceGet`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/get.php` ($GLOBALS)

### `$padInfoTraceHideDefault`

- `info/types/trace/_lib.php` (global)

### `$padInfoTraceId`

- `info/types/trace/_lib.php` (global)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceIds`

- `info/types/trace/_lib.php` (global)

### `$padInfoTraceInitsExits`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceKeepEmpty`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/level/end.php` (direct)

### `$padInfoTraceLevel`

- `info/types/trace/end.php` (global)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/occur/end.php` (direct)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceLevelBase`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/base.php` ($GLOBALS)

### `$padInfoTraceLevelChilds`

- `info/types/trace/level/end.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/occur/start.php` (direct)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceLines`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceLocal`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceLocalChk`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/end.php` (global)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/occur/end.php` (direct)

### `$padInfoTraceLvl`

- `info/types/trace/_lib.php` ($GLOBALS)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceMaxLevel`

- `info/types/trace/end.php` (global)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceMore`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceOccurChilds`

- `info/types/trace/level/end.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/occur/end.php` (direct)
- `info/types/trace/occur/start.php` (direct)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceOccurId`

- `info/types/trace/_lib.php` (global)

### `$padInfoTraceOccurs`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceOccursSmart`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceOptions`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceParms`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/parms.php` ($GLOBALS)

### `$padInfoTraceParse`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/parse.php` (direct)

### `$padInfoTraceParseEnd`

- `events/parse.php` (direct)

### `$padInfoTraceParseStart`

- `events/parse.php` (direct)

### `$padInfoTracePut`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/put.php` ($GLOBALS)

### `$padInfoTraceRequest`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceResultLvl`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/type.php` (direct)

### `$padInfoTraceResultOcc`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/resultOcc.php` (direct)

### `$padInfoTraceRoot`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceSequence`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/sequence.php` ($GLOBALS)

### `$padInfoTraceSession`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceSkipLevel`

- `info/types/trace/end.php` (global)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceSql`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/sql.php` ($GLOBALS)

### `$padInfoTraceStartEnd`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/end.php` (global)
- `info/types/trace/start.php` (direct)

### `$padInfoTraceStartEndLvl`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/level/start.php` (direct)

### `$padInfoTraceStartEndOcc`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/occur/end.php` (direct)
- `info/types/trace/occur/start.php` (direct)

### `$padInfoTraceStartLvl`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceStartOcc`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTraceStatus`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/level/end.php` (direct)

### `$padInfoTraceStore`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/store.php` ($GLOBALS)

### `$padInfoTraceTree`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceTrue`

- `config/info/all.php` (direct)
- `config/info/trace.php` (direct)
- `events/true.php` (direct)

### `$padInfoTraceType`

- `info/types/trace/_lib.php` (global)

### `$padInfoTraceTypes`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceTypesDir`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `info/types/trace/_lib.php` (global)

### `$padInfoTraceVar`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)
- `events/var_end.php` (direct)
- `events/var_start.php` (direct)

### `$padInfoTraceXref`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/trace.php` (direct)

### `$padInfoTrack`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/track.php` (direct)
- `info/end/config.php` ($GLOBALS)
- `info/start/config.php` (direct)
- `inits/info.php` (direct)
- `lib/info.php` ($GLOBALS)

### `$padInfoTrackDbData`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/track.php` (direct)
- `info/types/track/end.php` ($GLOBALS)

### `$padInfoTrackDbRequest`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/track.php` (direct)
- `info/types/track/end.php` ($GLOBALS)
- `info/types/track/_lib.php` ($GLOBALS)

### `$padInfoTrackDbSession`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/track.php` (direct)
- `info/types/track/end.php` ($GLOBALS)

### `$padInfoTrackFileData`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/track.php` (direct)
- `info/types/track/end.php` ($GLOBALS)

### `$padInfoTrackFileRequest`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/track.php` (direct)
- `info/types/track/end.php` ($GLOBALS)
- `info/types/track/start.php` ($GLOBALS)

### `$padInfoType`

- `inits/config.php` (direct)
- `inits/configSet.php` (direct)

### `$padInfoXml`

- `config/info/all.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/xml.php` (direct)
- `events/levelEnd.php` ($GLOBALS)
- `events/occurEnd.php` ($GLOBALS)
- `events/occurStart.php` ($GLOBALS)
- `events/parms.php` ($GLOBALS)
- `events/setup.php` ($GLOBALS)
- `info/end/config.php` ($GLOBALS)
- `info/start/config.php` (direct)
- `inits/info.php` (direct)
- `lib/info.php` ($GLOBALS)

### `$padInfoXmlCompact`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/xml.php` (direct)
- `info/types/xml/json.php` (global)
- `info/types/xml/_lib.php` (global)
- `info/types/xml/occur/end.php` ($GLOBALS)
- `info/types/xml/occur/start.php` ($GLOBALS)
- `info/types/xml/start.php` ($GLOBALS)

### `$padInfoXmlDepth`

- `info/types/xml/json.php` (global)
- `info/types/xml/_lib.php` (global)
- `info/types/xml/start.php` (direct)

### `$padInfoXmlEvent`

- `info/types/xml/event.php` (direct)

### `$padInfoXmlEvents`

- `info/types/xml/event.php` (direct)
- `info/types/xml/json.php` (global)
- `info/types/xml/_lib.php` (global)
- `info/types/xml/start.php` (direct)

### `$padInfoXmlEventType`

- `info/types/xml/event.php` (direct)
- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)

### `$padInfoXmlFile`

- `info/types/xml/end.php` ($GLOBALS)
- `info/types/xml/json.php` (global)
- `info/types/xml/_lib.php` (global)
- `info/types/xml/start.php` (direct)

### `$padInfoXmlId`

- `info/types/xml/event.php` (direct)
- `info/types/xml/start.php` (direct)

### `$padInfoXmlLevel`

- `info/types/xml/event.php` (direct)
- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/parent.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)
- `info/types/xml/start.php` (direct)

### `$padInfoXmlLvl`

- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)

### `$padInfoXmlNew`

- `info/types/xml/level/start.php` (direct)

### `$padInfoXmlOcc`

- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)

### `$padInfoXmlParent`

- `info/types/xml/level/parent.php` (direct)

### `$padInfoXmlParentOcc`

- `info/types/xml/level/parent.php` (direct)

### `$padInfoXmlParms`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/xml.php` (direct)
- `info/types/xml/level/parms.php` (direct)

### `$padInfoXmlTidy`

- `config/info/all.php` (direct)
- `config/info/none.php` (direct)
- `config/info/xml.php` (direct)
- `info/types/xml/end.php` ($GLOBALS)

### `$padInfoXmlTree`

- `info/types/xml/json.php` (global)
- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/parent.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/_lib.php` (global)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)
- `info/types/xml/start.php` (direct)

### `$padInfoXref`

- `config/info/all.php` (direct)
- `config/info/info.php` (direct)
- `config/info/myInfo.php` (direct)
- `config/info/none.php` (direct)
- `config/info/xref.php` (direct)
- `events/atGroups.php` ($GLOBALS)
- `events/atProperties.php` ($GLOBALS)
- `events/atTypes.php` ($GLOBALS)
- `events/content.php` ($GLOBALS)
- `events/else.php` ($GLOBALS)
- `events/end.php` ($GLOBALS)
- `events/fieldClassic.php` ($GLOBALS)
- `events/functionsFast.php` ($GLOBALS)
- `events/functions.php` ($GLOBALS)
- `events/go.php` ($GLOBALS)
- `events/handling.php` ($GLOBALS)
- `events/levelStart.php` ($GLOBALS)
- `events/optional.php` ($GLOBALS)
- `events/option.php` ($GLOBALS)
- `events/options.php` ($GLOBALS)
- `events/parms.php` ($GLOBALS)
- `events/sequence.php` ($GLOBALS)
- `events/start.php` ($GLOBALS)
- `events/tag.php` ($GLOBALS)
- `info/end/config.php` ($GLOBALS)
- `info/start/config.php` (direct)
- `info/types/xref/_lib.php` ($GLOBALS)
- `inits/info.php` (direct)
- `lib/info.php` ($GLOBALS)

### `$padInfoXrefSource`

- `info/types/xref/_lib.php` (global)
- `info/types/xref/start.php` (direct)

### `$padJ`

- `info/types/trace/occur/start.php` (direct)

### `$padK1`

- `sequence/exits/extra/chain.php` (direct)

### `$padK2`

- `handling/types/sort.php` (direct)
- `occurrence/set.php` (direct)
- `occurrence/table.php` (direct)
- `sequence/exits/extra/chain.php` (direct)

### `$padK3`

- `functions/in.php` (direct)

### `$padKey`

- `level/setup.php` (direct)
- `lib/table.php` (global)
- `occurrence/init.php` (direct)
- `start/end/dat.php` (direct)
- `tag/first.php` (global)
- `tag/key.php` (global)
- `tag/last.php` (global)

### `$padLastPull`

- `inits/vars.php` (direct)

### `$padLastPush`

- `inits/vars.php` (direct)
- `sequence/exits/extra/set.php` (direct)
- `sequence/exits/store/check.php` (direct)
- `sequence/exits/store/last.php` (direct)
- `sequence/exits/store/set.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/find/pull.php` (direct)
- `sequence/inits/set.php` (direct)

### `$padLen`

- `exits/output/download.php` (direct)
- `exits/output.php` (direct)
- `info/types/track/_lib.php` ($GLOBALS)
- `inits/vars.php` (direct)
- `lib/other.php` ($GLOBALS)
- `lib/output.php` ($GLOBALS)

### `$padLibDirectory`

- `build/_lib.php` (direct)
- `inits/database.php` (direct)
- `inits/lib.php` (direct)

### `$padLibFile`

- `inits/database.php` (direct)
- `inits/lib.php` (direct)

### `$padLibIterator`

- `build/_lib.php` (direct)
- `inits/database.php` (direct)
- `inits/lib.php` (direct)

### `$padLibOne`

- `build/_lib.php` (direct)
- `inits/database.php` (direct)
- `inits/lib.php` (direct)

### `$padLocalBox`

- `types/go/local.php` (direct)

### `$padLocalData`

- `types/go/local.php` (direct)

### `$padLocalExt`

- `types/go/local.php` (direct)

### `$padLocalFile`

- `lib/other.php` (direct)
- `types/go/local.php` (direct)
- `types/local.php` (direct)

### `$padLocalName`

- `types/go/local.php` (direct)

### `$padLocalParts`

- `types/go/local.php` (direct)

### `$padLog`

- `info/types/stats/end.php` (global)
- `info/types/trace/start.php` (direct)
- `info/types/track/_lib.php` (global)
- `inits/ids.php` (direct)
- `lib/dump.php` ($GLOBALS)

### `$padLvlFun`

- `level/level.php` (direct)
- `level/setup.php` (direct)
- `start/function.php` (direct)

### `$padLvlFunVar`

- `at/any/tag.php` (global)
- `at/groups/function.php` ($GLOBALS)
- `level/function.php` (direct)
- `level/setup.php` (direct)
- `lib/field/level.php` (global)

### `$padLvlId`

- `inits/vars.php` (direct)
- `level/setup.php` (direct)

### `$padLvlIds`

- `level/setup.php` (direct)

### `$padMakeFile`

- `tags/go/data.php` (direct)

### `$padMakeType`

- `tags/go/data.php` (direct)

### `$padMicro`

- `info/types/stats/end.php` (global)
- `inits/inits.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padMyTidy`

- `config/config.php` (direct)
- `config/output/console.php` (direct)
- `exits/exits.php` (direct)
- `exits/tidy.php` (direct)

### `$padMyTidyNoEmptyLines`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padMyTidyNoIndent`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padMyTidyNoNewLines`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padMyTidyRemoveWhitespace`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padMyTidySanitize`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padMyTidyTabToSpace`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padMyTidyTrim`

- `config/tidy.php` (direct)
- `exits/myTidy.php` (direct)

### `$padName`

- `at/_lib/lib.php` (global)
- `events/store.php` (direct)
- `handling/types/dedup.php` (direct)
- `level/name.php` (direct)
- `level/setup.php` (direct)
- `lib/data.php` (global)
- `lib/dump.php` ($GLOBALS)
- `lib/field/level.php` (global)
- `lib/field/lib.php` (global)
- `lib/field/prefix.php` ($GLOBALS)
- `occurrence/set.php` (direct)
- `sequence/inits/name.php` (direct)
- `tag/name.php` (global)
- `tags/go/store.php` (direct)
- `types/go/local.php` ($GLOBALS)

### `$padNoNo`

- `config/config.php` (direct)
- `inits/nono.php` (direct)

### `$padNull`

- `events/flags.php` (direct)
- `info/types/trace/_lib.php` (global)
- `level/base.php` (direct)
- `level/data.php` (direct)
- `level/flags.php` (direct)
- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `options/go/reset.php` (direct)
- `options/toBool.php` (direct)

### `$padOccur`

- `info/_lib/_lib.php` (global)
- `info/types/trace/_lib.php` (global)
- `info/types/trace/occur/end.php` (direct)
- `info/types/trace/occur/start.php` (direct)
- `info/types/xml/event.php` (direct)
- `info/types/xml/level/end.php` (direct)
- `info/types/xml/level/parent.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `info/types/xml/occur/start.php` (direct)
- `level/end.php` (direct)
- `level/setup.php` (direct)
- `occurrence/init.php` (direct)
- `tag/count.php` (global)
- `tag/current.php` (global)
- `tag/done.php` (global)
- `tag/even.php` (global)
- `tag/first.php` (global)
- `tag/remaining.php` (global)

### `$padOccurStart`

- `level/end.php` (direct)
- `level/setup.php` (direct)
- `occurrence/init.php` (direct)

### `$padOpenClose`

- `build/split.php` (direct)
- `level/split.php` (direct)

### `$padOpt`

- `at/any/tag.php` (global)
- `at/groups/parameters.php` (global)
- `events/parms.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `level/close.php` (direct)
- `level/function.php` (direct)
- `level/go.php` (direct)
- `level/parms/parameter.php` (direct)
- `level/parms/parms.php` (direct)
- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `lib/field/level.php` (global)
- `occurrence/init.php` (direct)
- `sequence/inits/find/parm/inits.php` (direct)
- `tag/parameter.php` (global)
- `tag/parameters.php` ($GLOBALS)
- `tags/decrement.php` (direct)
- `tags/echo.php` (direct)
- `tags/go/store.php` (direct)
- `tags/increment.php` (direct)
- `tags/switch.php` (direct)
- `types/function.php` (direct)
- `types/php.php` (direct)
- `types/script.php` (direct)
- `walk/end.php` (direct)

### `$padOptAt`

- `at/any/tag.php` (direct)
- `at/groups/parameters.php` (direct)

### `$padOptionName`

- `options/go/options.php` (direct)

### `$padOptions`

- `options/go/app.php` (direct)
- `options/go/callback.php` (direct)
- `options/go/end.php` (direct)
- `options/go/options.php` (direct)
- `options/go/start.php` (direct)

### `$padOptionsAppStart`

- `level/parms/option.php` (direct)
- `level/setup.php` (direct)
- `level/start.php` (direct)
- `options/go/options.php` (direct)

### `$padOptionsCallback`

- `lib/callback.php` (global)

### `$padOptionsWalk`

- `options/go/options.php` (direct)

### `$padOptOne`

- `level/var.php` (direct)

### `$padOpts`

- `events/var_start.php` (direct)
- `level/var.php` (direct)

### `$padOrg`

- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)

### `$padOrgSet`

- `level/setup.php` (direct)
- `lib/level.php` (global)

### `$padOut`

- `events/parse.php` (direct)
- `events/resultOcc.php` (direct)
- `info/types/trace/occur/end.php` (direct)
- `info/types/xml/occur/end.php` (direct)
- `level/pair.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)
- `lib/level.php` (global)
- `occurrence/end.php` (direct)
- `occurrence/init.php` (direct)
- `start/enter/function.php` (global)
- `start/pad.php` (direct)

### `$padOutput`

- `cache/exits.php` (direct)
- `cache/inits.php` (direct)
- `exits/exits.php` (direct)
- `exits/myTidy.php` (direct)
- `exits/output/console.php` (direct)
- `exits/output/download.php` (direct)
- `exits/output/file.php` (direct)
- `exits/output.php` (direct)
- `exits/tidy.php` (direct)
- `info/types/track/_lib.php` (global, $GLOBALS)
- `inits/vars.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `lib/output.php` (global, $GLOBALS)

### `$padOutputType`

- `cache/inits.php` (direct)
- `config/config.php` (direct)
- `exits/output.php` (direct)
- `inits/config.php` (direct)
- `inits/configSet.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `lib/exit.php` ($GLOBALS)
- `tags/output.php` (direct)

### `$padPage`

- `build/page.php` (direct)
- `info/types/trace/start.php` (direct)
- `info/types/xref/_lib.php` (global)
- `inits/fast.php` (direct)
- `inits/nono.php` (direct)
- `inits/page.php` (direct)
- `lib/api.php` (direct)
- `lib/curl.php` ($GLOBALS)
- `lib/dump.php` ($GLOBALS)
- `lib/other.php` (global, $GLOBALS)
- `start/enter/page.php` (direct)
- `start/enter/restart.php` (direct)

### `$padPageLevel`

- `inits/vars.php` (direct)

### `$padPair`

- `level/setup.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `options/toData.php` (direct)
- `sequence/exits/store/store.php` (direct)
- `tags/set.php` (direct)

### `$padPairCheck`

- `level/tag.php` (direct)

### `$padPairSet`

- `inits/level.php` (direct)
- `level/level.php` (direct)
- `level/pair.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)

### `$padPairTag`

- `level/tag.php` (direct)

### `$padParm`

- `level/go.php` (direct)
- `lib/data.php` (direct)
- `occurrence/init.php` (direct)
- `occurrence/set.php` (direct)
- `sequence/types/range/build.php` (direct)
- `start/enter/ajax.php` (direct)
- `start/enter/get.php` (direct)
- `start/enter/page.php` (direct)
- `start/enter/redirect.php` (direct)
- `tags/check.php` (direct)
- `tags/count.php` (direct)
- `tags/curl.php` (direct)
- `tags/dir.php` (direct)
- `tags/error.php` (direct)
- `tags/exception.php` (direct)
- `tags/exists.php` (direct)
- `tags/files.php` (direct)
- `tags/go/data.php` (direct)
- `tags/go/store.php` (direct)
- `tags/output.php` (direct)
- `tags/record.php` (direct)
- `tags/restart.php` (direct)
- `types/property.php` (direct)
- `walk/end.php` (direct)

### `$padParmParse`

- `at/groups/variables.php` (global)
- `level/parms/variable.php` (direct)
- `level/setup.php` (direct)

### `$padParms`

- `events/options.php` (direct)
- `handling/handling.php` (direct)
- `level/parms/parms.php` (direct)
- `level/setup.php` (direct)
- `sequence/actions/inits.php` (direct)
- `sequence/exits/info/options.php` (direct)
- `sequence/exits/start.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/find/loop.php` (direct)
- `sequence/inits/find/parm/quick.php` (direct)
- `sequence/plays/inits.php` (direct)
- `tags/case.php` (direct)
- `tags/if.php` (direct)
- `tags/while.php` (direct)

### `$padParmsOne`

- `sequence/inits/find/loop.php` (direct)

### `$padParmsSet`

- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)

### `$padParmsSetName`

- `level/parms/option.php` (direct)
- `level/parms/parameter.php` (direct)
- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)

### `$padParmsSetType`

- `level/parms/option.php` (direct)
- `level/parms/parameter.php` (direct)
- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)

### `$padParmsSetValue`

- `level/parms/option.php` (direct)
- `level/parms/parameter.php` (direct)
- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)

### `$padParmsType`

- `level/setup.php` (direct)

### `$padPath`

- `inits/page.php` (direct)
- `start/enter/page.php` (direct)

### `$padPipe`

- `level/var.php` (direct)

### `$padPipeAfter`

- `level/pipes/after.php` (direct)
- `level/setup.php` (direct)

### `$padPipeAfterSet`

- `level/pipes/end.php` (direct)
- `level/pipes/start.php` (direct)
- `level/setup.php` (direct)

### `$padPipeBefore`

- `level/pipes/before.php` (direct)
- `level/setup.php` (direct)

### `$padPipeBeforeSet`

- `level/pipes/start.php` (direct)
- `level/setup.php` (direct)

### `$padPos`

- `build/split.php` (direct)
- `level/pair.php` (direct)
- `level/split.php` (direct)
- `level/tag.php` (direct)
- `tags/case.php` (direct)
- `tags/if.php` (direct)

### `$padPrefix`

- `level/setup.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/set.php` (direct)

### `$padPrm`

- `at/any/tag.php` (global)
- `at/groups/options.php` (global)
- `events/parms.php` (direct)
- `handling/handling.php` (direct)
- `handling/types/end.php` (direct)
- `handling/types/page.php` (direct)
- `handling/types/random.php` (direct)
- `handling/types/row.php` (direct)
- `handling/types/rows.php` (direct)
- `handling/types/sort.php` (direct)
- `handling/types/start.php` (direct)
- `handling/types/trim.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `level/end.php` (direct)
- `level/function.php` (direct)
- `level/name.php` (direct)
- `level/parms/option.php` (direct)
- `level/setup.php` (direct)
- `level/start.php` (direct)
- `lib/callback.php` (global)
- `lib/data.php` (global)
- `lib/db.php` (global)
- `lib/dump.php` ($GLOBALS)
- `lib/field/level.php` (global)
- `lib/field/parm.php` ($GLOBALS)
- `lib/other.php` (global)
- `lib/table.php` (global)
- `occurrence/occurrence.php` (direct)
- `options/callback.php` ($GLOBALS)
- `options/go/options.php` (direct)
- `options/go/reset.php` (direct)
- `options/toBool.php` (direct)
- `options/toContent.php` (direct)
- `options/toData.php` (direct)
- `sequence/actions/types/randomize.php` (direct)
- `sequence/actions/types/trim.php` (direct)
- `sequence/exits/done.php` (direct)
- `sequence/inits/check/check.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/parms.php` (direct)
- `tag/option.php` (global)
- `tag/options.php` ($GLOBALS)
- `tags/curl.php` (direct)

### `$padPrmEval`

- `level/parms/parameter.php` (direct)

### `$padPrmKind`

- `events/options.php` (direct)
- `handling/handling.php` (direct)
- `sequence/exits/info/options.php` (direct)
- `sequence/exits/start.php` (direct)
- `sequence/plays/inits.php` (direct)

### `$padPrmName`

- `events/option.php` (direct)
- `events/options.php` (direct)
- `handling/handling.php` (direct)
- `level/parms/option.php` (direct)
- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)
- `sequence/exits/info/options.php` (direct)
- `sequence/exits/start.php` (direct)
- `sequence/inits/find/add.php` (direct)
- `sequence/inits/find/loop.php` (direct)
- `sequence/plays/add.php` (direct)
- `sequence/plays/inits.php` (direct)

### `$padPrmOne`

- `level/parms/parameter.php` (direct)
- `level/parms/parms.php` (direct)

### `$padPrmParse`

- `level/between.php` (direct)
- `level/level.php` (direct)
- `level/parms/parms.php` (direct)

### `$padPrmType`

- `level/parms/parms.php` (direct)
- `level/setup.php` (direct)
- `lib/other.php` (global)
- `tags/go/store.php` (direct)

### `$padPrmTypeSet`

- `inits/level.php` (direct)
- `level/pair.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)

### `$padPrmTypeX`

- `eval/parms/parm.php` (direct)

### `$padPrmValue`

- `handling/handling.php` (direct)
- `level/parms/option.php` (direct)
- `level/parms/parms.php` (direct)
- `level/parms/variable.php` (direct)
- `sequence/inits/find/add.php` (direct)
- `sequence/inits/find/loop.php` (direct)
- `sequence/plays/add.php` (direct)
- `sequence/plays/init.php` (direct)
- `sequence/plays/inits.php` (direct)

### `$padRefID`

- `inits/ids.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padRelations`

- `config/config.php` (direct)
- `lib/table.php` (global)
- `occurrence/table.php` (direct)

### `$padReqID`

- `error/boot.php` ($GLOBALS)
- `info/types/track/_lib.php` ($GLOBALS)
- `inits/cookies.php` (direct)
- `inits/ids.php` (direct)
- `lib/api.php` (global, $GLOBALS)
- `lib/curl.php` ($GLOBALS)
- `lib/other.php` ($GLOBALS)
- `lib/output.php` ($GLOBALS)

### `$padRequestScheme`

- `inits/host.php` (direct)

### `$padReset`

- `options/else.php` (direct)
- `options/error.php` (direct)
- `options/go/reset.php` (direct)
- `options/notOk.php` (direct)
- `options/null.php` (direct)

### `$padRestart`

- `exits/output/file.php` (direct)
- `inits/vars.php` (direct)
- `level/level.php` (direct)
- `lib/api.php` ($GLOBALS)
- `start/enter/restart.php` (direct)

### `$padRestartVars`

- `lib/api.php` ($GLOBALS)
- `start/enter/restart.php` (direct)

### `$padResult`

- `events/type.php` (direct)
- `exits/exits.php` (direct)
- `info/types/trace/end.php` (direct)
- `info/types/trace/level/end.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/xml/level/end.php` (direct)
- `level/end.php` (direct)
- `level/pipes/after.php` (direct)
- `level/setup.php` (direct)
- `lib/callback.php` (global)
- `lib/dump.php` ($GLOBALS)
- `occurrence/end.php` (direct)
- `options/go/options.php` (direct)
- `options/toBool.php` (direct)
- `options/toContent.php` (direct)
- `options/toData.php` (direct)
- `walk/end.php` (direct)

### `$padReturn`

- `tag/fields.php` (direct)
- `tag/keys.php` (direct)

### `$padrev`

- `sequence/types/recaman/function.php` (direct)

### `$padReverse`

- `sequence/types/emirp/loop.php` (direct)

### `$padRowParm`

- `lib/callback.php` (direct)

### `$padRowSave`

- `lib/callback.php` (direct)

### `$padRowSaveStore`

- `lib/callback.php` (direct)

### `$padSanitizeFlags`

- `exits/myTidy.php` (direct)

### `$padSaveLvl`

- `at/groups/saved.php` (global)
- `level/setup.php` (direct)
- `lib/other.php` (global)
- `tags/set.php` (direct)

### `$padSaveOcc`

- `at/groups/saved.php` (global)
- `level/setup.php` (direct)
- `lib/other.php` (global)

### `$padScript`

- `inits/host.php` (direct)
- `lib/api.php` (direct)

### `$padSent`

- `exits/output/console.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `lib/output.php` (global, $GLOBALS)

### `$padSeqData`

- `sequence/exits/extra/chain.php` (direct)
- `sequence/exits/extra/set.php` (direct)

### `$padSeqDefaultRows`

- `config/sequence.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/limits.php` (direct)

### `$padSeqDefaultTries`

- `config/sequence.php` (direct)
- `sequence/inits/direct.php` (global)
- `sequence/inits/limits.php` (direct)

### `$padServerPort`

- `inits/host.php` (direct)

### `$padSesID`

- `info/types/track/_lib.php` ($GLOBALS)
- `inits/cookies.php` (direct)
- `inits/ids.php` (direct)
- `lib/api.php` (global, $GLOBALS)
- `lib/curl.php` ($GLOBALS)
- `lib/other.php` ($GLOBALS)
- `lib/output.php` ($GLOBALS)

### `$padSessionStarted`

- `inits/parms.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padSessionVars`

- `config/config.php` (direct)
- `inits/parms.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padSetConfig`

- `exits/output/file.php` (direct)
- `inits/config.php` (direct)
- `inits/configSet.php` (direct)

### `$padSetLvl`

- `at/any/tag.php` (global)
- `at/groups/variables.php` (global)
- `events/parms.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `level/function.php` (direct)
- `level/parms/variable.php` (direct)
- `level/set.php` (direct)
- `level/setup.php` (direct)
- `start/enter/ajax.php` (direct)
- `start/enter/get.php` (direct)
- `start/enter/redirect.php` (direct)
- `start/start/level.php` (direct)
- `tags/curl.php` (direct)
- `tags/restart.php` (direct)
- `tags/set.php` (direct)
- `tag/variable.php` (global)
- `tag/variables.php` ($GLOBALS)

### `$padSetName`

- `level/parms/variable.php` (direct)
- `tags/set.php` (direct)

### `$padSetOcc`

- `at/any/tag.php` (global)
- `at/groups/variables.php` (global)
- `events/parms.php` (direct)
- `info/types/xml/level/parms.php` (direct)
- `level/parms/variable.php` (direct)
- `level/setup.php` (direct)
- `occurrence/set.php` (direct)

### `$padSetTmp`

- `occurrence/set.php` (direct)

### `$padSetValue`

- `tags/set.php` (direct)

### `$padSortArgs`

- `handling/types/sort.php` (direct)

### `$padSortField`

- `handling/types/sort.php` (direct)

### `$padSortFields`

- `handling/types/sort.php` (direct)

### `$padSortFlags`

- `handling/types/sort.php` (direct)

### `$padSortParms`

- `handling/types/sort.php` (direct)

### `$padSortSort`

- `handling/types/sort.php` (direct)

### `$padSource`

- `level/setup.php` (direct)
- `tags/go/store.php` (direct)

### `$padSplitAfter`

- `level/pipes/end.php` (direct)
- `level/pipes/start.php` (direct)

### `$padSplitBefore`

- `level/pipes/end.php` (direct)
- `level/pipes/start.php` (direct)

### `$padSqlConnect`

- `lib/db.php` (global)
- `lib/dump.php` ($GLOBALS)
- `start/start/resetPad.php` (direct)

### `$padSqlDatabase`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlHost`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlPadConnect`

- `lib/db.php` (global)
- `lib/dump.php` ($GLOBALS)

### `$padSqlPadDatabase`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlPadHost`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlPadPassword`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlPadUser`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlPassword`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padSqlUser`

- `config/config.php` (direct)
- `lib/db.php` (global)

### `$padStart`

- `events/parse.php` (direct)
- `level/level.php` (direct)
- `level/setup.php` (direct)
- `lib/level.php` (global)

### `$padStartBase`

- `info/types/trace/_lib.php` (global)
- `level/end.php` (direct)
- `level/setup.php` (direct)
- `level/start_end/start1.php` (direct)
- `level/start_end/start2.php` (direct)

### `$padStartData`

- `level/setup.php` (direct)
- `level/start_end/start1.php` (direct)
- `level/start_end/start2.php` (direct)

### `$padStartOption`

- `sequence/exits/info/options.php` (direct)
- `sequence/exits/start.php` (direct)
- `sequence/plays/inits.php` (direct)

### `$padStartPage`

- `info/types/track/_lib.php` (global, $GLOBALS)
- `info/types/xml/start.php` (direct)
- `info/types/xref/_lib.php` (global)
- `info/types/xref/start.php` (direct)
- `inits/page.php` (direct)

### `$padStats_boot`

- `info/types/stats/end.php` (direct)

### `$padStats_pad`

- `info/types/stats/end.php` (direct)

### `$padStats_total`

- `info/types/stats/end.php` (direct)

### `$padStats_user`

- `info/types/stats/end.php` (direct)

### `$padStop`

- `cache/hit.php` (direct)
- `cache/inits.php` (direct)
- `exits/exits.php` (direct)
- `exits/output.php` (direct)
- `exits/output/web.php` (direct)
- `info/types/track/_lib.php` ($GLOBALS)
- `inits/vars.php` (direct)
- `lib/other.php` ($GLOBALS)
- `lib/output.php` (global)

### `$padStoreData`

- `events/store.php` (direct)
- `tags/go/store.php` (direct)

### `$padStoreName`

- `options/toBool.php` (direct)
- `options/toContent.php` (direct)
- `options/toData.php` (direct)
- `tags/go/store.php` (direct)

### `$padStoreSource`

- `tags/go/store.php` (direct)

### `$padStrApp`

- `start/end/app.php` (direct)
- `start/end/unsetApp.php` (direct)
- `start/start/app.php` (global)
- `start/start/resetApp.php` (direct)

### `$padStrBld`

- `lib/other.php` ($GLOBALS)
- `start/end/end.php` (direct)
- `start/enter/code.php` (direct)
- `start/enter/page.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/pad.php` (direct)
- `start/start/start.php` (direct)

### `$padStrBox`

- `lib/other.php` ($GLOBALS)
- `start/end/end.php` (direct)
- `start/end.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/function.php` (direct)
- `start/parms.php` (direct)
- `start/start.php` (direct)
- `start/start/start.php` (direct)

### `$padStrCln`

- `lib/other.php` ($GLOBALS)
- `start/end/end.php` (direct)
- `start/end.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/function.php` (direct)
- `start/parms.php` (direct)
- `start/start.php` (direct)
- `start/start/start.php` (direct)

### `$padStrCnt`

- `inits/vars.php` (direct)
- `start/end/app.php` (direct)
- `start/end/dat.php` (direct)
- `start/end/end.php` (direct)
- `start/end/pad.php` (direct)
- `start/end.php` (direct)
- `start/end/stores.php` (direct)
- `start/end/unsetApp.php` (direct)
- `start/end/unsetPad.php` (direct)
- `start/start/app.php` (direct)
- `start/start/dat.php` (direct)
- `start/start/pad.php` (direct)
- `start/start.php` (direct)
- `start/start/resetApp.php` (direct)
- `start/start/start.php` (direct)
- `start/start/stores.php` (direct)

### `$padStrCod`

- `lib/other.php` (direct)
- `start/code.php` (direct)
- `start/end/end.php` (direct)
- `start/enter/code.php` (direct)
- `start/enter/page.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/function.php` ($GLOBALS)
- `start/parms.php` (direct)
- `start/start/start.php` (direct)

### `$padStrFun`

- `lib/other.php` (direct)
- `start/end/end.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/function.php` ($GLOBALS)
- `start/parms.php` (direct)
- `start/start/start.php` (direct)

### `$padStrFunCnt`

- `inits/vars.php` (direct)
- `start/function.php` ($GLOBALS)

### `$padStrFunResult`

- `start/function.php` (direct)

### `$padStrIdx`

- `start/end/dat.php` (direct)
- `start/start/dat.php` (direct)
- `start/start/resetPad.php` (direct)

### `$padStrKey`

- `level/function.php` (direct)
- `start/end/app.php` (direct)
- `start/end/pad.php` (direct)
- `start/end/stores.php` (direct)
- `start/end/unsetApp.php` (direct)
- `start/end/unsetPad.php` (direct)
- `start/function.php` (global)
- `start/start/app.php` (direct)
- `start/start/level.php` (global)
- `start/start/pad.php` (direct)
- `start/start/resetApp.php` (direct)

### `$padStrPag`

- `start/enter/page.php` (direct)

### `$padStrRes`

- `lib/other.php` ($GLOBALS)
- `start/end/end.php` (direct)
- `start/end.php` (direct)
- `start/enter/sandbox.php` (direct)
- `start/parms.php` (direct)
- `start/start.php` (direct)
- `start/start/start.php` (direct)

### `$padStrRet`

- `start/enter/page.php` (direct)

### `$padStrSav`

- `start/end/dat.php` (direct)
- `start/start/dat.php` (global)

### `$padStrStoDat`

- `start/end/stores.php` (direct)
- `start/start/stores.php` (global)

### `$padStrStr`

- `start/end/end.php` (direct)
- `start/start/start.php` (direct)

### `$padStrVal`

- `level/function.php` (direct)
- `start/end/app.php` (direct)
- `start/end/dat.php` (direct)
- `start/end/pad.php` (direct)
- `start/end/stores.php` (direct)
- `start/end/unsetApp.php` (direct)
- `start/end/unsetPad.php` (direct)
- `start/function.php` (direct)
- `start/start/app.php` (direct)
- `start/start/dat.php` (direct)
- `start/start/level.php` (direct)
- `start/start/pad.php` (direct)
- `start/start/resetApp.php` (direct)
- `start/start/resetPad.php` (direct)
- `start/start/stores.php` (direct)

### `$padStrZZZ`

- `start/end/pad.php` (direct)
- `start/end/unsetPad.php` (direct)
- `start/start/pad.php` (global)

### `$padSw`

- `tags/switch.php` (direct)

### `$padSwCnt`

- `tags/switch.php` (direct)

### `$padSwIdx`

- `tags/switch.php` (direct)

### `$padSwNow`

- `tags/switch.php` (direct)

### `$padTable`

- `at/any/tag.php` (global)
- `at/groups/tables.php` ($GLOBALS)
- `level/function.php` (direct)
- `level/setup.php` (direct)
- `lib/field/level.php` (global)
- `lib/field/prefix.php` ($GLOBALS)
- `lib/table.php` (global)
- `occurrence/set.php` (direct)
- `occurrence/table.php` (direct)

### `$padTableName`

- `types/go/table.php` (direct)
- `types/table.php` (direct)

### `$padTables`

- `config/config.php` (direct)
- `lib/table.php` (global)
- `occurrence/table.php` (direct)

### `$padTableTag`

- `level/setup.php` (direct)
- `lib/table.php` (global)
- `occurrence/table.php` (direct)
- `types/go/table.php` (direct)

### `$padTag`

- `at/_lib/lib.php` (global)
- `events/levelStart.php` (direct)
- `events/optional.php` (direct)
- `events/parms.php` (direct)
- `events/tag.php` (direct)
- `info/types/trace/_lib.php` (global)
- `info/types/xml/level/start.php` (direct)
- `level/close.php` (direct)
- `level/go.php` (direct)
- `level/name.php` (direct)
- `level/setup.php` (direct)
- `level/split.php` (direct)
- `lib/data.php` (global)
- `lib/dump.php` ($GLOBALS)
- `lib/field/lib.php` (global)
- `lib/other.php` (global)
- `sequence/inits/direct.php` (global)
- `sequence/inits/set.php` (direct)
- `tags/check.php` (direct)
- `tags/go/data.php` (direct)
- `tags/go/store.php` (direct)
- `tags/record.php` (direct)
- `tags/while.php` (direct)
- `types/app.php` (direct)
- `types/array.php` (direct)
- `types/bool.php` (direct)
- `types/constant.php` (direct)
- `types/data.php` (direct)
- `types/field.php` (direct)
- `types/function.php` (direct)
- `types/level.php` (direct)
- `types/local.php` (direct)
- `types/pad.php` (direct)
- `types/php.php` (direct)
- `types/property.php` (direct)
- `types/script.php` (direct)
- `types/table.php` (direct)

### `$padTagCheck`

- `level/between.php` (direct)
- `level/type.php` (direct)

### `$padTagContent`

- `events/go.php` (direct)
- `level/go.php` (direct)
- `options/go/reset.php` (direct)
- `types/go/tag.php` (direct)

### `$padTagGo`

- `types/app.php` (direct)
- `types/go/tag.php` (direct)
- `types/pad.php` (direct)

### `$padTagOpts`

- `level/between.php` (direct)
- `level/setup.php` (direct)

### `$padTagParmsResult`

- `tag/options.php` (direct)
- `tag/parameters.php` (direct)
- `tag/variables.php` (direct)

### `$padTagResult`

- `level/data.php` (direct)
- `level/flags.php` (direct)
- `level/go.php` (direct)
- `options/go/reset.php` (direct)
- `walk/next.php` (direct)

### `$padTagSeq`

- `events/options.php` (direct)
- `handling/handling.php` (direct)
- `level/setup.php` (direct)
- `sequence/inits/tag.php` (direct)

### `$padTidy`

- `config/config.php` (direct)
- `config/output/console.php` (direct)
- `config/tidy.php` (direct)
- `exits/exits.php` (direct)
- `exits/tidy.php` (direct)

### `$padTidyCcsid`

- `config/tidy.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padTidyConfig`

- `config/tidy.php` (direct)
- `lib/other.php` ($GLOBALS)

### `$padTime`

- `cache/hit.php` (direct)
- `inits/vars.php` (direct)
- `lib/output.php` (global, $GLOBALS)

### `$padTmp`

- `lib/sequence.php` (direct)

### `$padTry`

- `call/_call.php` (direct)
- `call/_once.php` (direct)
- `level/level.php` (direct)
- `level/start.php` (direct)
- `lib/eval/eval.php` (direct)
- `try/try.php` (direct)
- `walk/next.php` (direct)

### `$padTryCallTry`

- `config/try.php` (direct)

### `$padTryCallTryOnce`

- `config/try.php` (direct)

### `$padTryEvalEval`

- `config/try.php` (direct)

### `$padTryException`

- `try/catch/call/_tryOnce.php` (direct)
- `try/catch/call/_try.php` (direct)
- `try/catch/eval/eval.php` (direct)
- `try/catch/level/go.php` (direct)
- `try/catch/level/var.php` (direct)
- `try/try.php` (direct)

### `$padTryLevelGo`

- `config/try.php` (direct)

### `$padTryLevelVar`

- `config/try.php` (direct)

### `$padType`

- `at/_lib/lib.php` (global)
- `events/levelStart.php` (direct)
- `events/optional.php` (direct)
- `events/parms.php` (direct)
- `events/tag.php` (direct)
- `info/types/xml/level/start.php` (direct)
- `level/go.php` (direct)
- `level/setup.php` (direct)
- `level/split.php` (direct)
- `lib/dump.php` ($GLOBALS)
- `lib/field/lib.php` (global)
- `sequence/inits/direct.php` (global)
- `sequence/inits/set.php` (direct)
- `walk/end.php` (direct)

### `$padTypeCheck`

- `inits/level.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)
- `level/type.php` (direct)

### `$padTypeExplode`

- `level/type.php` (direct)

### `$padTypeGiven`

- `inits/level.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)
- `level/type.php` (direct)

### `$padTypePrefix`

- `level/setup.php` (direct)
- `level/type.php` (direct)

### `$padTypeResult`

- `inits/level.php` (direct)
- `level/level.php` (direct)
- `level/setup.php` (direct)
- `level/tag.php` (direct)
- `level/type.php` (direct)

### `$padTypeSeq`

- `level/tag.php` (direct)
- `level/type.php` (direct)
- `lib/type.php` (global, $GLOBALS)

### `$padUri`

- `inits/host.php` (direct)

### `$padUserFunc`

- `types/php.php` (direct)

### `$padV1`

- `handling/types/sort.php` (direct)
- `sequence/exits/extra/chain.php` (direct)

### `$padV2`

- `handling/types/sort.php` (direct)
- `occurrence/set.php` (direct)
- `occurrence/table.php` (direct)
- `sequence/exits/extra/chain.php` (direct)

### `$padVal`

- `events/var_end.php` (direct)
- `level/var.php` (direct)

### `$padVar`

- `inits/parms.php` (direct)

### `$padVarsAfter`

- `lib/callback.php` (direct)

### `$padVarsBefore`

- `lib/callback.php` (direct)

### `$padWalk`

- `info/types/trace/_lib.php` (global)
- `level/end.php` (direct)
- `level/setup.php` (direct)
- `lib/other.php` (global)
- `occurrence/init.php` (direct)
- `options/toData.php` (direct)
- `tags/file.php` (direct)
- `tags/go/store.php` (direct)
- `tags/tidy.php` (direct)
- `tags/trace.php` (direct)
- `tags/while.php` (direct)
- `walk/end.php` (direct)
- `walk/next.php` (direct)

### `$padWalkData`

- `level/setup.php` (direct)
- `occurrence/init.php` (direct)
- `options/toData.php` (direct)

### `$padWebEtag304`

- `config/output/web.php` (direct)
- `exits/output/web.php` (direct)

### `$padWebNoHeaders`

- `config/output/web.php` (direct)
- `lib/output.php` (global)

### `$padWhile`

- `tags/while.php` (direct)

### `$padWords`

- `level/between.php` (direct)
- `level/level.php` (direct)
- `level/tag.php` (direct)

### `$padWordsCheck`

- `level/pair.php` (direct)

### `$padWrk`

- `tag/remaining.php` (direct)

