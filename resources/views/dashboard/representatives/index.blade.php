@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body representatives-page" dir="rtl" style="text-align: right;">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="المندوبون">
            <li class="breadcrumb-item active">إدارة المندوبين</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card representatives-shell-card">
                        <div class="card-body representatives-header-wrap">
                            <div class="representatives-hero-card">
                                <h4 class="representatives-title mb-1">لوحة المندوبين</h4>
                                <p class="representatives-subtitle mb-0">عرض منظم للمندوبين يشمل الحملة الانتخابية والمرشح الذي أضاف كل مندوب.</p>
                            </div>

                            <div class="representatives-tools d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                                <form class="form-inline search-form search-box mb-0 representatives-search-form" role="search">
                                    <div class="form-group mb-0 w-100">
                                        <input id="datatable-search" aria-label="بحث في المندوبين" class="form-control" type="search"
                                               placeholder="ابحث باسم المندوب أو الحملة أو اسم المرشح...">
                                    </div>
                                </form>

                                @if(admin()->can('representatives.create'))
                                    <a href="{{ route('dashboard.representatives.create') }}" class="btn btn-primary add-row mt-md-0 mt-2 representatives-add-btn">
                                        إضافة مندوب جديد
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="card-body order-datatable overflow-x-auto representatives-table-wrap">
                            <!-- Representative table intentionally shows only business fields (ID hidden by design). -->
                            <div>
                                {!! $dataTable->table(['class' => 'display align-middle w-100']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@push('css')
    <style>
        .representatives-shell-card {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 44, 77, 0.12);
            background: linear-gradient(165deg, #f8fbff 0%, #eef5ff 42%, #ffffff 100%);
        }

        .representatives-header-wrap {
            border-bottom: 1px solid #dfe8f5;
            background: radial-gradient(circle at top right, rgba(36, 126, 223, 0.14), transparent 56%), #ffffff;
            padding: 1.25rem;
        }

        .representatives-hero-card {
            border: 1px solid #dbe7f8;
            border-radius: 16px;
            padding: 1rem 1.1rem;
            background: linear-gradient(140deg, #ffffff 0%, #f4f8ff 100%);
        }

        .representatives-title {
            color: #123c67;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .representatives-subtitle {
            color: #4f6680;
            font-size: 0.95rem;
        }

        .representatives-search-form {
            max-width: 540px;
            flex: 1 1 420px;
        }

        .representatives-search-form .form-control {
            height: 44px;
            border-radius: 12px;
            border-color: #cddbef;
            background-color: #f9fbff;
            padding-inline: 0.9rem;
        }

        .representatives-search-form .form-control:focus {
            border-color: #2f81db;
            box-shadow: 0 0 0 0.2rem rgba(47, 129, 219, 0.16);
            background-color: #ffffff;
        }

        .representatives-add-btn {
            border-radius: 12px;
            padding: 0.55rem 1rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .representatives-table-wrap {
            background: #ffffff;
            padding-top: 0.35rem;
        }

        .representatives-page .dataTables_wrapper .dataTables_filter {
            display: none;
        }

        .representatives-page .dataTables_wrapper .dt-buttons {
            margin-bottom: 0.75rem;
        }

        .representatives-page .dataTables_wrapper .dt-buttons .btn {
            border-radius: 10px;
            border: 0;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .representatives-page #data-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
            border: 1px solid #e5edf8;
            border-radius: 14px;
            overflow: hidden;
        }

        .representatives-page #data-table thead th {
            border-bottom: 1px solid #dbe6f5;
            background: linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%);
            color: #1f4369;
            font-weight: 700;
            padding: 0.9rem 0.75rem;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .representatives-page #data-table tbody td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid #edf2fa;
            color: #23415f;
            vertical-align: middle;
        }

        .representatives-page #data-table tbody tr {
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .representatives-page #data-table tbody tr:nth-child(even) {
            background: #fcfdff;
        }

        .representatives-page #data-table tbody tr:hover {
            background-color: #f9fbff;
            transform: translateY(-1px);
            box-shadow: inset 0 0 0 1px #e0eaf8;
        }

        .representatives-page #data-table tbody td:last-child a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            color: #1f5ea6;
            background: #eef5ff;
            margin-inline: 0.2rem;
            transition: all 0.2s ease;
        }

        .representatives-page #data-table tbody td:last-child a:hover {
            color: #ffffff;
            background: #2878d2;
        }

        .representatives-page #data-table tbody td:nth-child(3) {
            min-width: 260px;
        }

        .representatives-page .rep-created-by-editor {
            min-width: 230px;
            max-width: 320px;
            gap: 0.45rem !important;
        }

        .representatives-page .rep-created-by-editor .js-rep-creator-select {
            min-height: 38px;
            border-radius: 10px;
            border-color: #cddcf1;
            color: #1f4369;
            font-weight: 600;
            background-color: #f8fbff;
            padding-inline: 0.65rem;
            box-shadow: 0 1px 0 rgba(13, 63, 116, 0.03);
            transition: all 0.2s ease;
        }

        .representatives-page .rep-created-by-editor .js-rep-creator-select:focus {
            border-color: #2f81db;
            background-color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(47, 129, 219, 0.16);
            outline: none;
        }

        .representatives-page .rep-created-by-editor .js-save-rep-creator {
            min-height: 34px;
            border-radius: 10px;
            border-width: 1px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: #1f66b3;
            border-color: #c4daf4;
            background: linear-gradient(180deg, #ffffff 0%, #f1f7ff 100%);
            transition: all 0.2s ease;
        }

        .representatives-page .rep-created-by-editor .js-save-rep-creator:hover:not(:disabled) {
            color: #ffffff;
            border-color: #2f81db;
            background: linear-gradient(135deg, #2f81db 0%, #226fc6 100%);
            box-shadow: 0 8px 16px rgba(34, 111, 198, 0.22);
            transform: translateY(-1px);
        }

        .representatives-page .rep-created-by-editor .js-save-rep-creator:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 991px) {
            .representatives-header-wrap {
                padding: 1rem;
            }

            .representatives-tools {
                flex-direction: column;
                align-items: stretch !important;
            }

            .representatives-search-form {
                max-width: 100%;
                flex: 1 1 auto;
            }

            .representatives-add-btn {
                width: 100%;
            }

            .representatives-page #data-table tbody td:nth-child(3) {
                min-width: 220px;
            }

            .representatives-page .rep-created-by-editor {
                min-width: 200px;
                max-width: 260px;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchInput = document.getElementById('datatable-search');
            if (!searchInput || !window.jQuery) {
                return;
            }

            var bindSearch = function () {
                var tableElement = window.jQuery('#data-table');
                if (!tableElement.length || !window.jQuery.fn.dataTable.isDataTable(tableElement)) {
                    setTimeout(bindSearch, 250);
                    return;
                }

                var dataTable = tableElement.DataTable();
                searchInput.addEventListener('input', function () {
                    dataTable.search(this.value).draw();
                });
            };

            bindSearch();

            var updateUrlTemplate = @json(route('dashboard.rep.change', ['id' => '__ID__']));

            document.addEventListener('click', function (event) {
                var saveButton = event.target.closest('.js-save-rep-creator');
                if (!saveButton) {
                    return;
                }

                var repId = saveButton.getAttribute('data-rep-id');
                if (!repId) {
                    return;
                }

                var row = saveButton.closest('tr');
                if (!row) {
                    return;
                }

                var creatorSelect = row.querySelector('.js-rep-creator-select');
                if (!creatorSelect) {
                    return;
                }

                var selectedCreatorId = creatorSelect.value || null;

                saveButton.disabled = true;

                axios.post(updateUrlTemplate.replace('__ID__', repId), {
                    creator_id: selectedCreatorId,
                })
                    .then(function (response) {
                        if (window.toastr) {
                            toastr.success(response?.data?.message || 'تم تعديل المرشح التابع للمندوب بنجاح');
                        }
                    })
                    .catch(function (error) {
                        var message = error?.response?.data?.message || 'تعذر حفظ التعديل، يرجى المحاولة مرة أخرى.';
                        if (window.toastr) {
                            toastr.error(message);
                        }
                    })
                    .finally(function () {
                        saveButton.disabled = false;
                    });
            });
        });
    </script>
@endpush
