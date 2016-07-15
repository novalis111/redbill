/* Functions */
$.updateInvoice = function (clientId) {
    $.get("/asset/checklist/" + clientId, function (data) {
        $("#assetCheckList").html(data);
    });
    var invoiceId = $('input#invoice_id').attr('value') || 0;
    if (invoiceId != 0) {
        $.get("/invoice/checklist/" + invoiceId, function (data) {
            $("#invoiceEntries").html(data);
        });
        $.get('/invoice/has_entries/' + invoiceId, function (data) {
            if (data != 0) {
                $('select#client').attr('disabled', 'disabled');
            }
        });
    }
};

/* On DOM ready */
$(document).ready(function () {
    $(".datepicker").each(function () {
        $(this).datepicker();
        $(this).datepicker("option", "dateFormat", "yy-mm-dd");
        $(this).datepicker('setDate', $(this).attr('value'));
    });

    // Update Clients open assets + invoice entries on load + change
    $('select#client').change(function () {
        $.updateInvoice($(this).val())
    }).change();

    $('.toggle-panel').click(function () {
        $(this).parents('.panel').first().find('.panel-body').toggleClass('hidden');
    });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ajaxComplete(function () {
    $.collectSelectedRows = function (parent) {
        var rows = {};
        parent.find('input:checkbox:checked').each(function (idx) {
            var row = {};
            $(this).closest('tr').find('*[name]').each(function () {
                row[$(this).attr('name')] = $(this).val();
            });
            rows[idx] = row;
        });
        return rows;
    };
    // Remove selected invoice entries from invoice
    $('#btnRemoveEntriesFromInvoice').off('click').click(function () {
        var rows = $.collectSelectedRows($(this).parents('.form-group').first());
        var invoiceId = $(this).closest('form').find('input#invoice_id').val() || 0;
        if ($.isEmptyObject(rows)) {
            return;
        }
        $.post('/invoice/removeEntries', {
            invoice_id: invoiceId,
            entries: rows
        }).done(function () {
            $.updateInvoice($('select#client').val());
        }).fail(function (response) {
            if (response.status == 404) {
                alert('Please save invoice first');
            } else {
                alert('Unknown Error');
            }
        });
    });
    // Add selected assets to invoice as entries
    $('#btnAddAssetsToInvoice').off('click').click(function () {
        var rows = $.collectSelectedRows($(this).parents('.form-group').first());
        var invoiceId = $(this).closest('form').find('input#invoice_id').val() || 0;
        if ($.isEmptyObject(rows)) {
            return;
        }
        $.post('/invoice/addAssets', {
            'invoice_id': invoiceId,
            assets: rows
        }).done(function () {
            $.updateInvoice($('select#client').val());
        }).fail(function (response) {
            if (response.status == 404) {
                alert('Please save invoice first');
            } else {
                alert('Unknown Error');
            }
        });
    });
    // Revert checkbox selection in form-group
    $('.reverseSelection').off('click').click(function () {
        $(this).parents('.form-group').first().find('input:checkbox').each(function () {
            $(this).prop('checked', !$(this).prop('checked'));
        });
    });
});