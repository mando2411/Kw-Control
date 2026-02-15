<div class="card-header">
    <form class="form-inline search-form search-box">
        <div class="form-group">
            <input id="datatable-search" aria-label="Search" class="form-control" type="search"
                   placeholder="Search..">
        </div>
    </form>
    <div>
        @if(\Illuminate\Support\Facades\Route::has('dashboard.'.$model->plural()->lower()->kebab().'.create') &&
        admin()->can($model->plural()->lower()->kebab().'.create'))
            <a href="{{ route('dashboard.'.$model->plural()->lower()->kebab().'.create') }}" type="button"
               class="btn btn-primary add-row mt-md-0 mt-2">
                Add {{$model->plural()->studly()}}
            </a>
        @endif
        @if($model->plural()->lower() == 'candidates')
            <a id="addFakeCandidate" type="button"class="btn btn-danger add-row mt-md-0 mt-2">
                Add Fake Candidate
            </a>
        @endif
        {{$slot}}
    </div>
        
</div>
<script>
    window.onload = () => {
        $('.auto-translate').each(function () {
            $(this).on('click', function () {
                if (confirm('This will overwrite any translated property!')) {
                    $(this).addClass('disabled')
                    let $icon = $(this).find('.icon')
                    $icon.removeClass('fa-language')
                    $icon.addClass('fa-spinner fa-spin')
                    axios.post("{{ route('dashboard.model.auto.translate') }}", {
                        model: $(this).data('model'),
                        id: $(this).data('id'),
                    })
                        .then(response => toastr.success(response.data.message))
                        .catch(error => toastr.error(error?.response?.data?.message || "Unexpected Error!"))
                        .finally(() => {
                            $(this).removeClass('disabled')
                            $icon.addClass('fa-language')
                            $icon.removeClass('fa-spinner fa-spin')
                        })
                }
            })
        })
    }
</script>
