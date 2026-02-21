<script>
(function () {
    $(document)
        .off('click.exportModalOpen', 'button[data-bs-target="#elkshoofDetails"]')
        .on('click.exportModalOpen', 'button[data-bs-target="#elkshoofDetails"]', function () {
            var siblingInput = $(this).siblings('input[type="hidden"]');
            if (!siblingInput.length) return;

            var inputId = siblingInput.attr('id');
            var value = siblingInput.val();
            if (!inputId) return;

            $('#search_id').attr('name', inputId);
            $('#search_id').val(value || '');
        });

    $(document)
        .off('click.exportModalAction', '#export .button')
        .on('click.exportModalAction', '#export .button', function (event) {
            event.preventDefault();

            var buttonValue = $(this).val();
            $('#type').val(buttonValue);

            var form = $('#export');
            if (!form.length) return;

            form.find('input[name="voter[]"]').remove();

            var checkedValues = $.map($('.check:checked'), function (element) {
                return $(element).val();
            });

            checkedValues.forEach(function (value) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'voter[]',
                    value: value
                }).appendTo('#export');
            });

            var submitBtn = $(this);
            var originalHtml = submitBtn.html();
            submitBtn.prop('disabled', true).text('جاري التنفيذ...');

            var formDataArray = form.serializeArray();
            var queryData = {};

            $.each(formDataArray, function (_, field) {
                if (queryData[field.name]) {
                    if (!Array.isArray(queryData[field.name])) {
                        queryData[field.name] = [queryData[field.name]];
                    }
                    queryData[field.name].push(field.value || '');
                } else {
                    queryData[field.name] = field.value || '';
                }
            });

            axios
                .get(form.attr('action'), {
                    params: queryData,
                    responseType: buttonValue === 'Excel' || buttonValue === 'PDF' ? 'blob' : 'json'
                })
                .then(function (res) {
                    if (buttonValue === 'Excel' || buttonValue === 'PDF') {
                        var blobUrl = window.URL.createObjectURL(new Blob([res.data]));
                        var link = document.createElement('a');
                        link.href = blobUrl;
                        link.setAttribute('download', buttonValue === 'Excel' ? 'Voters.xlsx' : 'Voters.pdf');
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(blobUrl);
                        return;
                    }

                    if (buttonValue === 'Send' && res.data && res.data.Redirect_Url) {
                        window.open(res.data.Redirect_Url, '_blank');
                        return;
                    }

                    if (res.data && res.data.Redirect_Url) {
                        window.location.href = res.data.Redirect_Url;
                        return;
                    }

                    var newTab = window.open();
                    if (newTab && typeof res.data === 'string') {
                        newTab.document.open();
                        newTab.document.write(res.data);
                        newTab.document.close();
                    }
                })
                .catch(function (error) {
                    toastr.error(error.response?.data?.error ?? '{{ __('main.unexpected-error') }}');
                })
                .finally(function () {
                    submitBtn.html(originalHtml);
                    submitBtn.prop('disabled', false);
                });
        });
})();
</script>
