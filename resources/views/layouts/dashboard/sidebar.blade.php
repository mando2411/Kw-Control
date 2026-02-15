<!-- Page Sidebar Start-->
<div class="page-sidebar">
    <div class="main-header-left d-none d-lg-block">
        <div class="logo-wrapper">
            <a href="{{ route('dashboard') }}">
                <img class="d-none d-lg-block blur-up lazyloaded sidebar-logo" src="{{ logo() }}" alt="">
            </a>
        </div>
    </div>
    <div class="sidebar custom-scrollbar">
        <div class="sidebar-user">
            <img class="img-60" src="{{ admin()->image ? admin()->image : asset('assets/admin/images/users/user-placeholder.png') }}" onerror="this.onerror=null;this.src='{{ asset('assets/admin/images/users/user-placeholder.png') }}';" alt="user image">
            <div>
                <h6 class="f-14">{{ admin()->first_name }}</h6>
                <p>{{ admin()->role }}</p>
            </div>
        </div>

        @php
            $uiPolicy = setting(\App\Enums\SettingKey::UI_MODE_POLICY->value, true) ?: 'user_choice';
            $uiPolicy = in_array($uiPolicy, ['user_choice', 'modern', 'classic'], true) ? $uiPolicy : 'user_choice';
        @endphp
        @if ($uiPolicy === 'user_choice')
            <div class="sidebar-ui-mode" dir="rtl">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="sidebarUiModeToggle" aria-pressed="false">
                    <label class="form-check-label" for="sidebarUiModeToggle">الشكل الحديث</label>
                </div>
            </div>
        @endif
        <div class="sidebar-color-mode" dir="rtl">
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" id="sidebarColorModeToggle" aria-pressed="false">
                <label class="form-check-label" for="sidebarColorModeToggle">دارك مود</label>
            </div>
        </div>
        <ul class="sidebar-menu">
            <x-dashboard.sidebar.single-link title="الصفحه الرئيسيه" link="{{ route('dashboard') }}" icon="home" />

            <x-dashboard.sidebar.single-link title="Media" class="open-media" link="javascript:;" icon="camera" />

            <x-dashboard.sidebar.link-with-child title="مستخدم" icon="user" :permissions="['users.list', 'users.create', 'users.edit', 'users.delete']" :children="[
                [
                    'title' => 'مستخدم',
                    'link' => route('dashboard.users.index'),
                    'permissions' => ['users.list', 'users.edit', 'users.delete'],
                ],
                [
                    'title' => ' اضافه مستخدم',
                    'link' => route('dashboard.users.create'),
                    'permissions' => ['users.create'],
                ],
            ]" />

            <x-dashboard.sidebar.link-with-child title="صلاحيات" icon="users" :permissions="['roles.list', 'roles.create', 'roles.edit', 'roles.delete']" :children="[
                [
                    'title' => 'صلاحيات',
                    'link' => route('dashboard.roles.index'),
                    'permissions' => ['roles.list', 'roles.edit', 'roles.delete'],
                ],
                [
                    'title' => ' اضافه صلاحيات',
                    'link' => route('dashboard.roles.create'),
                    'permissions' => ['roles.create'],
                ],
            ]" />

            <x-dashboard.sidebar.link-with-child title="التقاير" icon="users" :permissions="['reports.list', 'reports.create', 'reports.edit', 'reports.delete']" :children="[
                [
                    'title' => 'التقاير',
                    'link' => route('dashboard.reports.index'),
                    'permissions' => ['reports.list', 'reports.edit', 'reports.delete'],
                ],
                [
                    'title' => ' استخراج تقرير',
                    'link' => route('dashboard.reports.create'),
                    'permissions' => ['reports.create'],
                ],
            ]" />

<x-dashboard.sidebar.link-with-child title="الانتخابات" icon="users" :permissions="['elections.list', 'elections.create', 'elections.edit', 'elections.delete']" :children="[
    [
        'title' => 'الانتخابات',
        'link' => route('dashboard.elections.index'),
        'permissions' => ['elections.list', 'elections.edit', 'elections.delete'],
    ],
    [
        'title' => 'اضافه انتخابات',
        'link' => route('dashboard.elections.create'),
        'permissions' => ['elections.create'],
    ],
]" />


<x-dashboard.sidebar.link-with-child title="المتعهدين" icon="users" :permissions="['contractors.list', 'contractors.create', 'contractors.edit', 'contractors.delete']" :children="[
    [
        'title' => 'المتعهدين',
        'link' => route('dashboard.contractors.index'),
        'permissions' => ['contractors.list', 'contractors.edit', 'contractors.delete'],
    ],
    [
        'title' => ' اضافه متعهد رئيسي',
        'link' => route('dashboard.contractors.create'),
        'permissions' => ['contractors.create'],
    ],
]" />

<x-dashboard.sidebar.link-with-child title="المناديب" icon="users" :permissions="['representatives.list', 'representatives.create', 'representatives.edit', 'representatives.delete']" :children="[
    [
        'title' => 'المناديب',
        'link' => route('dashboard.representatives.index'),
        'permissions' => ['representatives.list', 'representatives.edit', 'representatives.delete'],
    ],
    [
        'title' => 'اضافه مندوب',
        'link' => route('dashboard.representatives.create'),
        'permissions' => ['representatives.create'],
    ],
]" />


<x-dashboard.sidebar.link-with-child title="المرشحين" icon="users" :permissions="['candidates.list', 'candidates.create', 'candidates.edit', 'candidates.delete']" :children="[
    [
        'title' => 'المرشحين',
        'link' => route('dashboard.candidates.index'),
        'permissions' => ['candidates.list', 'candidates.edit', 'candidates.delete', 'candidates.updateVotes','candidates.setVotes'],
    ],
    [
        'title' => 'اضافه مرشح',
        'link' => route('dashboard.candidates.create'),
        'permissions' => ['candidates.create'],
    ],
]" />


			
<x-dashboard.sidebar.link-with-child title="الناخبين" icon="users" :permissions="['voters.list', 'voters.create', 'voters.edit', 'voters.delete' ,'voters.update-status','import.contractor.voters']" :children="[
    [
        'title' => 'الناخبين',
        'link' => route('dashboard.voters.index'),
        'permissions' => ['voters.list', 'voters.edit', 'voters.delete' ,'voters.update-status'],
    ],
    [
        'title' => 'اضافه ناخب',
        'link' => route('dashboard.voters.create'),
        'permissions' => ['voters.create'],
    ],
    [
        'title' => 'اضافه ناخبين لمتعهد',
        'link' => route('dashboard.import-contractor-voters-form'),
        'permissions' => ['import.contractor.voters'],
    ],
]" />
			
			
<x-dashboard.sidebar.link-with-child title="اللجان" icon="users" :permissions="['committees.list', 'committees.create', 'committees.edit', 'committees.delete', 'committees.generate' ,'committees.update-status']" :children="[
    [
        'title' => 'اللجان',
        'link' => route('dashboard.committees.index'),
        'permissions' => ['committees.list', 'committees.edit', 'committees.delete' ,'committees.update-status'],
    ],
    [
        'title' => 'اضافه لجنه',
        'link' => route('dashboard.committees.create'),
        'permissions' => ['committees.create'],
    ],
    [
        'title' => 'اضافه عدد من لجنه',
        'link' => route('dashboard.committees.generate'),
        'permissions' => ['committees.generate'],
    ],
]" />


<x-dashboard.sidebar.link-with-child title="المدارس" icon="users" :permissions="['schools.list', 'schools.create', 'schools.edit', 'schools.delete' ,'schools.update-status']" :children="[
    [
        'title' => 'المدارس',
        'link' => route('dashboard.schools.index'),
        'permissions' => ['schools.list', 'schools.edit', 'schools.delete' ,'schools.update-status'],
    ],
    [
        'title' => 'اضافه مدرسه',
        'link' => route('dashboard.schools.create'),
        'permissions' => ['schools.create'],
    ],
]" />


<x-dashboard.sidebar.link-with-child title="العوائل" icon="users" :permissions="['families.list', 'families.create', 'families.edit', 'families.delete' ,'families.update-status']" :children="[
    [
        'title' => 'العوائل',
        'link' => route('dashboard.families.index'),
        'permissions' => ['families.list', 'families.edit', 'families.delete' ,'families.update-status'],
    ],
    [
        'title' => 'اضافه عائله',
        'link' => route('dashboard.families.create'),
        'permissions' => ['families.create'],
    ],
]" />
<x-dashboard.sidebar.single-link :permissions="['settings.show']" title="الرسائل" link="{{ route('dashboard.settings.show') }}" icon="settings" />
<x-dashboard.sidebar.single-link :permissions="['settings.show']" title=" الاعدادت " link="{{ route('dashboard.settings.result') }}" icon="settings" />


<br>
<br>
     {{-- Sidebar Auto Generation --}}


            <x-dashboard.sidebar.single-link title="تسجيل خروج" link="{{ route('logout') }}" icon="log-in" />

        </ul>
    </div>
</div>
<!-- Page Sidebar Ends-->
