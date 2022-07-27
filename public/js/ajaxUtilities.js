// ! urlFormatter Function Deprecated
const urlFormatter = (url, id = null, context = null) => {
    if (context != null) {
        url = url.replace(':id', id);
        url = url.replace(':context', context);
        return url;
    } else if (id != null){
        return url = url.replace(':id', id);
    }

    return url;
}

const editDeleteBtn = (id = null) => {
    return `
    <a href="#" class="btn btn-sm btn-secondary" onclick="edit('${id}')">Edit</a>
    <a href="#" class="btn btn-sm btn-error" onclick="destroy('${id}')">Delete</a>`
}

const reload = (formId) => {
    $(`#${formId}`).DataTable().ajax.reload(null, false);
}

const resetInvalidClass = (element) => {
    const textareaError = $(element).find('.textarea-error');
    const inputError = $(element).find('.input-error');

    $(element).find('.badge-error').text('');
    $(element).find('.badge-error').addClass('hidden');

    if(textareaError.length > 0) textareaError.removeClass('textarea-error');
    if(inputError.length > 0) inputError.removeClass('input-error');
}

const createDataTable = (id, url, options) => {
    $(`#${id}`).DataTable({
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 99999],
            [10, 25, 50, 'All'],
        ],
        ajax: {
            url: url,
            beforeSend: function(){
                // ? Reset select option for Popup Active in popup management
                if($('select[name="id"]').length > 0) $('select[name="id"]').html('');
            }
        },
        serverSide: true,
        deferRender: true,
        dom: '<"overflow-x-hidden"<"flex flex-wrap gap-4 justify-center sm:justify-between items-center mb-5"lf><t><"flex justify-between items-center mt-5"ip>>',
        columns: options
    });
}

const closeModal = (modalId) => {
    $(`#${modalId}`).prop('checked', false);
}