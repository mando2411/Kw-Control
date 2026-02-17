# PROJECT_BRAIN (Updated)

Last updated: 2026-02-17
Scope of this update: Full project re-scan (web + API + mobile + APK distribution), correction of outdated notes, and dated timeline from Git.

## Owner Working Preference
- Default UI/UX mode remains modern (`ui_mode = modern`) unless explicitly requested otherwise.

## Executive Snapshot
- The system is a Laravel-based election control platform with dashboard flows for voters, contractors, committees, candidates, and reports.
- Contractor experience now exists in two fronts:
  1) Web page: `GET /contract/{token}/profile`
  2) Native Flutter app under `.mobile/ContractorPortalFlutter`
- APK distribution is now first-class in Laravel through `GET /download/contractor-app` with anti-cache headers and dynamic download filename.
- Mobile API is active and routed in `routes/api.php` through `ContractorMobileController` (not empty anymore).

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
