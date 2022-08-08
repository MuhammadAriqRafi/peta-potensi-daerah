const capitalizeFirstLetter = (word) => {
    return word.charAt(0).toUpperCase() + word.slice(1);
}

const textInputComponent = (title, name, type = 'text', options = '') => {
    const capitalizedTitleFirstLetter = capitalizeFirstLetter(title);
    const lowerCasedName = name.toLowerCase();

    return `
        <div class="form-control mb-4" onclick="resetInvalidClass(this)">
            <span class="label-text font-bold">${capitalizedTitleFirstLetter}</span>
            <input type="${type}" ${options} class="input input-bordered w-full max-w-xs my-2" name="${lowerCasedName}" />
            <div id="error-${lowerCasedName}" class="badge badge-error hidden"></div>
        </div>
    `;
}

const dateInputComponent = (title, name) => {
    const capitalizedTitleFirstLetter = capitalizeFirstLetter(title);
    const lowerCasedName = name.toLowerCase();
    const date = new Date();
    const year = date.getFullYear();
    const month = `${date.getMonth()}`.length < 2 ? `0${date.getMonth()}` : date.getMonth();
    const day = `${date.getDate()}`.length < 2 ? `0${date.getDate()}` : date.getDate();
    const maxDate = `${year}-12-31`;
    const currentDate = `${year}-${month}-${day}`;

    return `
        <div class="form-control mb-4" onclick="resetInvalidClass(this)">
            <span class="label-text font-bold">${capitalizedTitleFirstLetter}</span>
            <input type="date" class="input input-bordered w-full max-w-xs my-2" name="${lowerCasedName}" min="1900-01-01" max="${maxDate}" value="${currentDate}" />
            <div id="error-${lowerCasedName}" class="badge badge-error hidden"></div>
        </div>
    `;
}

const textareaComponent = (title, name, summernote = false) => {
    const capitalizedTitleFirstLetter = capitalizeFirstLetter(title);
    const lowerCasedName = name.toLowerCase();
    let attribute = `class="textarea textarea-bordered"`;

    if (summernote) {
        attribute = `id="summernote"`;
    }

    return `
        <div class="form-control mb-4" onclick="resetInvalidClass(this)">
            <span class="label-text font-bold mb-2">${capitalizedTitleFirstLetter}</span>
            <textarea ${attribute} name="${lowerCasedName}"></textarea>
            <div id="error-${lowerCasedName}" class="badge badge-error hidden mt-2"></div>
        </div>
    `;
}

const selectInputComponent = (title, name, options) => {
    const capitalizedTitleFirstLetter = capitalizeFirstLetter(title);
    const lowerCasedName = name.toLowerCase();
    let optionList = '';

    options.forEach(option => {
        optionList += `
            <div class="flex items-center gap-4">
                <input type="radio" name="${lowerCasedName}" class="radio" value="${option}" />
                <label for="${option}">${capitalizeFirstLetter(option)}</label>
            </div>
        `;
    });

    return `
        <div class="form-control mb-4" onclick="resetInvalidClass(this)">
            <span class="label-text font-bold">${capitalizedTitleFirstLetter}</span>
            <div class="flex items-center gap-4 my-2">
                ${optionList}
            </div>
            <div id="error-${lowerCasedName}" class="badge badge-error hidden"></div>
        </div>
    `;
}

const dropdownComponent = (title, name, options) => {
    const capitalizedTitleFirstLetter = capitalizeFirstLetter(title);
    const lowerCasedName = name.toLowerCase();
    let optionList = '';

    options.forEach(option => {
        if(Object.keys(option).length == 1) optionList += `<option value="${option[Object.keys(option)[0]]}">${option[Object.keys(option)[0]]}</option>`;
        else if (Object.keys(option).length == 2) optionList += `<option value="${option[Object.keys(option)[0]]}">${option[Object.keys(option)[1]]}</option>`
    });

    return `
        <div class="form-control mb-4" onclick="resetInvalidClass(this)">
            <span class="label-text font-bold mb-2">${capitalizedTitleFirstLetter}</span>
            <select name="${lowerCasedName}" class="select select-bordered w-full max-w-xs my-2">
                <option value="" hidden>-- Pilih ${capitalizedTitleFirstLetter} --</option>
                ${optionList}
            </select>
            <div id="error-${lowerCasedName}" class="badge badge-error hidden"></div>
        </div>
    `;
}

const fileInputComponent = (title, name) => {
    const capitalizedTitleFirstLetter = capitalizeFirstLetter(title);
    const lowerCasedName = name.toLowerCase();

    return `
        <div class="form-control mb-4" onclick="resetInvalidClass(this)">
            <span class="label-text font-bold">${capitalizedTitleFirstLetter}</span>
            <img src="#" height="100" class="img-thumbnail img-preview my-2">
            <input type="file" id="${lowerCasedName}" class="input input-bordered w-full max-w-xs my-2" name="${lowerCasedName}" onchange="previewImg()" accept="image/jpg, image/jpeg, image/png" />
            <div id="error-${lowerCasedName}" class="badge badge-error hidden"></div>
        </div>
    `;
}