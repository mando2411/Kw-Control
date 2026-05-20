# PROJECT_BRAIN (Updated)

Last updated: 2026-02-18
Scope of this update: Full project re-scan (web + API + mobile + APK distribution), correction of outdated notes, and dated timeline from Git.

## Owner Working Preference
- Default UI/UX mode remains modern (`ui_mode = modern`) unless explicitly requested otherwise.
- من تاريخ 2026-02-18: أي تطوير/تعديل واجهات جديد يتم على الشكل الحديث فقط (`ui-modern`).
- الشكل القديم (`classic`) يعتبر Legacy وغير مستخدم تشغيليًا، ولا يتم توسيعه بميزات جديدة إلا بطلب صريح.
- من تاريخ 2026-02-18: صفحة التشغيل الأساسية الحالية هي `dashboard/list-management`، وأي تطوير تشغيلي جديد يتمحور حولها.

## Executive Snapshot
- The system is a Laravel-based election control platform with dashboard flows for voters, contractors, committees, candidates, and reports.
- Contractor experience now exists in two fronts:
  1) Web page: `GET /contract/{token}/profile`
  2) Native Flutter app under `.mobile/ContractorPortalFlutter`
- APK distribution is now first-class in Laravel through `GET /download/contractor-app` with anti-cache headers and dynamic download filename.
- Mobile API is active and routed in `routes/api.php` through `ContractorMobileController` (not empty anymore).

---

## Change Tag: real-time (2026-03-08)

Purpose:
- Make `/dashboard/sorting` update numbers instantly (event-driven) when another user changes votes/attendance, without waiting for 3-second polling.

### Old Version (Before `real-time`)
- File: `resources/views/dashboard/sorting/index.blade.php`
- Behavior:
  - Client polled `dashboard.sorting.live-stats` every 3 seconds.
  - UI updates were near-real-time but not instant.

### New Version (After `real-time`)
- File: `app/Events/SortingRealtimeUpdated.php`
  - Added new broadcast event on channel `sorting.{committeeId}` with alias `.sorting.realtime.updated`.
- File: `app/Services/VoteService.php`
  - After successful vote updates, dispatches `SortingRealtimeUpdated(committeeId, 'votes')`.
- File: `app/Http/Controllers/Dashboard/VoterController.php`
  - After successful attendance status change, dispatches `SortingRealtimeUpdated(committeeId, 'attendance')`.
- File: `resources/views/dashboard/sorting/index.blade.php`
  - Removed 3-second polling loop.
  - Subscribes to Echo channel `sorting.{committeeId}`.
  - On `.sorting.realtime.updated`, runs one `live-stats` fetch and updates UI immediately.
  - Added fallback polling every 15 seconds only if Echo is unavailable.

### Hotfix Under Same Tag (`real-time`) - 500 Protection
- Date: 2026-03-08
- Problem:
  - Some environments returned `500` on `/candidates/setVotes` when broadcasting realtime event failed.
- Old behavior:
  - `VoteService` and `VoterController` dispatched realtime event directly.
  - Any broadcast failure could bubble up and break the main action.
- New behavior:
  - Realtime dispatch is wrapped in `try/catch`.
  - Main action (save vote / update attendance) continues successfully.
  - Failure is logged as warning only.
- Files:
  - `app/Services/VoteService.php`
  - `app/Http/Controllers/Dashboard/VoterController.php`

### Hotfix Under Same Tag (`real-time`) - `changeVotes` Transaction Stabilization
- Date: 2026-03-08
- Problem:
  - Endpoint `/candidates/setVotes` still returned generic 500 (`حدث خطا اثناء التصويت`) although vote updates were being applied.
- Old behavior:
  - `CandidateController@changeVotes` used manual `DB::beginTransaction/commit/rollBack` around service call.
  - Potential transaction-state mismatch could surface as 500 after successful update path.
- New behavior:
  - Removed manual transaction controls from `changeVotes`.
  - Added structured `Log::error(...)` in catch with file/line/trace preview for future diagnostics.
  - Response keeps backward-compatible Arabic message and includes underlying exception message in `data`.
- File:
  - `app/Http/Controllers/Dashboard/CandidateController.php`

### Hotfix Under Same Tag (`real-time`) - Committee Cast Crash in VoteService
- Date: 2026-03-08
- Problem:
  - `changeVotes` crashed with: `Object of class App\\Models\\Committee could not be converted to int`.
- Root cause:
  - In `VoteService@updateVotes2`, variable `$committee` was overwritten from id to Committee model, then casted to int when dispatching realtime event.
- Fix:
  - Introduced `$committeeId` as stable integer.
  - Used `$committeeModel` for Eloquent object.
  - Dispatch now uses `$committeeId` only.
  - Added explicit not-found guard for committee model.
- File:
  - `app/Services/VoteService.php`

### Hotfix Under Same Tag (`real-time`) - Low-Latency Frontend Tuning
- Date: 2026-03-08
- Problem:
  - Users reported updates were still delayed noticeably.
- Old behavior:
  - Sorting page listened only to `sorting.{committeeId}` realtime channel.
  - Fallback polling interval was longer.
- New behavior:
  - Sorting page now also listens to existing `votes` channel (`.my-event`) and `committee` channel (`.event`) to trigger immediate refresh.
  - Fallback polling reduced to `2000ms` to keep UI fast if websocket is unstable/unavailable.
  - Added `visibilitychange` immediate fetch on tab focus return.
- File:
  - `resources/views/dashboard/sorting/index.blade.php`

### Hotfix Under Same Tag (`real-time`) - Log Noise Reduction
- Date: 2026-03-08
- Problem:
  - Laravel log received many INFO lines per vote action (request payload + `my-event` broadcast payload).
- Old behavior:
  - `CandidateController@changeVotes` wrote per-request `Log::info(...)`.
  - `VoteService` dispatched `VoteUpdated` (`my-event` on `votes`) in addition to `sorting.realtime.updated`.
  - Sorting frontend listened to both channels.
- New behavior:
  - Removed per-request info logging from `changeVotes`.
  - Removed `VoteUpdated` dispatch from `VoteService` (kept `sorting.realtime.updated`).
  - Removed `votes` channel listener from sorting frontend.
- Files:
  - `app/Http/Controllers/Dashboard/CandidateController.php`
  - `app/Services/VoteService.php`
  - `resources/views/dashboard/sorting/index.blade.php`

### Extension Under Same Tag (`real-time`) - `/all/results` Live Updates
- Date: 2026-03-08
- Goal:
  - Apply same realtime behavior on `https://kw-control.com/all/results` without page reload.
- Old behavior:
  - Page used direct Pusher snippet + `votes/my-event` payload.
  - Only candidate cards were updated directly from event payload.
  - Committee details modal table totals were not refreshed from a live endpoint.
- New behavior:
  - Added election-scoped live endpoint:
    - `GET all/results/live-stats` (`all.results.live-stats`) returning:
      - candidate votes
      - per-committee candidate votes
      - committee totals
      - men/women/grand totals
  - Extended `SortingRealtimeUpdated` event to broadcast on:
    - `sorting.{committeeId}` (existing)
    - `results.{electionId}` (new, when election id available)
  - `/all/results` page now:
    - listens via Echo on `results.{electionId}` for `.sorting.realtime.updated`
    - fetches live stats endpoint and updates cards + modal table + totals instantly
    - has fallback polling every 4 seconds + visibility refresh on tab focus
  - Attendance vote-status updates now emit election-aware realtime event too.
- Files:
  - `app/Events/SortingRealtimeUpdated.php`
  - `app/Http/Controllers/Dashboard/CandidateController.php`
  - `app/Http/Controllers/Dashboard/VoterController.php`
  - `app/Services/VoteService.php`
  - `routes/admin.php`
  - `resources/views/dashboard/resualt/all_index.blade.php`

Rollback notes for this extension only:
1. Remove route `all.results.live-stats` from `routes/admin.php`.
2. Remove `allResultLiveStats()` from `CandidateController` and stop passing `election_id` to `all_index` view.
3. Revert `all_index.blade.php` JS to old Pusher votes listener.
4. Revert `SortingRealtimeUpdated` multi-channel broadcast (remove `results.{electionId}`).
5. Remove election id parameter from realtime event dispatch in `VoteService` and `VoterController`.

### UI Upgrade Under Same Tag (`real-time`) - `/all/results` Pro Max UX
- Date: 2026-03-08
- Scope:
  - Full UI/UX redesign for `all_index` with premium card styling, leaderboard framing (`1st`, `2nd`, ...), and smooth FLIP animation when rank order changes.
  - Modal details table restyled to match competition-grade dashboard look.
  - Live updates keep same realtime logic while applying animated visual transitions for rank and vote changes.
- File:
  - `resources/views/dashboard/resualt/all_index.blade.php`

### Related Support Already In Place
- File: `app/Http/Controllers/Dashboard/CandidateController.php`
  - `sortingLiveStats()` endpoint returns latest votes/totals/status.
- File: `routes/admin.php`
  - Route `dashboard.sorting.live-stats`.
- File: `app/Http/Middleware/PermittedMiddleware.php`
  - Permission mapping `sorting.live-stats -> sorting`.

### Rollback Plan (if user says: "راجع عن تحديث real-time")
1. Delete file:
   - `app/Events/SortingRealtimeUpdated.php`
2. Revert `VoteService` event dispatch lines:
   - remove `use App\Events\SortingRealtimeUpdated;`
   - remove `event(new SortingRealtimeUpdated(...))` in both vote update methods.
3. Revert `VoterController` attendance dispatch:
   - remove `use App\Events\SortingRealtimeUpdated;`
  - remove safe `try/catch` realtime dispatch block after attendance update.
4. Revert `sorting/index.blade.php` JS from Echo listener back to 3-second polling block.
5. Run:
   - `php artisan optimize:clear`

Optional strict rollback (remove hotfix safety):
- In `VoteService`, replace safe helper call with direct event dispatch.
- In `VoterController`, replace safe `try/catch` block with direct event dispatch.
- In `CandidateController@changeVotes`, restore old manual transaction block if needed.

Rollback success criteria:
- Sorting page no longer listens to Echo channel.
- Numbers refresh again via fixed polling interval.

---

## Repository Inventory (verified)

### Core Backend / Web
- `app/` (controllers, models, services, middleware)
- `routes/` (`web.php`, `admin.php`, `auth.php`, `api.php`, ...)
- `resources/views/` (Blade dashboard and contractor pages)
- `database/` (migrations, seeders, factories)

### Mobile + Distribution
- `.mobile/ContractorPortalFlutter/` (real Flutter app source)
- `scripts/build-contractor-apk.ps1` (clean/build/verify/publish pipeline)
- `public/downloads/contractor-portal-latest.apk` (published artifact)

### Operational Notes
- `.gitignore` currently excludes `/mobile/` and `/.mobile/` source folders from tracking.
- `.gitignore` allows tracking only the published artifact:
  - `/public/downloads/*`
  - `!/public/downloads/contractor-portal-latest.apk`

---

## Current Routes and Flows (verified)

### Web Routes (contractor-related)
- `GET /contract/{token}/profile` → contractor profile page (`con-profile`)
- `GET /contract/{token}/support` → contractor support page (`con-support`)
- `GET /download/contractor-app` → APK download (`contractor-app.download`)

### Download Endpoint Behavior
From `routes/web.php`:
- Reads APK from `public/downloads/contractor-portal-latest.apk`
- Builds timestamped file name: `control-app-YYYYmmdd-HHMMSS.apk`
- Forces APK content type
- Sends no-cache headers (`Cache-Control`, `Pragma`, `Expires`) to reduce stale downloads

### Mobile API Routes (active in `routes/api.php`)
Prefix: `/api/contractor/{token}`

#### Bootstrap / Search / Voters
- `GET /bootstrap`
- `GET /voters`
- `GET /voters/{voterId}`
- `PUT /voters/{voterId}/phone`
- `PUT /voters/{voterId}/percentage`
- `POST /voters/{voterId}/attachment`
- `POST /voters/attachment-bulk`

#### Groups
- `GET /groups`
- `POST /groups`
- `GET /groups/{groupId}`
- `PUT /groups/{groupId}`
- `DELETE /groups/{groupId}`
- `POST /groups/{groupId}/voters-action`

---

## Access Control: Roles & Permissions (single consolidated section)

All role-related documentation is centralized in this section only.

### Verified sources
- `database/seeders/RolesAndPermissionsSeeder.php`
- `database/seeders/Permissions/*.php`
- `database/seeders/AdminSeeder.php`
- `app/Http/Middleware/PermittedMiddleware.php`
- `routes/admin.php`
- `app/Http/Controllers/Dashboard/UserController.php`
- `app/Http/Controllers/Dashboard/RepresentativeController.php`
- `app/Http/Controllers/Dashboard/ContractorController.php`
- `app/Http/Controllers/ProfileController.php`

Note:
- This reflects the seeded/default permission model الموجود في الكود.
- If production database was manually modified after seeding, runtime may differ.

### Runtime permission gate logic
- Dashboard group uses middleware: `auth:web` + `permitted`.
- `PermittedMiddleware` maps route names to permission names by:
  - removing `dashboard.`
  - `index` → `list`
  - `store` → `create`
  - `update` → `edit`
  - `destroy` → `delete`
  - `statement.search-modern` → `statement.search`
  - `import-contractor-voters-form`/`import-contractor-voters` → `import.contractor.voters`

Routes excluded from permission check (still require dashboard auth):
- `dashboard.model.auto.translate`
- `dashboard.toggle-theme`
- `dashboard.settings.result`
- `dashboard.store-fake-candidates`
- `dashboard.notifications.index`
- `dashboard.notifications.page`
- `dashboard.notifications.read-all`
- `dashboard.notifications.read`
- `dashboard.statement.export-async`
- `dashboard.statement.export-download`

### Seeded roles list
- `Administrator`
- `مندوب`
- `مرشح`
- `بحث في الكشوف`
- `حذف المضامين`
- `متعهد`
- `مسئول كنترول`
- `الدواوين`
- `وكيل المدرسة`
- `كل صلاحيات اللجان`
- `التحكم بالموقع`

### Role matrix (organized)

#### Full-suite roles (مع اختلافات دقيقة)
- Roles: `Administrator`, `مرشح`, `التحكم بالموقع`
- Shared domains:
  - Elections: `elections.*`, `cards`, `user-change`
  - Committees/Candidate access: `committees.*`, `committee.home`, `committees.multi`, `committees.generate`, `candidates.index`, `candidates.list`, `attending-admin`
  - Contractors: `contractors.*`, `mot-up`, `search-stat-con`, `delete-stat-con`, `con-main`, `results`
  - Representatives: `representatives.*`
  - Voters: `voters.*`, `import.contractor.voters`, `madameen`, `history`, `delete`
  - Statement/statistics: `statement`, `statement.search`, `statistics.search`, `statistics`, `statement.query`, `statement.show`
  - Settings: `settings.show`, `settings.edit`
  - Schools/Families: `schools.*`, `families.*`
  - Mandob ops: `attending`, `rep-home`, `voters.change-status`, `candidates.changeVotes`, `candidates.setVotes`, `sorting`, `school-rep`, `rep.change`
- Differences:
  - `Administrator` فقط لديه `users.*` و `roles.*`
  - `Administrator` و `مرشح` لديهما `reports.*`
  - `التحكم بالموقع` لا يملك `reports.*` في seeders الحالية

#### Medium-scope role
- `مسئول كنترول`
  - Contractors: `contractors.*`, `mot-up`, `search-stat-con`, `delete-stat-con`, `con-main`, `results`
  - Voters: `voters.*`, `import.contractor.voters`, `madameen`, `history`, `delete`

#### Field operation roles
- `مندوب`, `وكيل المدرسة`
  - `attending`, `rep-home`, `voters.change-status`, `candidates.changeVotes`, `candidates.setVotes`, `sorting`, `school-rep`, `rep.change`

#### Contractor role
- `متعهد`
  - `contractors.create`, `contractors.list`, `statement`, `statement.show`, `madameen`

#### Micro-roles
- `بحث في الكشوف` → `search-stat-con` فقط
- `حذف المضامين` → `delete-stat-con` فقط

#### Roles without seeded permissions
- `الدواوين`
- `كل صلاحيات اللجان`

### Role assignment paths (actual code)
- Seeder bootstrap:
  - `AdminSeeder` assigns `Administrator` to `admin@perfect.com`.
- User management:
  - `Dashboard/UserController@store` assigns submitted roles.
  - `Dashboard/UserController@update` syncs submitted roles.
- Representative creation:
  - `Dashboard/RepresentativeController@store` auto-assigns role `مندوب`.
- Contractor creation (`dashboard.con-main`):
  - `Dashboard/ContractorController@contractor` assigns submitted roles + always assigns `متعهد`.
- Profile edit role update:
  - `ProfileController@update` allows `syncRoles` only if current user has role `Administrator`.

### Route → permission mapping examples
- Users:
  - `dashboard.users.index` → `users.list`
  - `dashboard.users.store` → `users.create`
  - `dashboard.users.update` → `users.edit`
  - `dashboard.users.destroy` → `users.delete`
- Roles:
  - `dashboard.roles.*` → `roles.*`
- Contractors:
  - `dashboard.contractors.*` → `contractors.*`
  - `dashboard.con-main` → `con-main`
  - `dashboard.mot-up` → `mot-up`
- Voters:
  - `dashboard.voters.*` → `voters.*`
  - `dashboard.import-contractor-voters` / `dashboard.import-contractor-voters-form` → `import.contractor.voters`
  - `dashboard.madameen` → `madameen`
- Statement:
  - `dashboard.statement` → `statement`
  - `dashboard.statement.search-modern` → `statement.search`
- Settings:
  - `dashboard.settings.update` → `settings.edit`

### Candidate (المرشح) deep-dive — based on actual code

#### 1) من الذي يضيف المرشح؟

- الطريقة الرسمية (CRUD):
  - عبر `dashboard.candidates.create` ثم `dashboard.candidates.store` داخل مجموعة `dashboard` المحمية بـ `auth:web` + `permitted`.
  - هذا المسار يحتاج صلاحية `candidates.create` (عبر تحويل route name بواسطة `PermittedMiddleware`).

- الطريقة السريعة (Fake Candidate):
  - زر `Add Fake Candidate` في شاشة المرشحين يرسل Ajax إلى `POST /dashboard/store/fake/candidates` (route: `dashboard.store-fake-candidates`).
  - هذه route موجودة ضمن dashboard group لكنها مستثناة صراحةً من فحص الصلاحيات داخل `PermittedMiddleware`.
  - النتيجة: الفحص هنا يعتمد أساسًا على `auth` (مع بقاء CSRF)، وليس على permission محدد مثل `candidates.create`.

#### 2) الحقول المطلوبة لإضافة المرشح (فعليًا في الكود)

##### أ) الإضافة الرسمية `CandidateController@store`

- التحقق يتم عبر `CandidateRequest` + `UserRequest`:
  - من `UserRequest`:
    - `name` مطلوب.
    - `phone` مطلوب (إلا إذا `fake=1`، وهذا غير مستخدم في هذا المسار).
    - `email` اختياري (ولو فارغ عند الإنشاء يتم توليد بريد تلقائي).
    - `roles` اختيارية.
  - من `CandidateRequest`:
    - `max_contractor` مطلوب، رقم صحيح >= 0.
    - `max_represent` مطلوب، رقم صحيح >= 0.
    - `election_id` غير إلزامي في الـvalidation (nullable).
    - `banner` اختياري.

- الحقول التي يعتمد عليها نموذج الإنشاء في الواجهة:
  - بيانات شخصية: الاسم/البريد/كلمة المرور/الصورة/الهاتف/الأدوار.
  - بيانات المرشح: `election_id` + `max_contractor` + `max_represent` + `banner`.

##### ب) الإضافة السريعة `CandidateController@storeFakeCandidate`

- من الواجهة (Modal) يتم فرض إدخال:
  - `name`
  - `election_id`
  - `image`
  - `fake=1`

- في الباك إند:
  - لا يوجد Validator مخصص داخل الدالة نفسها لـ`image`/`election_id`.
  - الدالة تعتمد مباشرة على وجود `image` (باستدعاء `uploadImage`)؛ غياب الصورة يؤدي إلى Exception ثم رسالة خطأ.
  - يتم إنشاء Candidate بقيم افتراضية:
    - `max_contractor = 0`
    - `max_represent = 0`

#### 3) طرق الإضافة: هل هناك تكرار؟

- نعم، توجد طريقتان واضحتان لإضافة المرشح:
  1) رسمية كاملة (Form + validation أوسع + redirect إلى edit).
  2) سريعة/وهمية (Modal + Ajax + defaults + assignRole ثابت).

- ملاحظة تقنية مهمة في الطريقة السريعة:
  - الدور يُسند باستخدام رقم ثابت `3` على أنه دور `مرشح`.
  - هذا يعتمد على ثبات IDs في قاعدة البيانات، وهو أضعف من الإسناد بالاسم.

#### 4) ماذا يستطيع المرشح أن يفعل؟ (وفق الصلاحيات المزروعة)

- دور `مرشح` يمتلك صلاحيات واسعة في seeders، منها:
  - إدارة: الانتخابات، اللجان، المتعهدين، المناديب، الناخبين.
  - عمليات: الفرز/التصويت (`sorting`, `candidates.changeVotes`, `candidates.setVotes`, `attending`, `rep-home`).
  - تقارير وإعدادات: `reports.*` + `settings.*`.
  - عمليات الكشوف: `statement*`, `statistics*`.

- عمليًا داخل واجهة النظام:
  - يمكنه إدارة المرشحين ضمن resource routes (`dashboard.candidates.*`).
  - يمكنه التعامل مع نتائج وفرز الأصوات عبر `results` و`sorting` وتحديثات التصويت.

#### 5) علاقة المرشح بوجود حملة انتخابية داخل المشروع

لا يوجد كيان باسم `Campaign` صريح في قاعدة البيانات، لكن منطق الحملة متحقق عبر علاقات المرشح التالية:

- المرشح مرتبط بانتخاب (`candidates.election_id`) ومرتبط بالمستخدم (`user_id`).
- كل مرشح يرتبط باللجان عبر pivot `candidate_committee` مع أصوات لكل لجنة.
- إجمالي أصوات المرشح مشتق من مجموع أصواته في اللجان (داخل `VoteService`).

- علاقة المرشح بالمتعهدين (الهيكل الميداني للحملة):
  - في استيراد الناخبين للمتعهدين، المستخدم يختار `candidate` أولاً.
  - بعدها النظام يجلب المتعهدين الرئيسيين حيث `creator_id = candidate_user_id`.
  - هذا يجعل المرشح هو المالك التشغيلي لشجرة المتعهدين، وهي نواة الحملة في التطبيق.

- علاقة المرشح بعرض النتائج العامة:
  - إعدادات `result_control` تعتمد على اختيار مرشح (by `candidate.user_id`) لتفعيل سياق عرض النتائج العامة.
  - يوجد تنبيه في واجهة الإعدادات بضرورة إضافة المرشح المطلوب (مثل "مرشح الفرز العام") قبل التفعيل.

#### 6) ملاحظات تكرار/تداخل مرتبطة بالمرشح (مؤكدة من الكود)

- تكرار في مسارات تحديث التصويت:
  - `dashboard.candidates/{id}/votes/set` → `setVotes`
  - `POST /candidates/setVotes` (خارج prefix dashboard) → `changeVotes`
  - كلاهما يؤديان لتحديث أصوات المرشح بواجهتين مختلفتين.

- تداخل في ربط المرشح باللجان عند الإنشاء:
  - `Candidate` model عند `created` يقوم بربط المرشح بكل اللجان تلقائيًا.
  - `store` الرسمي بعدها يعمل `sync` مع لجان انتخاب المرشح فقط.
  - `storeFakeCandidate` لا يعمل هذا `sync`، فيبقى الربط الافتراضي العام من model event.

- `max_contractor` و`max_represent` مطلوبة في الإدخال الرسمي، لكنها لا تظهر كقيود إنفاذ واضحة في المسارات التي تم فحصها (تُستخدم كبيانات أكثر من كونها limit مُطبق)..

### List Leader Candidate (مرشح رئيس قائمة) — latest implemented behavior (2026-02-18)

هذا القسم يوثق كل التعديلات الأخيرة الخاصة بـ "مرشح رئيس قائمة" حتى تكون مرجعًا واحدًا واضحًا.

#### 1) Data model and migration changes

- تم توسيع نموذج المرشح لدعم حالة رئيس القائمة عبر حقول جديدة في جدول `candidates`:
  - `candidate_type` (`candidate` أو `list_leader`)
  - `list_candidates_count` (سقف عدد مرشحي القائمة)
  - `list_name` (اسم القائمة)
  - `list_logo` (شعار القائمة)
  - `is_actual_list_candidate` (تمييز المرشح الفعلي من المرشح التنظيمي)
  - `list_leader_candidate_id` (ربط مرشحي القائمة برئيس القائمة)
- تم تحديث `Candidate` model لدعم العلاقات:
  - `listLeader` (belongsTo)
  - `listMembers` (hasMany)
  - helper: `isListLeader()`.

#### 2) Create/Edit UI and request validation

- في إنشاء/تعديل المرشح تمت إضافة حقول نوع المرشح والقائمة.
- عند اختيار `candidate_type = list_leader` تظهر الحقول الإضافية المطلوبة (`list_candidates_count`, `list_name`, `list_logo`).
- `CandidateRequest` يفرض هذه الحقول عند حالة رئيس القائمة (`required_if`).
- عند دخول رئيس القائمة لإضافة مرشح جديد:
  - يتم قفل `election_id` على نفس حملة رئيس القائمة.
  - يتم الإرسال عبر hidden field لنفس `election_id`.

#### 3) Business rules for list leader and list members

- رئيس القائمة يستطيع إضافة مرشحين مثل صلاحيات المرشح/الإدمن ولكن داخل قائمة واحدة فقط (قائمته).
- أي مرشح يُنشئه رئيس القائمة يرتبط تلقائيًا بـ `list_leader_candidate_id` الخاص به.
- منع تجاوز السقف:
  - يتم حساب العدد الحالي ومقارنته مع `list_candidates_count`.
  - عند الوصول للسقف يتم منع الإضافة.

#### 4) Counting policy (final refined rule)

- القاعدة النهائية المطبقة:
  - المرشح "الفعلي" فقط (`is_actual_list_candidate = true`) يُحتسب ضمن سعة القائمة.
  - المرشح "التنظيمي" لا يُحتسب.
  - رئيس القائمة نفسه يُحتسب فقط إذا كان فعليًا.
- تمت مواءمة عرض السعة المتبقية في واجهة الإنشاء مع نفس قاعدة العد في السيرفر.

#### 5) Visibility and access scope for list leader

- في `CandidateController` و `CandidateDataTable` تم ضبط الرؤية بحيث رئيس القائمة يرى نطاق قائمته المرتبط به فقط.
- تم دعم fallback في `PermittedMiddleware` للسماح لرئيس القائمة بالوصول لمسارات `candidates.*` المطلوبة للتشغيل.
- زر/إجراء "Fake Candidate" أُخفي في سياق رئيس القائمة لتفادي تدفقات غير متوافقة مع قواعد السعة والقائمة.

#### 6) Navbar/Sidebar behavior for list leader

- تم استبدال سلوك زر السايدبار التقليدي لرئيس القائمة بزر مخصص (Quick Menu) في النافبار.
- القائمة السريعة تتضمن:
  - `المرشحين`
  - `إضافة مرشح`
- الإدمن يحتفظ بسلوك السايدبار المعتاد دون تغيير.

#### 7) Home dashboard card: "إدارة القائمة"

- تم إضافة كارت جديد في الرئيسية داخل قسم `المتعهدين والمضامين`:
  - العنوان: `إدارة القائمة`
  - يظهر اسم القائمة أسفل العنوان.
  - يفتح صفحة المرشحين (`dashboard.candidates.index`).
- شرط الظهور:
  - يظهر فقط لمستخدم رئيس قائمة (role أو وجود Candidate من نوع `list_leader`).

#### 8) Root cause discovered for “card not showing” and final fix

- السبب الجذري: صفحة الرئيسية كانت تدخل مسار `pendingJoinRequest` (واجهة طلب انضمام المتعهد) في أعلى `home/index.blade.php`، وهذا يمنع رندر بقية كروت الرئيسية بالكامل.
- الإصلاح النهائي:
  - في route الرئيسية (`routes/web.php`) تم تجاوز جلب `pendingJoinRequest` لمستخدم رئيس القائمة.
  - في Blade تم منع عرض واجهة pending إذا المستخدم رئيس قائمة.
  - تم مسح كاش Laravel (`php artisan optimize:clear`) لتفعيل التغييرات مباشرة.

#### 9) Files touched in this list-leader stream

- `database/migrations/2026_02_17_220000_add_list_leader_fields_to_candidates_table.php`
- `app/Models/Candidate.php`
- `app/Http/Requests/Dashboard/CandidateRequest.php`
- `app/Http/Controllers/Dashboard/CandidateController.php`
- `app/DataTables/CandidateDataTable.php`
- `app/Http/Middleware/PermittedMiddleware.php`
- `resources/views/dashboard/candidates/create.blade.php`
- `resources/views/dashboard/candidates/edit.blade.php`
- `resources/views/dashboard/candidates/index.blade.php`
- `resources/views/layouts/dashboard/navbar.blade.php`
- `resources/views/layouts/dashboard/sidebar.blade.php`
- `resources/views/layouts/dashboard/app.blade.php`
- `resources/views/dashboard/home/index.blade.php`
- `routes/web.php`

#### 10) Operational note (important)

- عند أي تعديل في شروط العرض الخاصة بالهيدر/الهوم/الصلاحيات، يجب تنفيذ:
  - `php artisan optimize:clear`
- السبب: route/view/config cache قد يجعل التعديل يبدو "غير فوري" رغم أنه صحيح في الكود.

### Mermaid map (Role → Permission → Route → Module)

```mermaid
flowchart TD
    subgraph R[Roles]
      R1[Administrator]
      R2[مرشح]
      R3[التحكم بالموقع]
      R4[مسئول كنترول]
      R5[مندوب / وكيل المدرسة]
      R6[متعهد]
      R7[بحث في الكشوف]
      R8[حذف المضامين]
    end

    subgraph P[Permission Domains]
      P1[users.* / roles.*]
      P2[elections + committees + candidate access]
      P3[contractors.* core domain]
      P4[voters.* + import.contractor.voters + madameen]
      P5[statement.* + statistics]
      P6[representative and attendance operations]
      P7[settings / families / schools (and reports لبعض الأدوار)]
      P8[search-stat-con]
      P9[delete-stat-con]
    end

    subgraph RT[Dashboard Routes]
      T1[dashboard.users.*, dashboard.roles.*]
      T2[dashboard.elections.*, dashboard.committees.*, dashboard.candidates.*]
      T3[dashboard.contractors.*, dashboard.con-main, dashboard.results]
      T4[dashboard.voters.*, dashboard.import-contractor-voters, dashboard.madameen]
      T5[dashboard.statement*, dashboard.statistics]
      T6[dashboard.representatives*, dashboard.rep-home, dashboard.attending, dashboard.rep.change]
      T7[dashboard.settings.*, dashboard.families.*, dashboard.schools.*, dashboard.reports.*]
    end

    subgraph M[Business Modules]
      M1[User & Role Management]
      M2[Election/Candidate/Committee]
      M3[Contractor Management]
      M4[Voter Operations]
      M5[Statement & Analytics]
      M6[Representative Operations]
      M7[System Configuration]
    end

    R1 --> P1
    R1 --> P2
    R1 --> P3
    R1 --> P4
    R1 --> P5
    R1 --> P6
    R1 --> P7

    R2 --> P2
    R2 --> P3
    R2 --> P4
    R2 --> P5
    R2 --> P6
    R2 --> P7

    R3 --> P2
    R3 --> P3
    R3 --> P4
    R3 --> P5
    R3 --> P6
    R3 --> P7

    R4 --> P3
    R4 --> P4

    R5 --> P6
    R6 --> P3
    R6 --> P5
    R6 --> P4
    R7 --> P8
    R8 --> P9

    P1 --> T1 --> M1
    P2 --> T2 --> M2
    P3 --> T3 --> M3
    P4 --> T4 --> M4
    P5 --> T5 --> M5
    P6 --> T6 --> M6
    P7 --> T7 --> M7
```

---

## Contractor Mobile Backend (ContractorMobileController)

Key capabilities currently implemented:
- Token resolution (`resolveContractor`)
- Bootstrap payload (contractor identity, election dates/times, links, families, groups)
- Voters search with:
  - `name`, `family`
  - sibling search (`sibling`, `sibling_exclude_id`)
  - scope filtering (`all`, `attached`, `available`)
  - grouped exclusion option (`exclude_grouped`)
  - IDs-only mode for bulk selection (`ids_only=1`)
- Pagination metadata in mobile responses
- Single attach/detach + bulk attach/detach actions
- Voter details + phone + percentage update
- Group CRUD + group voters actions

Important behavior notes:
- `scope=all` returns election voters regardless of contractor attachment
- `scope=attached` / `available` are attachment-aware based on `contractor_voter`
- Detach operation also syncs to soft-delete relation via `contractor_voter_delete`

---

## Flutter App (real native app)

Location: `.mobile/ContractorPortalFlutter`

### Runtime and dependencies
- Version in `pubspec.yaml`: `1.0.2+3`
- Main dependencies:
  - `dio`
  - `shared_preferences`
  - `url_launcher`

### Android identity
- App label in Android manifest: `كنترول`

### App architecture in `lib/main.dart`
- `SplashGate` startup routing
- Onboarding flow
- Setup flow (base URL + token extraction/normalization + bootstrap validation)
- `HomeShell` with tabs (`SearchTab`, `ListsTab`)
- SearchTab supports:
  - search by name/number
  - family filtering
  - scope chips (`all/attached/available`)
  - bulk select + bulk actions
  - single-row attach/detach actions
  - pagination / all-mode loading
- Hero card + countdown with election start/end awareness
- Footer marketing/support section

### Build traceability
- Build signature constant used in app/build pipeline:
  - `CONTROL_BUILD_SIG_20260216_1605`

---

## APK Build/Publish Pipeline

Script: `scripts/build-contractor-apk.ps1`

Pipeline steps:
1) `flutter pub get`
2) `flutter clean`
3) `flutter pub get`
4) `flutter build apk --release`
5) Open APK as zip and verify signature exists in `lib/arm64-v8a/libapp.so`
6) Copy release APK to `public/downloads/contractor-portal-latest.apk`

Guarantee:
- Publish is blocked if expected signature is missing.

---

## Dated Change Log (from Git history)

### 2026-02-16 (major implementation day)

#### Distribution and anti-cache
- `fc4ae07` (15:09) updated `routes/web.php`
- `0be4c0d` (14:53) updated `routes/web.php` + APK artifact
- Outcome: download endpoint stabilized around APK-only delivery + cache-control behavior.

#### Mobile API evolution
- `77a30de` (13:35) changed `app/Http/Controllers/Api/ContractorMobileController.php` + `routes/api.php`
- `d9a25bd` (14:24) changed `ContractorMobileController.php` + `routes/api.php` + APK
- `2be3cc7` (14:44) changed `ContractorMobileController.php`
- Outcome: bootstrap/voters/groups and bulk flows matured.

#### Web contractor profile updates
- `c616246` (07:37), `d3a4dc4` (07:40), `646be7f` (07:52), `fb90c33` (14:40)
- File changed repeatedly: `resources/views/dashboard/contractors/profile.blade.php`
- Outcome: UX/layout/interaction updates on web contractor page.

#### Scope and contractor logic alignment
- `2917438` (14:32)
- Files changed:
  - `app/Http/Controllers/Api/ContractorMobileController.php`
  - `app/Http/Controllers/Dashboard/ContractorController.php`
- Outcome: alignment of search/attachment behavior between web and mobile flows.

#### Build hardening
- `ca66b43` (16:05)
- Files changed:
  - `scripts/build-contractor-apk.ps1`
  - `public/downloads/contractor-portal-latest.apk`
- Outcome: strict signature verification before publishing APK.

#### Artifact publish updates
- APK-only updates were committed multiple times:
  - `4590368`, `5f8f635`, `e3386ec`, `5125895`
- Outcome: repeated rebuild/republish cycles of `public/downloads/contractor-portal-latest.apk`.

#### Source tracking strategy change
- `4932cf4` (12:50)
- Updated `.gitignore` and removed tracked Android source tree under `mobile/ContractorPortalAndroid/*`
- Outcome: repository keeps published APK artifact while mobile source tracking strategy changed.

---

## Corrections vs Older PROJECT_BRAIN Content

The old file contained stale statements. Correct state is:
- `routes/api.php` is NOT empty.
- Contractor mobile API is active and used.
- APK download route is active and controlled in `routes/web.php`.

---

## Harmony Audit (2026-02-18)

نتيجة التدقيق العام: المميزات الأساسية متوافقة، وتم إغلاق نقاط التعارض المؤثرة التالية:

1) **Role bootstrap gap**
- المشكلة: دور `مرشح رئيس قائمة` لم يكن موجودًا في seeders، ما قد يسبب اختلافًا بين بيئات جديدة وقديمة.
- المعالجة: إضافة الدور إلى `RolesAndPermissionsSeeder`.

2) **Permission fallback mismatch**
- المشكلة: `PermittedMiddleware` كان يعتبر المستخدم رئيس قائمة فقط إذا امتلك role صريحًا، رغم أن أجزاء أخرى من النظام تعتمد (role أو `candidate_type=list_leader`).
- الأثر: احتمال ظهور الكارت/الزر مع بقاء 403 في بعض المسارات.
- المعالجة: توحيد المنطق داخل middleware ليعتمد (role أو candidate_type).

3) **Brittle role assignment in fake candidate flow**
- المشكلة: `storeFakeCandidate` كان يستخدم `assignRole(3)` (اعتماد على ID ثابت).
- المعالجة: الاستبدال إلى `assignRole('مرشح')`.

### Current support recommendations (still valid)

- بعد أي تعديل في الصلاحيات/العرض: تنفيذ `php artisan optimize:clear`.
- عند بيئة جديدة بعد نشر seeders: تشغيل seeding/permission cache reset للتأكد من وجود الدور الجديد.
- لتفادي اختلافات البيانات القديمة: يفضل عمل job/command one-time لمزامنة أي Candidate بنوع `list_leader` مع role `مرشح رئيس قائمة` إذا كان مفقودًا.

### Demo full data seeder (new)

- تمت إضافة Seeder شامل لاختبار كل السيناريوهات في الملف:
  - `database/seeders/DemoFullElectionSeeder.php`
- ما الذي يُنشئه:
  - رؤساء قوائم + أعضاء قوائم + مرشحين مستقلين.
  - متعهدين أساسيين وفرعيين مرتبطين بكل رئيس قائمة.
  - 1000 ناخب تجريبي بحالات حضور/عدم حضور مع ربطهم باللجان والعائلات والانتخابات والمتعهدين.
- أمر التشغيل:
  - `php artisan db:seed --class 'Database\Seeders\DemoFullElectionSeeder' --force`
- ملاحظة تشغيلية:
  - الـSeeder ينظف الداتا التجريبية القديمة (بنمط `demo.*@kw.local` والأسماء التجريبية) قبل إعادة الإنشاء حتى يكون قابلًا لإعادة التشغيل بأمان.

### Demo accounts map (ready for login/testing)

- كلمة المرور الافتراضية لكل الحسابات التجريبية:
  - `12345678`

- حسابات مؤكدة وثابتة (يمكن تسجيل الدخول بها مباشرة):
  - **مندوب تجريبي:** `demo.attender@kw.local`
  - **رؤساء القوائم:**
    - `demo.listleader1@kw.local`
    - `demo.listleader2@kw.local`
    - `demo.listleader3@kw.local`
  - **أعضاء القوائم (أمثلة):**
    - `demo.listmember1.1@kw.local`
    - `demo.listmember2.1@kw.local`
    - `demo.listmember3.1@kw.local`
  - **مرشحون مستقلون (أمثلة):**
    - `demo.independent1@kw.local`
    - `demo.independent2@kw.local`

- حسابات المتعهدين (تُنشأ تلقائيًا بنمط dynamic):
  - أساسي: `demo.maincontractor...@kw.local`
  - فرعي: `demo.subcontractor...@kw.local`
  - لاستخراج القائمة الفعلية سريعًا:
    - `php artisan tinker --execute="echo \App\Models\User::where('email','like','demo.maincontractor%@kw.local')->pluck('email')->join(PHP_EOL);"`
    - `php artisan tinker --execute="echo \App\Models\User::where('email','like','demo.subcontractor%@kw.local')->pluck('email')->join(PHP_EOL);"`

### Quick QA scenarios (minimal)

- **رئيس قائمة** (`demo.listleader1@kw.local`):
  - تحقق من ظهور كارت `إدارة القائمة` في الكلاسيك والمودرن.
  - افتح إنشاء مرشح: تحقق من قفل `election_id` على نفس الحملة.
  - جرّب إضافة أعضاء حتى حد القائمة ثم تحقق من المنع عند بلوغ السعة.


- **عضو قائمة** (`demo.listmember1.1@kw.local`):
  - تحقق من الظهور ضمن قائمة رئيسه فقط وعدم امتلاك صلاحيات رئيس القائمة.

- **مرشح مستقل** (`demo.independent1@kw.local`):
  - تحقق من عدم ظهور خصائص/كارت `إدارة القائمة` الخاصة برئيس القائمة.

- **متعهد أساسي/فرعي** (من قائمة الايميلات المستخرجة):
  - تحقق من الوصول لواجهات المتعهد وتبعيات الناخبين المرتبطين.

- **الناخبون**:
  - الداتا تشمل حالات حضور/عدم حضور؛ للتحقق السريع:
    - `php artisan tinker --execute="echo 'attended=' . \App\Models\Voter::where('name','like','ناخب تجريبي %')->where('status',1)->count() . PHP_EOL . 'not_attended=' . \App\Models\Voter::where('name','like','ناخب تجريبي %')->where('status',0)->count();"`
- Real Flutter app exists under `.mobile/ContractorPortalFlutter`.
- APK build script now includes verification, not just build/copy.

---

## Current Risks / Technical Debt (re-validated)

- Mobile source is currently under an ignored path (`/.mobile/`), which can reduce traceability of source-level changes in Git.
- APK artifact is tracked, but binary-only commits can make code review difficult.
- Some legacy routes in admin/web context still use GET for operations that ideally should be non-GET.
- Contractor/profile Blade remains large and includes substantial inline logic; maintainability risk remains.

---

## Immediate Next Documentation Step (recommended)

When the next deploy is done, append a short release entry including:
- Date/time
- APK hash (SHA256)
- Build signature constant
- Main UI/API deltas
- Route or controller files touched

---

## Committees Deep Review (2026-03-07)

مراجعة كود قسم اللجان تمت من المصدر مباشرة (Routes + Controller + Request + Model + Views + DataTable + Services + Events + Migrations + Permissions) مع ربط المسارات ببعضها.

### Scope الملفات التي تمت مراجعتها

- `app/Http/Controllers/Dashboard/CommitteeController.php`
- `app/Http/Controllers/Dashboard/GeneralController.php`
- `app/Http/Requests/Dashboard/CommitteeRequest.php`
- `app/Models/Committee.php`
- `app/Models/Candidate.php`
- `app/Models/Voter.php`
- `app/Scopes/ElectionScope.php`
- `app/DataTables/CommitteeDataTable.php`
- `app/Services/Query/CommitteeGenerator.php`
- `app/Events/CommitteeUpdate.php`
- `app/Observers/CommitteeObserver.php`
- `resources/views/dashboard/committees/create.blade.php`
- `resources/views/dashboard/committees/edit.blade.php`
- `resources/views/dashboard/committees/index.blade.php`
- `resources/views/dashboard/committees/multi.blade.php`
- `resources/views/dashboard/committees/committee.blade.php`
- `resources/views/dashboard/committees/action.blade.php`
- `resources/views/layouts/dashboard/sidebar.blade.php`
- `resources/views/components/dashboard/partials/table-card-header.blade.php`
- `resources/views/dashboard/sorting/index.blade.php`
- `resources/views/dashboard/resualt/index.blade.php`
- `routes/admin.php`
- `database/migrations/2024_06_10_104328_create_committees_table.php`
- `database/migrations/2024_06_24_105049_add_column_to_voters_table.php`
- `database/migrations/2024_10_01_140504_add_column_to_commitees_table.php`
- `database/migrations/2025_01_04_044919_add_column_to_committees_table.php`
- `database/migrations/2024_06_10_110606_create_committee_candidates_table.php`
- `database/seeders/Permissions/CommitteePermissionSeeder.php`

### نموذج البيانات والعلاقات

- جدول `committees`:
  - أساسي: `id`, `name`, `type`, `timestamps`
  - لاحق: `school_id` (nullable FK), `election_id` (nullable FK), `status` (boolean default true)
- Pivot: `candidate_committee`:
  - `candidate_id`, `committee_id`, `votes` (default 0)
- العلاقات:
  - Committee -> voters (`hasMany`)
  - Committee -> representatives (`hasMany`)
  - Committee -> school (`belongsTo`)
  - Committee -> election (`belongsTo`)
  - Committee <-> candidates (`belongsToMany` with `pivot.votes`)

### تدفق الإضافة (Create) مترابطًا

#### 1) إضافة لجنة واحدة

- المسار: `dashboard.committees.create` ثم `dashboard.committees.store`
- الفورم في `create.blade.php` يرسل: `name`, `type`, `election_id`
- التحقق في `CommitteeRequest`:
  - `name` مطلوب
  - `type` مطلوب
  - `election_id` مطلوب
- الحفظ في `CommitteeController@store` عبر `Committee::create($request->getSanitized())`
- بعد الإنشاء مباشرة، `Committee::boot()` (حدث `created`) ينفذ:
  - جلب مرشحي نفس الانتخاب `candidate.election_id = committee.election_id`
  - ربط المرشحين المطابقين فقط مع اللجنة في pivot
  - محاولة تعيين المدرسة تلقائيًا حسب `type`

#### 2) إضافة متعددة (Bulk)

- المسار: `dashboard.committees.generate` (فورم) -> `dashboard.committees.multi` (تنفيذ)
- التحقق داخل `CommitteeController@multi`:
  - `men` و `women` أعداد صحيحة >= 1
  - `election_id` مطلوب
- التنفيذ عبر `CommitteeGenerator::createRecords()`:
  - ينشئ لجان الرجال `Type::MEN->value`
  - ينشئ لجان النساء `Type::WOMEN->value`
  - كل إنشاء يمر بنفس side effects (attach candidates + assign school)

### تدفق التعديل (Edit/Update)

- المسار: `dashboard.committees.edit` -> `dashboard.committees.update`
- `edit.blade.php` يسمح تعديل `name`, `type`, `election_id`
- `CommitteeController@update` ينفذ `update($sanitized)` فقط
- مهم: منطق `created` في الموديل لا يُعاد تشغيله عند update
  - تغيير `type` لا يعيد ضبط `school_id` تلقائيًا
  - لا توجد إعادة ربط/تنظيف تلقائي لعلاقات المرشحين عند التعديل

### تدفق المراجعة (Review) كاملًا

#### 1) صفحة القائمة الأساسية

- المسار: `dashboard.committees.index`
- العرض عبر `CommitteeDataTable`
- الأعمدة: `id`, `name`, `type`, `created_at`, `action`
- `action.blade.php` يقدم أزرار التعديل والحذف
- إنشاء زر الإضافة يأتي من `table-card-header.blade.php` ويعتمد على صلاحية `committees.create`

#### 2) صفحة المراجعة التشغيلية (Committee Home)

- المسار: `dashboard.committee.home`
- الكنترولر يجهز مدارس مع لجان: `School::with('committees')->get()`
- العرض في `committee.blade.php`:
  - فلتر المدرسة (all/مدرسة محددة)
  - إحصاءات الحضور/الباقي
  - جدول تفصيلي لكل لجنة
  - أسماء المندوبين لكل لجنة
  - روابط تقارير `dashboard.statement.show`

#### 3) قفل/فتح اللجنة (status)

- المسار: `committee.status` (`POST committee/status/{id}`)
- `CommitteeController@status` يحدّث `status` ثم يطلق `CommitteeUpdate`
- الحدث `CommitteeUpdate` يعمل broadcast على قناة `committee` باسم `event`
- الاستهلاك في الواجهة موجود في `dashboard/resualt/index.blade.php` (Pusher channel `committee`)
- نموذج التبديل موجود في `dashboard/sorting/index.blade.php`

### الربط مع الأقسام الأخرى

- اللجان <-> المرشحين:
  - ربط ثنائي الاتجاه من `Committee` و `Candidate`
  - من تاريخ `2026-03-08`: الربط التلقائي عند `created` أصبح **مقيدًا بنفس `election_id`** في الموديلين، وهذا يجعل السلوك متوافقًا مع منطق `list_leader` وعزل القوائم انتخابيًا.
- اللجان <-> الناخبين:
  - `committee_id` على الناخب
  - إحصاءات الحضور تعتمد على `status`
  - جلب ناخبين لجنة عبر `GeneralController@fetchVotersForCommittee`
- اللجان <-> المدارس:
  - تعيين آلي للمدرسة عند الإنشاء فقط
- اللجان <-> الانتخابات:
  - علاقة `belongsTo` + Global Scope (`ElectionScope`)
- اللجان <-> النتائج المباشرة:
  - تغيير حالة اللجنة يبث حدثًا حيًا يغيّر الأيقونات في شاشة النتائج

### صلاحيات قسم اللجان (المطبق فعليًا)

- Seeder يضيف: `committees.list`, `committees.create`, `committees.edit`, `committees.delete`, `committees.restore`, `committee.home`, `committees.multi`, `committees.generate`, `committees.update-status`
- تم توحيد `committee.status` مع permission mapping إلى `committees.update-status` داخل `PermittedMiddleware`.
- صلاحية `committees.multi` موجودة كاسم permission رغم أن route الفعلي مرتبط بالاسم `dashboard.committees.multi` ويُستخدم من شاشة الإنشاء المتعدد

### Compatibility Alignment مع قسم List Leader (2026-03-08)

- تم توحيد منطق ربط `candidate_committee` ليكون داخل نفس الانتخاب فقط:
  - `Committee::created` يربط مرشحي نفس `committee.election_id` فقط.
  - `Candidate::created` يربط لجان نفس `candidate.election_id` فقط.
- تم إزالة debug log الخاص بجلب المرشحين من مسار إنشاء اللجنة.
- تم تحصين `CommitteeController@status`:
  - فحص auth.
  - فحص صلاحية (`committees.update-status` مع fallback `committees.edit` للتوافق الخلفي).
  - معالجة حالة `committee` غير موجودة بـ `404` بدل null crash.
- route `committee.status` أصبح يتطلب `auth:web`.

### مراجعة المخاطر والملاحظات الحرجة

1. `edit.blade.php` يستخدم input نصي لـ`type` بدل select مضبوط (يسمح بقيم غير قياسية بسهولة).
2. `CommitteeRequest` لا يتحقق من enum ثابت للنوع (`in:ذكور,اناث`) ولا من وجود `election_id` كـ FK (`exists:elections,id`).
3. `CommitteeObserver` موجود لكنه فارغ وغير مُسجّل في `AppServiceProvider` (dead code حاليًا).
4. `committee.blade.php` ينفذ عدادات كثيرة داخل Blade loops على علاقات (`$com->voters`) مما يسبب N+1 واستهلاك أعلى عند كبر البيانات.
5. عدادات أعلى صفحة المراجعة (`App\Models\Voter::where(...)`) غير مقيدة بالمدرسة المختارة ولا بالانتخاب الحالي في نفس الشاشة، فتظهر أرقام إجمالية عامة.
6. `ElectionScope` يستخدم `where('election_id', auth()->user()->election_id)` داخل علاقة `election`، بينما جدول `elections` الطبيعي يعتمد `id`، وهذا موضع يحتاج تدقيق فوري.
7. في `GeneralController@fetchVotersForCommittee` لا يوجد check إذا كانت اللجنة غير موجودة قبل استخدام خصائصها.

### ملاحظات تشغيلية سريعة

- أي تعديل في صلاحيات اللجان أو ربطها بالـsidebar يحتاج بعدها `php artisan optimize:clear`.
- قبل الإنتاج يفضل توحيد تمثيل النوع (ذكور/اناث مقابل ذكر/انثى) ضمن enum وvalidation وواجهات الإدخال.
- يوصى بنقل حسابات الإحصاءات الثقيلة من Blade إلى Queries محسنة (`withCount`/aggregates) لتقليل زمن الصفحة.

---

## Schools <-> Committees Logic Review (2026-03-08)

مراجعة منطق المدارس وعلاقته باللجان تمت من المصدر المباشر (Models/Controllers/Requests/Migrations/Views/Seeders).

### Applied Fixes (completed)

1. تم إضافة `election_id` لجدول المدارس عبر migration:
   - `database/migrations/2026_03_08_120000_add_election_id_to_schools_table.php`
   - يتضمن backfill ذكي عندما تكون المدرسة مرتبطة بلجان من انتخاب واحد فقط.
2. تم توسيع نموذج `School` ليدعم:
   - `election_id` في `$fillable`
   - علاقة `election()`.
3. تم إصلاح منطق ربط المدرسة باللجنة داخل `Committee::created`:
   - الاختيار الآن حسب `type + election_id` مع fallback لمدارس `election_id = null`.
4. تم إضافة إعادة ضبط تلقائي لـ`school_id` عند تعديل `Committee.type` أو `Committee.election_id` (`updating` hook).
5. تم إصلاح `SchoolRequest`:
   - `election_id` مطلوب.
   - `type` مضبوط على enum (`ذكور/اناث`).
   - منع تكرار `type` داخل نفس `election_id` (unique rule) لتفادي الربط الغامض.
6. تم إصلاح `SchoolController` لتمرير `elections` في صفحات create/edit.
7. تم تحديث واجهات المدارس:
   - `schools/create` يحتوي الآن اختيار `type` + `election_id`.
   - `schools/edit` يحتوي الآن `name` + `type` + `election_id` ومتوافق مع الـvalidation.
8. تم إصلاح `SchoolPermissionSeeder`:
   - إصلاح `updateOrCreate` الخاطئ.
   - استخدام قيم enum الصحيحة (`Type::...->value`).
9. تم تحسين `SchoolDataTable`:
   - إضافة أعمدة `type` و`election`.
   - فلترة حسب انتخاب المستخدم (مع fallback للمدارس العامة) لغير الـAdministrator.
10. تم تحسين صفحة `committee.home`:
    - إلغاء العدادات العامة غير scoped.
    - اعتماد summary مربوط بالمدرسة/المدارس المختارة.
    - تصحيح إجمالي الحضور لكل مدرسة/لجنة ليعتمد على بياناتها الفعلية.
11. تم جعل `RepresentativeController@home` election-aware عند تحميل المدارس واللجان.

### Residual Notes (still open)

1. `SchoolObserver` لا يزال فارغًا وغير مستخدم (يمكن حذفه أو استغلاله لقواعد domain).
2. ما زال اختيار المدرسة في نموذج اللجنة تلقائيًا (based on type+election) وليس اختيارًا يدويًا صريحًا من الفورم؛ إذا مطلوب دقة تشغيلية أعلى، يفضل إضافة `school_id` مباشرة في create/edit اللجنة.
شسس