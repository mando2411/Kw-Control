@props([
    'id' => null,
    'model'
])
<style>
    .auto-translate-d {
        position: fixed;
        bottom: 10%;
        right: 2%;
        width: 50px;
        height: 50px;
        padding: 0px;
        display: flex;
        justify-content: center;
        align-content: center;
        align-items: center;
        font-size: 24px;
    }
</style>
<script>
    window.onload = () => {
        $('.auto-translate').each(function () {
            $(this).on('click', function () {
                if(confirm('This will overwrite any translated property!')) {
                    $(this).addClass('disabled')
                    let $icon = $(this).find('.icon')
                    $icon.removeClass('fa-language')
                    $icon.addClass('fa-spinner fa-spin')
                    axios.post("{{ route('dashboard.model.auto.translate') }}", {
                        model: $(this).data('model'),
                        id: $(this).data('id'),
                    })
                        .then(response=> toastr.success(response.data.message))
                        .catch(error=> toastr.error(error?.response?.data?.message || "Unexpected Error!"))
                        .finally(()=> {
                            $(this).removeClass('disabled')
                            $icon.addClass('fa-language')
                            $icon.removeClass('fa-spinner fa-spin')
                        })
                }
            })
        })
    }
</script>
<a href="javascript:;" title="Run automatic translation for this resource"
   class="btn auto-translate auto-translate-d btn-primary rounded-circle p-fixed"
   data-model="{{ $model }}" @if($id) data-id="{{ $id }}" @endif >
    <i class="fa icon fa-language"></i>
</a>
