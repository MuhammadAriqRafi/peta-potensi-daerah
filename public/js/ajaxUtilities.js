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
    <a href="#" class="btn btn-sm btn-outline-warning" onclick="update('${id}')">Edit</a>
    <a href="#" class="btn btn-sm btn-outline-danger" onclick="destroy('${id}')">Delete</a>`
}

const reload = () => {
    table.ajax.reload(null, false);
}

const resetInvalidClass = (element) => {
    $(element).find('.text-error').text('');
    if($(element).find('.textarea-error').length > 0) $(element).find('.textarea-error').removeClass('textarea-error');
    if($(element).find('.input-error').length > 0) $(element).find('.input-error').removeClass('input-error');
}

const createDataTable = (id, url) => {
    $(`#${id}`).DataTable({
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 99999],
            [10, 25, 50, 'All'],
        ],
        ajax: url,
        serverSide: true,
        deferRender: true,
        dom: '<"overflow-x-hidden"<"flex flex-wrap gap-4 justify-center sm:justify-between items-center mb-5"lf><t><"flex justify-between items-center mt-5"ip>>',
    });
}