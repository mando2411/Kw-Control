Project Brain - Technical Documentation

System Overview
- Laravel MVC application for managing elections, candidates, voters, contractors, committees, representatives, and reporting.
- Core features live under a secured dashboard route group with role/permission gating and activity logging.
- Uses Spatie Roles/Permissions, Spatie Activity Log, Yajra DataTables, Maatwebsite Excel, DomPDF, Pusher, and Laravel Echo.

Project Structure (high level)
- app/Http/Controllers: Web + dashboard controllers; API controllers exist but routes/api.php is empty.
- app/Models: Eloquent models for elections, candidates, voters, contractors, committees, reports, etc.
- database/migrations: DB schema and many incremental column adds for voters/contractors/committees/users.
- resources/views: Blade views with inline AJAX for dashboard and site UI.
- resources/js: Bootstrap + Echo/Pusher client setup.
- routes: web, auth, admin (dashboard), api (empty).

Request -> Controller -> Model -> DB -> Response (core flows)
- Dashboard list screens
	- /dashboard/* resources -> respective Controller (index) -> Model query -> tables -> Blade view render.
- Voter import
	- POST /dashboard/import/voters -> VoterController@import -> VotersImport/VoterCheck -> voters + related tables -> redirect.
- Attendance update
	- POST /dashboard/voters/{id}/status/update -> VoterController@updateStatus -> Voter + Attendance -> voters + representatives.attendance -> JSON.
- Votes update (AJAX)
	- POST /dashboard/candidates/{id}/votes/set|change -> CandidateController + VoteService -> candidate_committee + candidates.votes -> JSON + Pusher event.
- Contractor assignment
	- POST /ass/{id} -> ContractorController@ass -> contractor_voter + contractor_voter_delete -> redirect.
- Committee status toggle
	- POST /committee/status/{id} -> CommitteeController@status -> committees.status -> JSON + CommitteeUpdate event.
- Reports export
	- POST /dashboard/reports -> ReportController@store -> ReportService -> reports + pdf/xlsx -> download response.

Database Tables (main)
- users: name, email, password, phone, image, theme, last_login_at, last_active_at, creator_id, election_id, soft deletes.
- elections: name, start/end date & time, type.
- candidates: user_id, election_id, max_contractor, max_represent, votes, banner.
- committees: name, type, school_id, election_id, status.
- schools: name, type.
- voters: large profile record (name, identifiers, demographic fields, phone1/phone2, region, status, committee_id, family_id, cod1-3, father, age, user_id, grand, restricted, attend_id, normalized_name, soundex_name, plus initial import fields).
- contractors: parent_id, election_id, creator_id, user_id, name, email, phone, note, trust, status, token.
- representatives: user_id, election_id, committee_id, status, attendance.
- families: name, election_id.
- selections: cod1/2/3, alfkhd, alktaa, albtn, alfraa, street, home, alharaa.
- reports: pdf, creator_id.
- settings: option_key, option_value (json).
- activity_log: subject/causer morphs, description, properties, event, batch_uuid, user_id.
- Pivots: candidate_committee (votes), contractor_voter (percentage), contractor_voter_delete, group_voter, election_voter.

Model Relations (key)
- Election hasMany Candidates, Committees, Users; belongsToMany Voters.
- Candidate belongsTo User & Election; belongsToMany Committees with votes.
- Committee hasMany Voters & Representatives; belongsTo School & Election; belongsToMany Candidates.
- Voter belongsTo Committee, Family, User (creator), Attend User; belongsToMany Contractors, Groups, Elections.
- Contractor belongsTo Election, Creator(User), Parent Contractor; hasMany Children Contractors; belongsToMany Voters; hasMany Groups.
- Representative belongsTo User, Election, Committee.
- Family hasMany Voters; belongsTo Election.

Routes and Endpoints (map)
- Web entry
	- GET / -> dashboard home (requires auth + verified) [routes/web.php].
- Auth
	- Login/register/reset/verify/password routes [routes/auth.php].
- Dashboard resource routes [routes/admin.php]
	- /dashboard/users, roles, elections, contractors, representatives, candidates, voters, committees, schools, families, reports.
- Dashboard utilities / AJAX
	- POST /dashboard/translate (auto-translate)
	- GET /dashboard/toggle-theme
	- POST /dashboard/voters/{id}/status/update
	- POST /dashboard/candidates/{id}/votes/change|set
	- POST /dashboard/rep/{id}/update
	- GET /dashboard/committee/home
	- GET /dashboard/results and /dashboard/all/results
	- GET /dashboard/sorting, /dashboard/attending
	- Import: /dashboard/import/voters, /dashboard/import/contractor/voters
	- Export: /dashboard/voters/export
	- Reports: /dashboard/reports (CRUD) + /report (Excel download)
- Standalone endpoints (non-dashboard prefix)
	- POST /keep-alive
	- POST /committee/status/{id}
	- POST /ass/{id}
	- POST /delete/mad
	- GET /contract/{token}/profile
	- GET /con/{id} (contractor JSON)
	- GET /user/{id} (user JSON)
	- POST /group-e/{id}, GET /group-d/{id}, GET /group/{id}
	- GET /voter/{id} and /voter/{id}/{con_id}
	- GET /percent/{id}/{con_id}/{val}
	- GET /user/contractors/{user_id}, /subcontractors/{main_id}, /voters/{committee_id}, /get_attending_counts/{committee_id}
- API routes
	- routes/api.php is empty; Api\CandidateController and Api\ReportController are present but not routed.

Auth Flow
- Standard Laravel auth routes (register/login/reset/verify).
- Protected dashboard routes use middleware: auth:web + permitted.
- Email verification is enforced on the main dashboard entry.
- UpdateLastActivity middleware updates users.last_active_at; keep-alive JS pings /keep-alive every 120s.

Admin / Vendor / Role Logic
- Roles are managed via Spatie Permission. Route access is enforced by PermittedMiddleware based on route names.
- Administrator can see all contractors, voters, and reports; non-admins are scoped by creator_id or contractor tree.
- Contractor logic supports parent/child contractors and soft-deleted voter assignments.
- Representative has committee assignment and attendance counter.
- Candidates have per-committee vote tallies and total votes; votes are updated via AJAX and broadcast.

Main Features
- Elections and candidate management with per-committee vote tracking.
- Voter management with rich search/filtering, batch import, attendance marking, and export (PDF/Excel/WhatsApp link).
- Contractor management with voter assignment, grouping, and history logs.
- Committee/school views with attendance counts and live status updates.
- Reporting and exports with saved PDFs.
- Activity logging for auditing (Spatie activity_log).

JS/AJAX and Realtime
- Axios/Fetch requests in dashboard views for live updates, CRUD, and exports.
- Pusher client in result views listens to vote and committee status channels.
- Keep-alive pings update user online status.

Important Files
- Routes: [routes/web.php](routes/web.php), [routes/admin.php](routes/admin.php), [routes/auth.php](routes/auth.php), [routes/api.php](routes/api.php)
- Core controllers: [app/Http/Controllers/Dashboard/VoterController.php](app/Http/Controllers/Dashboard/VoterController.php), [app/Http/Controllers/Dashboard/CandidateController.php](app/Http/Controllers/Dashboard/CandidateController.php), [app/Http/Controllers/Dashboard/ContractorController.php](app/Http/Controllers/Dashboard/ContractorController.php), [app/Http/Controllers/Dashboard/StatementController.php](app/Http/Controllers/Dashboard/StatementController.php), [app/Http/Controllers/Dashboard/CommitteeController.php](app/Http/Controllers/Dashboard/CommitteeController.php)
- Services: [app/Services/Attendance.php](app/Services/Attendance.php), [app/Services/VoteService.php](app/Services/VoteService.php), [app/Services/VoterService.php](app/Services/VoterService.php)
- Models: [app/Models/Voter.php](app/Models/Voter.php), [app/Models/Contractor.php](app/Models/Contractor.php), [app/Models/Candidate.php](app/Models/Candidate.php), [app/Models/Committee.php](app/Models/Committee.php)
- Middleware: [app/Http/Middleware/PermittedMiddleware.php](app/Http/Middleware/PermittedMiddleware.php), [app/Http/Middleware/UpdateLastActivity.php](app/Http/Middleware/UpdateLastActivity.php)
- JS entry: [resources/js/app.js](resources/js/app.js)

Known Risks and Tech Debt
- routes/api.php is empty while API controllers exist; API may be unreachable or unused.
- Several state-changing routes use GET (e.g., /percent, /group-d), which is unsafe and may bypass CSRF.
- Voter import can truncate tables and disable FK checks; high risk if misused.
- Many queries fetch large datasets without pagination (e.g., Voter::Filter()->get()) which can cause memory/timeouts.
- Pusher keys are embedded in Blade views; consider environment-driven config.
- Some responses expose raw model data without explicit resource shaping.
- Contractor deletion uses force delete; audit/soft delete policy is unclear.