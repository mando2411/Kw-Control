Data Import and Schema Guide

Scope
- Covers table schemas derived from database migrations and model hints.
- Maps column sources: Excel import, forms, and system-generated fields.
- Documents all Excel/CSV import flows and how to prepare sheets.

Legend (column source)
- EXCEL: Maatwebsite Excel imports.
- FORM: Admin/dashboard forms (request validation in app/Http/Requests/Dashboard).
- SYSTEM: Auto-generated, computed, defaults, or internal processes.
- UNKNOWN: Present in migration/model but source not found in code.

Compact Column Source Matrix
| Table | EXCEL columns | FORM columns | SYSTEM columns |
| --- | --- | --- | --- |
| elections | none | name, start_date, end_date, start_time, end_time, type | id, timestamps |
| candidates | none | max_contractor, max_represent, banner | id, user_id, election_id, votes, timestamps |
| committees | none | name, type, election_id | id, school_id, status, timestamps |
| schools | none | name, type | id, timestamps |
| families | name (alaaaylh) | name | id, election_id, timestamps |
| voters | alasm, mrgaa, albtn, alfraa, btn_almoyhy, tarykh_alandmam, alrkm_ala_yl_llaanoan, alktaah, alrkm_almdny, alkyd_ao_alsndok, alfkhd, alnoaa, alhatf1, alhatf2, almntk, cod1, cod2, cod3, hal_alkyd | name, almrgaa, albtn, alfraa, yearOfBirth, btn_almoyhy, tary_kh_alandmam, alrkm_ala_yl_llaanoan, alktaa, alrkm_almd_yn, alsndok, phone, region, status, committee_id, family_id, alfkhd, age, user_id | id, status, committee_id, attend_id, normalized_name, soundex_name, timestamps |
| selections | cod1, cod2, cod3, alfkhd, alktaa, albtn, alfraa, street, home, elharaa | none | id, timestamps |
| contractors | none | parent_id, name, email, phone, note, trust, status | id, election_id, creator_id, user_id, token, timestamps |
| representatives | none | user_id, election_id, committee_id | id, status, attendance, timestamps |
| reports | none | none | id, pdf, creator_id, timestamps |
| contractor_voter | alrkm_almdn, asm_almtaahd (indirect) | contractor_id, voter_id | id, percentage, timestamps |
| election_voter | alrkm_almdny (indirect) | none | voter_id, election_id, timestamps |
| settings | none | option_key, option_value | id, timestamps |
| activity_log, auth tables | none | none | all columns |

Per-Table Column Catalog

users
- id (bigint, pk) - SYSTEM
- name (string) - FORM (UserRequest)
- email (string, unique) - FORM (UserRequest)
- email_verified_at (timestamp, nullable) - SYSTEM (email verification)
- password (string) - FORM (UserRequest; hashed)
- phone (string, nullable) - FORM (UserRequest)
- image (string, nullable) - FORM (UserRequest)
- theme (string, default light) - SYSTEM
- remember_token - SYSTEM (auth)
- last_login_at (timestamp, nullable) - SYSTEM
- last_active_at (timestamp, nullable) - SYSTEM (UpdateLastActivity middleware / keep-alive)
- creator_id (fk users, nullable) - SYSTEM (UserRequest sets from auth)
- election_id (fk elections, nullable) - SYSTEM/FORM (UserRequest sets from auth or election_id)
- deleted_at (soft delete) - SYSTEM
- created_at, updated_at - SYSTEM

password_resets
- email (string, pk) - SYSTEM
- token (string) - SYSTEM
- created_at (timestamp, nullable) - SYSTEM

failed_jobs
- id (bigint, pk) - SYSTEM
- uuid (string, unique) - SYSTEM
- connection (text) - SYSTEM
- queue (text) - SYSTEM
- payload (longText) - SYSTEM
- exception (longText) - SYSTEM
- failed_at (timestamp, default current) - SYSTEM

personal_access_tokens
- id (bigint, pk) - SYSTEM
- tokenable_type, tokenable_id (morphs) - SYSTEM
- name (string) - SYSTEM
- token (string, unique) - SYSTEM
- abilities (text, nullable) - SYSTEM
- last_used_at (timestamp, nullable) - SYSTEM
- expires_at (timestamp, nullable) - SYSTEM
- created_at, updated_at - SYSTEM

notifications
- id (uuid, pk) - SYSTEM
- type (string) - SYSTEM
- notifiable_type, notifiable_id (morphs) - SYSTEM
- data (text) - SYSTEM
- read_at (timestamp, nullable) - SYSTEM
- created_at, updated_at - SYSTEM

settings
- id (bigint, pk) - SYSTEM
- option_key (string, unique) - FORM (SettingsRequest)
- option_value (json, nullable) - FORM (SettingsRequest)
- created_at, updated_at - SYSTEM

elections
- id (bigint, pk) - SYSTEM
- name (string) - FORM (ElectionRequest)
- start_date (date) - FORM (ElectionRequest)
- end_date (date) - FORM (ElectionRequest)
- start_time (time) - FORM (ElectionRequest)
- end_time (time) - FORM (ElectionRequest)
- type (string) - FORM (ElectionRequest)
- created_at, updated_at - SYSTEM

candidates
- id (bigint, pk) - SYSTEM
- user_id (fk users, nullable) - SYSTEM/FORM (CandidateController creates User then sets user_id)
- election_id (fk elections, nullable) - SYSTEM/FORM (CandidateController)
- max_contractor (float) - FORM (CandidateRequest)
- max_represent (float) - FORM (CandidateRequest)
- votes (float, default 0) - SYSTEM (VoteService updates)
- banner (string, nullable) - FORM (CandidateRequest)
- created_at, updated_at - SYSTEM

committees
- id (bigint, pk) - SYSTEM
- name (string) - FORM (CommitteeRequest)
- type (string) - FORM (CommitteeRequest)
- school_id (fk schools, nullable) - SYSTEM (Committee boot assigns by type)
- election_id (fk elections, nullable) - FORM (CommitteeRequest)
- status (boolean, default true) - SYSTEM/FORM (CommitteeController status toggle)
- created_at, updated_at - SYSTEM

schools
- id (bigint, pk) - SYSTEM
- name (string) - FORM (SchoolRequest)
- type (string) - FORM (SchoolRequest)
- created_at, updated_at - SYSTEM

families
- id (bigint, pk) - SYSTEM
- name (string) - EXCEL (VotersImport uses alaaaylh) / FORM (FamilyRequest)
- election_id (fk elections, nullable) - EXCEL (VotersImport) / SYSTEM (auth context)
- created_at, updated_at - SYSTEM

voters
- id (bigint, pk) - SYSTEM
- name (string, nullable) - EXCEL (alasm) / FORM (VoterRequest) [SEARCH]
- almrgaa (string, nullable) - EXCEL (mrgaa) / FORM (VoterRequest)
- albtn (string, nullable) - EXCEL (albtn) / FORM (VoterRequest) [FILTER]
- alfraa (string, nullable) - EXCEL (alfraa) / FORM (VoterRequest) [FILTER]
- yearOfBirth (date, nullable) - EXCEL (derived from alrkm_almdny) / FORM (VoterRequest)
- btn_almoyhy (string, nullable) - EXCEL (btn_almoyhy) / FORM (VoterRequest)
- tary_kh_alandmam (string, nullable) - EXCEL (tarykh_alandmam) / FORM (VoterRequest)
- alrkm_ala_yl_llaanoan (string, nullable) - EXCEL (alrkm_ala_yl_llaanoan) / FORM (VoterRequest)
- alfkhd (string, nullable) - EXCEL (alfkhd) / FORM (VoterRequest) [FILTER]
- alktaa (string, nullable) - EXCEL (alktaah) / FORM (VoterRequest) [FILTER]
- alrkm_almd_yn (string, nullable) - EXCEL (alrkm_almdny) / FORM (VoterRequest) [SEARCH]
- alsndok (string, nullable) - EXCEL (alkyd_ao_alsndok) / FORM (VoterRequest) [SEARCH]
- phone1 (string, nullable) - EXCEL (alhatf1) / FORM (VoterRequest uses phone) [FILTER]
- phone2 (string, nullable) - EXCEL (alhatf2) / FORM (UI edits)
- job (string, nullable) - FORM (not in requests, but in schema)
- type (string, nullable) - EXCEL (alnoaa) / FORM (VoterRequest) [FILTER][REPORT]
- region (string, nullable) - EXCEL (almntk) / FORM (VoterRequest)
- status (string, default 0) - SYSTEM (attendance updates, VoterCheck import) [FILTER][REPORT]
- committee_id (fk committees, nullable) - SYSTEM (attendance updates) [REPORT]
- family_id (fk families, nullable) - EXCEL (VotersImport) / FORM (VoterRequest) [FILTER][REPORT]
- user_id (fk users, nullable) - FORM (VoterRequest) / SYSTEM
- cod1 (string, nullable) - EXCEL (cod1) [FILTER]
- cod2 (string, nullable) - EXCEL (cod2) [FILTER]
- cod3 (string, nullable) - EXCEL (cod3) [FILTER]
- father (string, nullable) - EXCEL (derived from alasm) [FILTER][SEARCH]
- age (integer, default 0) - EXCEL (derived from alrkm_almdny) [FILTER]
- grand (string, nullable) - EXCEL (derived from alasm)
- restricted (string, default non-English string) - EXCEL (hal_alkyd) / SYSTEM default [FILTER]
- attend_id (fk users, nullable) - SYSTEM (attendance updates)
- normalized_name (string, nullable) - SYSTEM/UNKNOWN [SEARCH]
- soundex_name (string, nullable) - SYSTEM/UNKNOWN
- created_at, updated_at - SYSTEM

contractors
- id (bigint, pk) - SYSTEM
- parent_id (fk contractors, nullable) - FORM (ContractorRequest)
- election_id (fk elections, nullable) - SYSTEM (ContractorRequest sets from auth)
- creator_id (fk users, nullable) - SYSTEM (ContractorRequest sets from auth)
- user_id (fk users, nullable) - SYSTEM/FORM (ContractorController creates user then assigns)
- note (string, nullable) - FORM
- trust (float, nullable) - FORM
- status (string, nullable) - FORM
- token (string, nullable) - SYSTEM (ContractorRequest generates)
- name (string) - FORM
- email (string, nullable) - FORM
- phone (string, nullable) - FORM
- created_at, updated_at - SYSTEM

representatives
- id (bigint, pk) - SYSTEM
- user_id (fk users, nullable) - SYSTEM/FORM (RepresentativeController creates user then sets)
- election_id (fk elections, nullable) - SYSTEM/FORM
- committee_id (fk committees, nullable) - FORM (RepresentativeRequest)
- status (boolean, default false) - SYSTEM (set during password update)
- attendance (float, default 0) - SYSTEM (Attendance service)
- created_at, updated_at - SYSTEM

reports
- id (bigint, pk) - SYSTEM
- pdf (string) - SYSTEM (ReportService output path)
- creator_id (fk users, nullable) - SYSTEM
- created_at, updated_at - SYSTEM

selections
- id (bigint, pk) - SYSTEM
- cod1 (string, nullable) - EXCEL (cod1)
- cod2 (string, nullable) - EXCEL (cod2)
- cod3 (string, nullable) - EXCEL (cod3)
- alfkhd (string, nullable) - EXCEL (alfkhd)
- alktaa (string, nullable) - EXCEL (alktaah)
- albtn (string, nullable) - EXCEL (albtn)
- alfraa (string, nullable) - EXCEL (alfraa)
- street (string, nullable) - EXCEL (street)
- home (string, nullable) - EXCEL (home)
- alharaa (string, nullable) - EXCEL (elharaa)
- created_at, updated_at - SYSTEM

groups
- id (bigint, pk) - SYSTEM
- contractor_id (fk contractors, nullable) - FORM
- name (string, nullable) - FORM
- type (string, nullable) - FORM
- created_at, updated_at - SYSTEM

group_voter (pivot)
- id (bigint, pk) - SYSTEM
- voter_id (fk voters) - SYSTEM/FORM (group assignment)
- group_id (fk groups) - SYSTEM/FORM (group assignment)
- created_at, updated_at - SYSTEM

contractor_voter (pivot)
- id (bigint, pk) - SYSTEM
- contractor_id (fk contractors) - FORM / EXCEL (ContractorVotersImport)
- voter_id (fk voters) - FORM / EXCEL (ContractorVotersImport)
- percentage (string, default 0) - SYSTEM/FORM
- created_at, updated_at - SYSTEM

contractor_voter_delete (pivot)
- id (bigint, pk) - SYSTEM
- contractor_id (fk contractors) - SYSTEM (soft delete logic)
- voter_id (fk voters) - SYSTEM (soft delete logic)
- created_at, updated_at - SYSTEM

candidate_committee (pivot)
- id (bigint, pk) - SYSTEM
- candidate_id (fk candidates) - SYSTEM (candidate creation)
- committee_id (fk committees) - SYSTEM (candidate creation)
- votes (float, default 0) - SYSTEM (VoteService)
- created_at, updated_at - SYSTEM

election_voter (pivot)
- voter_id (fk voters) - EXCEL (VotersImport attaches)
- election_id (fk elections) - EXCEL (VotersImport attaches)
- created_at, updated_at - SYSTEM

activity_log
- id (bigint, pk) - SYSTEM
- log_name (string, nullable) - SYSTEM
- description (text) - SYSTEM
- subject_type, subject_id (morphs) - SYSTEM
- causer_type, causer_id (morphs) - SYSTEM
- properties (json, nullable) - SYSTEM
- user_id (fk users, nullable) - SYSTEM
- event (string, nullable) - SYSTEM
- batch_uuid (uuid, nullable) - SYSTEM
- created_at, updated_at - SYSTEM

permissions (Spatie)
- id (bigint, pk) - SYSTEM
- name (string) - SYSTEM/FORM (role/permission UI)
- guard_name (string) - SYSTEM
- created_at, updated_at - SYSTEM

roles (Spatie)
- id (bigint, pk) - SYSTEM
- name (string) - FORM (RoleRequest)
- guard_name (string) - SYSTEM
- created_at, updated_at - SYSTEM

model_has_permissions (Spatie)
- permission_id (fk permissions) - SYSTEM
- model_type (string) - SYSTEM
- model_id (unsigned bigint) - SYSTEM
- team_id (optional, if enabled) - SYSTEM

model_has_roles (Spatie)
- role_id (fk roles) - SYSTEM
- model_type (string) - SYSTEM
- model_id (unsigned bigint) - SYSTEM
- team_id (optional, if enabled) - SYSTEM

role_has_permissions (Spatie)
- permission_id (fk permissions) - SYSTEM
- role_id (fk roles) - SYSTEM

Tables With Models But No Migrations Found
- appointments (model exists; no migration in database/migrations).
- seos (model uses table seos; no migration found).
- clients (ClientRequest exists; no migration found).

Excel/CSV Import Features

1) Voters master import
- Upload route: POST /dashboard/import/voters (routes/admin.php)
- Controller: VoterController@import
- Import classes:
  - App\Imports\VotersImport (normal import)
  - App\Imports\VoterCheck (status-only update)
- Validation rules (controller):
  - import: required, mimes xlsx/xls/csv
  - check: required (used to switch import type)
  - election: required (election ID)
- Required columns (VotersImport):
  - alasm (used to skip empty rows)
- Optional columns (VotersImport):
  - mrgaa, albtn, alfraa, btn_almoyhy, tarykh_alandmam, alrkm_ala_yl_llaanoan
  - alktaah, alrkm_almdny, alkyd_ao_alsndok, alfkhd, alnoaa, alhatf1, almntk, alhatf2
  - cod1, cod2, cod3, hal_alkyd, alaaaylh, street, home, elharaa
- Effects:
  - Creates/updates voters and families and selections.
  - Derives father/grand from alasm and dateOfBirth/age from alrkm_almdny.
  - Attaches voter to election via election_voter.
- Required columns (VoterCheck):
  - mrgaa_aldakhly (used to match voters by alrkm_almd_yn)
- VoterCheck behavior:
  - Sets status=1, committee_id=1, attend_id=23 for matched voters with status=0.

2) Contractor voters import
- Upload route: POST /dashboard/import/contractor/voters (routes/admin.php)
- Controller: VoterController@importVotersFotContractor
- Import class: App\Imports\ContractorVotersImport
- Validation rules (request):
  - import: required, mimes xlsx/xls/csv
  - sub_contractor: optional (if not provided, sheet must include contractor name)
- Required columns:
  - alrkm_almdn (voter civil id)
  - asm_almtaahd (contractor name) when sub_contractor is not provided in the form
- Behavior:
  - Looks up voters by alrkm_almd_yn.
  - Verifies voter belongs to contractor election (election_voter).
  - Inserts into contractor_voter with percentage=0.
  - Restores from contractor_voter_delete if present.

Excel Sheet Preparation Guide

General
- File formats: .xlsx, .xls, .csv
- The first row must be a heading row (WithHeadingRow is used).
- All headings must match exactly (case-insensitive, underscore-based names below).
- Empty or misspelled headings will cause silent skips or partial imports.

A) Voters master import (VotersImport)
Required headers
- alasm

Recommended headers (mapped to voters)
- mrgaa
- albtn
- alfraa
- btn_almoyhy
- tarykh_alandmam
- alrkm_ala_yl_llaanoan
- alktaah
- alrkm_almdny
- alkyd_ao_alsndok
- alfkhd
- alnoaa
- alhatf1
- alhatf2
- almntk
- cod1
- cod2
- cod3
- hal_alkyd
- alaaaylh

Recommended headers (mapped to selections)
- street
- home
- elharaa

Notes
- alrkm_almdny is used to derive date of birth and age.
- alaaaylh creates/links a family under the selected election.
- cod1/cod2/cod3 and address fields also populate selections.

Expanded Guide: Voters Master Import

Required vs optional fields
- Required (minimum):
  - alasm (name). Rows with empty alasm are skipped.
- Strongly recommended (for matching/reporting):
  - alrkm_almdny (civil ID), albtn, alfraa, alktaah, alfkhd, alnoaa.
- Optional (fills secondary info):
  - mrgaa, btn_almoyhy, tarykh_alandmam, alrkm_ala_yl_llaanoan, alkyd_ao_alsndok, alhatf1, alhatf2, almntk, cod1, cod2, cod3, hal_alkyd, alaaaylh, street, home, elharaa.

Detailed column explanations
- alasm: Full voter name. Used to derive father and grand names by splitting on spaces.
- alrkm_almdny: Civil ID. Used to derive yearOfBirth and age; also key for matching in other flows.
- albtn, alfraa, alktaah, alfkhd: Geographic/administrative fields used in filters and reports.
- alnoaa: Gender/type value (used in attendance and statistics).
- alhatf1, alhatf2: Phone numbers (phone1/phone2 in DB).
- cod1, cod2, cod3: Coded selectors used in filters and selections.
- alaaaylh: Family name; creates/links families per election.
- street, home, elharaa: Address fields that populate selections.
- hal_alkyd: Restriction status. Defaults to a non-empty value when missing.

Data normalization rules (import logic)
- yearOfBirth and age are computed from alrkm_almdny:
  - First digit determines century (2 => 19xx, 3 => 20xx).
  - If invalid, dateOfBirth/age remain null and a warning is logged.
- father and grand are derived from alasm (2nd and 3rd tokens).
- family is created/linked using alaaaylh and the selected election.
- selections are created using cod1/2/3 plus address fields.

Duplicate prevention logic
- VotersImport uses firstOrCreate with the full voter data array.
- If any non-key fields differ, a new row can be created even if the civil ID matches.
- Election linkage is guarded: the voter is attached to the election only if not already linked.

Step-by-step validation before upload
1) Confirm alasm is present for all rows.
2) Confirm alrkm_almdny is present for as many rows as possible and stored as Text.
3) Ensure headers match exactly (no extra spaces or alternate spellings).
4) Check gender/type values are consistent (e.g., "ذكر" vs "اناث").
5) Remove duplicate rows that differ only in optional fields.
6) Ensure alaaaylh values are consistent to avoid fragmenting families.

Post-import verification checklist
- Spot-check newly created voters for name, civil ID, and phone numbers.
- Confirm families were created and linked to the election.
- Verify selections contain the expected cod/address values.
- Run a basic filter (by family, type, or cod1) and confirm results.
- Confirm voters appear under the selected election (election_voter).

B) Attendance status import (VoterCheck)
Required headers
- mrgaa_aldakhly

Notes
- This flow does not use the full voter sheet; it only updates status.
- Sets committee_id to 1 and attend_id to 23 for matching voters.

C) Contractor voters import (ContractorVotersImport)
Required headers
- alrkm_almdn
- asm_almtaahd (only if sub_contractor is not selected in the form)

Notes
- alrkm_almdn must match voters.alrkm_almd_yn.
- If sub_contractor is chosen in the form, asm_almtaahd can be omitted.

Ready-to-Use Import Templates

Voters master import (CSV or XLSX)
```csv
alasm,mrgaa,albtn,alfraa,btn_almoyhy,tarykh_alandmam,alrkm_ala_yl_llaanoan,alktaah,alrkm_almdny,alkyd_ao_alsndok,alfkhd,alnoaa,alhatf1,alhatf2,almntk,cod1,cod2,cod3,hal_alkyd,alaaaylh,street,home,elharaa
Example Name,MRG-001,ALBTN-01,ALFRAA-01,BTN-01,2024-01-01,ADDR-01,SECTOR-01,299010112345,BOX-12,ALFKHD-01,ذكر,96512345678,96587654321,REG-01,C1,C2,C3,غير مقيد,Family Name,Street 1,Home 1,Block A
```

Attendance status import (VoterCheck)
```csv
mrgaa_aldakhly
299010112345
```

Contractor voters import (ContractorVotersImport)
```csv
alrkm_almdn,asm_almtaahd
299010112345,Contractor Name
```

Templates & Tools
- Templates (CSV):
  - [docs/templates/voters_import_template.csv](docs/templates/voters_import_template.csv)
  - [docs/templates/votercheck_import_template.csv](docs/templates/votercheck_import_template.csv)
  - [docs/templates/contractor_voters_import_template.csv](docs/templates/contractor_voters_import_template.csv)
- XLSX generator script:
  - [docs/templates/generate_xlsx_templates.py](docs/templates/generate_xlsx_templates.py)

Usage examples
- Create XLSX templates locally:
```bash
pip install openpyxl
python docs/templates/generate_xlsx_templates.py
```
- Open a CSV template in Excel and save as .xlsx if you prefer manual conversion.

Quickstart: Using Import Templates
1) Download the template
  - Pick the right template from docs/templates and copy it to your working folder.
2) Fill required columns
  - Keep the header row unchanged.
  - Fill only the required columns first, then add optional columns as needed.
3) Avoid common mistakes
  - Do not rename headers.
  - Format civil ID columns as Text to avoid losing leading digits.
  - Use exact contractor names when required.
4) Upload in the dashboard
  - Go to the import screen, choose the file, select required options (election, check type, contractor), then submit.
5) Verify results
  - Confirm new records appear in the relevant list views.
  - Spot-check a few records for correct names, IDs, and linked entities.

Fields Used in Filters, Search, and Reports
- Voter filters (Pipeline): name, albtn, phone1, family_id, father, status, alfkhd, alktaa, alfraa, cod1, cod2, cod3, type, alrkm_almd_yn, alsndok, age, restricted.
- Voter search (General/Contractor): name, normalized_name, family_id, father, alsndok.
- Attendance/statistics reports: committee_id, family_id, type, status.
- Export reports: family_id, voter ids, school_id; exported columns are selected from voters in the UI.

Practical Checklist Before Upload
- Ensure the heading row matches the exact names listed above.
- Use UTF-8 CSV (if using .csv) to avoid header encoding issues.
- Remove empty trailing columns and blank header cells.
- Verify civil ids (alrkm_almdny, alrkm_almdn) are strings, not numbers with truncated leading digits.
- For contractor imports, confirm voters are already linked to the target election.

Common Import Mistakes and Fixes
- Missing or misspelled headers: fix the header row to match the exact keys shown in templates.
- Numeric IDs lose leading digits in Excel: format civil id columns as Text before saving.
- Wrong contractor name in contractor import: ensure asm_almtaahd matches contractors.name exactly or select sub_contractor in the form.
- Mixed languages/encoding in CSV: re-save CSV as UTF-8 and avoid Excel locale auto-conversions.
- Duplicate voters from minor field differences: keep stable key fields (alrkm_almdny, alasm) consistent across uploads.
- Using VoterCheck with wrong IDs: mrgaa_aldakhly must match voters.alrkm_almd_yn, not the name.

Known Gaps and Risks
- No migration found for appointments, seos, and clients tables though models/requests exist.
- VoterCheck uses fixed committee_id and attend_id values; review before production use.
- VotersImport uses firstOrCreate on the full voter data array; duplicates may be created if non-key fields differ.
- Several voter columns are optional in the import and may remain null.
