@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body schools-page" dir="rtl" style="text-align: right;">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="المدارس">
            <li class="breadcrumb-item active">إدارة المدارس</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card schools-shell-card">
                        <div class="card-body schools-header-wrap">
                            <div class="schools-hero-card">
                                <h4 class="schools-title mb-1">لوحة المدارس</h4>
                                <p class="schools-subtitle mb-0">عرض شامل للمدارس مع النوع والحملة الانتخابية وعدد اللجان والإجراءات.</p>
                            </div>

                            <div class="schools-tools d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                                <form class="form-inline search-form search-box mb-0 schools-search-form" role="search">
                                    <div class="form-group mb-0 w-100">
                                        <input id="datatable-search" aria-label="بحث في المدارس" class="form-control" type="search"
                                               placeholder="ابحث باسم المدرسة أو النوع أو الحملة الانتخابية...">
                                    </div>
                                </form>

                                @if(admin()->can('schools.create'))
                                    <a href="{{ route('dashboard.schools.create') }}" class="btn btn-primary add-row mt-md-0 mt-2 schools-add-btn">
                                        إضافة مدرسة جديدة
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="card-body order-datatable overflow-x-auto schools-table-wrap">
                            <div class="alert alert-light border mb-3 schools-hint" role="status">
                                <strong>ملاحظة:</strong>
                                تم إخفاء معرف المدرسة، ويظهر الآن: اسم المدرسة، النوع، الحملة، تاريخ الإنشاء، وعدد اللجان.
                            </div>
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
        .schools-shell-card {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 44, 77, 0.12);
            background: linear-gradient(165deg, #f8fbff 0%, #eef5ff 42%, #ffffff 100%);
        }

        .schools-header-wrap {
            border-bottom: 1px solid #dfe8f5;
            background: radial-gradient(circle at top right, rgba(36, 126, 223, 0.14), transparent 56%), #ffffff;
            padding: 1.25rem;
        }

        .schools-hero-card {
            border: 1px solid #dbe7f8;
            border-radius: 16px;
            padding: 1rem 1.1rem;
            background: linear-gradient(140deg, #ffffff 0%, #f4f8ff 100%);
        }

        .schools-title {
            color: #123c67;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .schools-subtitle {
            color: #4f6680;
            font-size: 0.95rem;
        }

        .schools-search-form {
            max-width: 540px;
            flex: 1 1 420px;
        }

        .schools-search-form .form-control {
            height: 44px;
            border-radius: 12px;
            border-color: #cddbef;
            background-color: #f9fbff;
            padding-inline: 0.9rem;
        }

        .schools-search-form .form-control:focus {
            border-color: #2f81db;
            box-shadow: 0 0 0 0.2rem rgba(47, 129, 219, 0.16);
            background-color: #ffffff;
        }

        .schools-add-btn {
            border-radius: 12px;
            padding: 0.55rem 1rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .schools-table-wrap {
            background: #ffffff;
        }

        .schools-hint {
            border-radius: 12px;
            color: #2f4b67;
            background-color: #f8fbff;
        }

        .schools-page .dataTables_wrapper .dataTables_filter {
            display: none;
        }

        .schools-page .dataTables_wrapper .dt-buttons {
            margin-bottom: 0.75rem;
        }

        .schools-page .dataTables_wrapper .dt-buttons .btn {
            border-radius: 10px;
            border: 0;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .schools-page #data-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
        }

        .schools-page #data-table thead th {
            border-bottom: 1px solid #dbe6f5;
            background: #f4f8ff;
            color: #1f4369;
            font-weight: 700;
            padding: 0.9rem 0.75rem;
            white-space: nowrap;
        }

        .schools-page #data-table tbody td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid #edf2fa;
            color: #23415f;
            vertical-align: middle;
        }

        .schools-page #data-table tbody tr {
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .schools-page #data-table tbody tr:hover {
            background-color: #f9fbff;
            transform: translateY(-1px);
            box-shadow: inset 0 0 0 1px #e0eaf8;
        }

        .schools-page #data-table tbody td:last-child a {
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

        .schools-page #data-table tbody td:last-child a:hover {
            color: #ffffff;
            background: #2878d2;
        }

        @media (max-width: 991px) {
            .schools-header-wrap {
                padding: 1rem;
            }

            .schools-tools {
                flex-direction: column;
                align-items: stretch !important;
            }

            .schools-search-form {
                max-width: 100%;
                flex: 1 1 auto;
            }

            .schools-add-btn {
                width: 100%;
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
        });
    </script>
@endpush
