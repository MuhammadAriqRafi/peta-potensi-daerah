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
    $(element).find('.is-invalid').removeClass('is-invalid');
}