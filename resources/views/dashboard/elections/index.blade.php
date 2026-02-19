@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="الانتخابات">
            <li class="breadcrumb-item active">إدارة الانتخابات</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <form class="form-inline search-form search-box mb-0">
                                <div class="form-group">
                                    <input id="datatable-search" aria-label="بحث" class="form-control" type="search" placeholder="ابحث في الانتخابات...">
                                </div>
                            </form>
                            @if(admin()->can('elections.create'))
                                <a href="{{ route('dashboard.elections.create') }}" class="btn btn-primary add-row mt-md-0 mt-2">
                                    إضافة انتخابات جديدة
                                </a>
                            @endif
                        </div>
                        <div class="card-body order-datatable overflow-x-auto">
                            <div class="">
                                {!! $dataTable->table(['class'=>'display']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection
