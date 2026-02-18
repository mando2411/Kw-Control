<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Committee;
use App\Models\Contractor;
use App\Models\Election;
use App\Models\Family;
use App\Models\Role;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoFullElectionSeeder extends Seeder
{
    private const ELECTION_NAME = 'انتخابات تجريبية شاملة 2026';
    private const VOTERS_COUNT = 1000;
    private const LIST_LEADERS_COUNT = 3;
    private const MEMBERS_PER_LIST = 8;
    private const INDEPENDENTS_COUNT = 10;
    private const MAIN_CONTRACTORS_PER_LEADER = 2;
    private const SUB_CONTRACTORS_PER_MAIN = 2;

    public function run(): void
    {
        DB::transaction(function () {
            $this->ensureRoles();
            $this->purgeOldDemoData();

            $election = Election::create([
                'name' => self::ELECTION_NAME,
                'start_date' => now()->addDays(7)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'type' => 'عام',
            ]);

            $committees = collect();
            for ($index = 1; $index <= 6; $index++) {
                $committees->push(Committee::create([
                    'name' => "لجنة تجريبية {$index}",
                    'type' => $index % 2 === 0 ? 'رجال' : 'نساء',
                    'election_id' => $election->id,
                    'status' => true,
                ]));
            }

            $families = collect();
            for ($index = 1; $index <= 30; $index++) {
                $families->push(Family::create([
                    'name' => "عائلة تجريبية {$index}",
                    'election_id' => $election->id,
                ]));
            }

            $attenderUser = $this->createUser(
                name: 'مندوب تجريبي',
                email: 'demo.attender@kw.local',
                electionId: $election->id,
                creatorId: null,
                roles: ['مندوب']
            );

            $listLeaders = collect();
            for ($leaderIndex = 1; $leaderIndex <= self::LIST_LEADERS_COUNT; $leaderIndex++) {
                $leaderUser = $this->createUser(
                    name: "رئيس قائمة تجريبي {$leaderIndex}",
                    email: "demo.listleader{$leaderIndex}@kw.local",
                    electionId: $election->id,
                    creatorId: null,
                    roles: ['مرشح', 'مرشح رئيس قائمة']
                );

                $listLeaderCandidate = Candidate::create([
                    'user_id' => $leaderUser->id,
                    'election_id' => $election->id,
                    'max_contractor' => 80,
                    'max_represent' => 120,
                    'votes' => 0,
                    'candidate_type' => 'list_leader',
                    'list_candidates_count' => self::MEMBERS_PER_LIST + 2,
                    'list_name' => "قائمة تجريبية {$leaderIndex}",
                    'list_logo' => null,
                    'is_actual_list_candidate' => true,
                    'list_leader_candidate_id' => null,
                ]);

                for ($memberIndex = 1; $memberIndex <= self::MEMBERS_PER_LIST; $memberIndex++) {
                    $memberUser = $this->createUser(
                        name: "مرشح قائمة {$leaderIndex}-{$memberIndex}",
                        email: "demo.listmember{$leaderIndex}.{$memberIndex}@kw.local",
                        electionId: $election->id,
                        creatorId: $leaderUser->id,
                        roles: ['مرشح']
                    );

                    Candidate::create([
                        'user_id' => $memberUser->id,
                        'election_id' => $election->id,
                        'max_contractor' => 40,
                        'max_represent' => 60,
                        'votes' => 0,
                        'candidate_type' => 'candidate',
                        'list_candidates_count' => null,
                        'list_name' => $listLeaderCandidate->list_name,
                        'list_logo' => null,
                        'is_actual_list_candidate' => $memberIndex % 4 !== 0,
                        'list_leader_candidate_id' => $listLeaderCandidate->id,
                    ]);
                }

                $listLeaders->push([
                    'user' => $leaderUser,
                    'candidate' => $listLeaderCandidate,
                ]);
            }

            for ($independentIndex = 1; $independentIndex <= self::INDEPENDENTS_COUNT; $independentIndex++) {
                $independentUser = $this->createUser(
                    name: "مرشح مستقل {$independentIndex}",
                    email: "demo.independent{$independentIndex}@kw.local",
                    electionId: $election->id,
                    creatorId: null,
                    roles: ['مرشح']
                );

                Candidate::create([
                    'user_id' => $independentUser->id,
                    'election_id' => $election->id,
                    'max_contractor' => 35,
                    'max_represent' => 50,
                    'votes' => 0,
                    'candidate_type' => 'candidate',
                    'list_candidates_count' => null,
                    'list_name' => null,
                    'list_logo' => null,
                    'is_actual_list_candidate' => true,
                    'list_leader_candidate_id' => null,
                ]);
            }

            $mainContractors = collect();
            $allContractors = collect();

            foreach ($listLeaders as $leaderData) {
                /** @var User $leaderUser */
                $leaderUser = $leaderData['user'];

                for ($mainIndex = 1; $mainIndex <= self::MAIN_CONTRACTORS_PER_LEADER; $mainIndex++) {
                    $mainUser = $this->createUser(
                        name: "متعهد أساسي {$leaderUser->id}-{$mainIndex}",
                        email: "demo.maincontractor{$leaderUser->id}.{$mainIndex}@kw.local",
                        electionId: $election->id,
                        creatorId: $leaderUser->id,
                        roles: ['متعهد']
                    );

                    $mainContractor = Contractor::create([
                        'parent_id' => null,
                        'user_id' => $mainUser->id,
                        'election_id' => $election->id,
                        'note' => 'بيانات تجريبية - متعهد أساسي',
                        'trust' => rand(60, 95),
                        'status' => 'active',
                        'token' => Str::uuid()->toString(),
                        'creator_id' => $leaderUser->id,
                        'name' => $mainUser->name,
                        'email' => $mainUser->email,
                        'phone' => $mainUser->phone,
                    ]);

                    $mainContractors->push($mainContractor);
                    $allContractors->push($mainContractor);

                    for ($subIndex = 1; $subIndex <= self::SUB_CONTRACTORS_PER_MAIN; $subIndex++) {
                        $subUser = $this->createUser(
                            name: "متعهد فرعي {$mainContractor->id}-{$subIndex}",
                            email: "demo.subcontractor{$mainContractor->id}.{$subIndex}@kw.local",
                            electionId: $election->id,
                            creatorId: $leaderUser->id,
                            roles: ['متعهد']
                        );

                        $subContractor = Contractor::create([
                            'parent_id' => $mainContractor->id,
                            'user_id' => $subUser->id,
                            'election_id' => $election->id,
                            'note' => 'بيانات تجريبية - متعهد فرعي',
                            'trust' => rand(40, 85),
                            'status' => 'active',
                            'token' => Str::uuid()->toString(),
                            'creator_id' => $leaderUser->id,
                            'name' => $subUser->name,
                            'email' => $subUser->email,
                            'phone' => $subUser->phone,
                        ]);

                        $allContractors->push($subContractor);
                    }
                }
            }

            $committeeIds = $committees->pluck('id')->values()->all();
            $familyIds = $families->pluck('id')->values()->all();
            $contractors = $allContractors->values();

            for ($voterIndex = 1; $voterIndex <= self::VOTERS_COUNT; $voterIndex++) {
                $isAttended = $voterIndex % 3 === 0;

                $voter = Voter::create([
                    'name' => 'ناخب تجريبي ' . str_pad((string) $voterIndex, 4, '0', STR_PAD_LEFT),
                    'almrgaa' => (string) rand(1, 300),
                    'albtn' => (string) rand(1, 90),
                    'alfraa' => (string) rand(1, 40),
                    'yearOfBirth' => now()->subYears(rand(18, 80))->toDateString(),
                    'btn_almoyhy' => (string) rand(1000, 9999),
                    'tary_kh_alandmam' => now()->subDays(rand(1, 600))->toDateString(),
                    'alrkm_ala_yl_llaanoan' => (string) rand(1, 200),
                    'alktaa' => 'قطاع ' . rand(1, 12),
                    'alrkm_almd_yn' => (string) rand(10000, 99999),
                    'alsndok' => (string) rand(1, 30),
                    'phone1' => '500' . str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT),
                    'phone2' => '511' . str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT),
                    'region' => 'منطقة تجريبية ' . rand(1, 10),
                    'type' => rand(0, 1) ? 'ثابت' : 'عام',
                    'status' => $isAttended,
                    'committee_id' => $committeeIds[array_rand($committeeIds)],
                    'family_id' => $familyIds[array_rand($familyIds)],
                    'alfkhd' => 'فخذ ' . rand(1, 20),
                    'cod1' => (string) rand(10, 99),
                    'cod2' => (string) rand(100, 999),
                    'cod3' => (string) rand(1000, 9999),
                    'father' => 'اسم الأب ' . rand(1, 300),
                    'age' => (string) rand(18, 80),
                    'user_id' => null,
                    'grand' => null,
                    'restricted' => null,
                    'attend_id' => $isAttended ? $attenderUser->id : null,
                    'normalized_name' => Str::lower('ناخب تجريبي ' . $voterIndex),
                ]);

                $voter->election()->syncWithoutDetaching([$election->id]);

                /** @var Contractor $selectedContractor */
                $selectedContractor = $contractors->random();
                $selectedContractor->voters()->syncWithoutDetaching([
                    $voter->id => ['percentage' => (string) rand(0, 100)],
                ]);
            }
        });

        $this->command?->info('Demo dataset seeded successfully: list leaders, list members, independents, contractors (main/sub), and 1000 voters.');
    }

    private function ensureRoles(): void
    {
        foreach (['مرشح', 'مرشح رئيس قائمة', 'متعهد', 'مندوب'] as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }
    }

    private function purgeOldDemoData(): void
    {
        $demoUserIds = User::withTrashed()
            ->where('email', 'like', 'demo.%@kw.local')
            ->pluck('id');

        if ($demoUserIds->isNotEmpty()) {
            Candidate::withoutGlobalScopes()->whereIn('user_id', $demoUserIds)->delete();
            Contractor::withoutGlobalScopes()->whereIn('user_id', $demoUserIds)->delete();
            User::withTrashed()->whereIn('id', $demoUserIds)->forceDelete();
        }

        Voter::withoutGlobalScopes()->where('name', 'like', 'ناخب تجريبي %')->delete();
        Family::withoutGlobalScopes()->where('name', 'like', 'عائلة تجريبية %')->delete();
        Committee::withoutGlobalScopes()->where('name', 'like', 'لجنة تجريبية %')->delete();
        Election::where('name', self::ELECTION_NAME)->delete();
    }

    private function createUser(string $name, string $email, ?int $electionId, ?int $creatorId, array $roles): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('12345678'),
            'phone' => '555' . str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT),
            'image' => null,
            'ui_mode' => 'modern',
            'creator_id' => $creatorId,
            'election_id' => $electionId,
        ]);

        $user->syncRoles($roles);

        return $user;
    }
}
