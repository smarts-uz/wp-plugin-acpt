
window.onload = function () {

    /**
     * ===================================================================
     * HELPERS
     * ===================================================================
     */

    function debounce(func, timeout = 300){
        let timer;

        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }

    function escapeIdSelector(selector) {
        let escaped = selector.replaceAll("[", "\\[");
        escaped = escaped.replaceAll("]", "\\]");

        return escaped;
    }

    /**
     *
     * @param action
     * @param data
     * @return {Promise<Response>}
     */
    const makeAjaxCall = (action, data) => {
        let url = document.location.origin+document.globals.ajax_url;

        const _data = {
            "action": action,
            "data": JSON.stringify(data)
        };

        return fetch(url, {
            method: 'POST',
            body: (new URLSearchParams(_data)).toString(),
            headers: { 'Content-type': 'application/x-www-form-urlencoded' }
        })
    };

    // Phone fields
    const handlePhoneFields = () => {
        const phoneFields = document.getElementsByClassName("acpt-phone");

        if(phoneFields.length > 0){
            for (let i = 0; i < phoneFields.length; i++) {
                const phoneElement = phoneFields.item(i);
                const country = document.getElementById(`${phoneElement.id}_country`);
                const dialCode = document.getElementById(`${phoneElement.id}_dial`);
                const utilsPath = document.getElementById(`${phoneElement.id}_utils`);

                const iti = intlTelInput(phoneElement, {
                    initialCountry: country.value,
                    separateDialCode: true,
                    geoIpLookup: callback => {
                        fetch("https://ipapi.co/json")
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback("us"));
                    },
                    loadUtils: () => import(utilsPath.value),
                });

                // on change country
                phoneElement.addEventListener("countrychange", function(e) {
                    const countryData = iti.getSelectedCountryData();

                    if(countryData.iso2 && countryData.dialCode){
                        country.value = countryData.iso2;
                        dialCode.value = countryData.dialCode;
                    }
                });
            }
        }
    };

    handlePhoneFields();

    // Character counter
    const handleTextareaCharacterCounter = () => {
        const textAreas = document.getElementsByClassName("acpt-textarea");

        if(textAreas.length > 0){
            for (let i = 0; i < textAreas.length; i++) {
                const textArea = textAreas.item(i);
                const textareaCounter = textArea.nextElementSibling;

                textArea.addEventListener("keyup", function (e) {

                    const value = e.target.value.length;
                    const count = textareaCounter.querySelector(".count");
                    const max = parseInt(textareaCounter.dataset.max);
                    const min = parseInt(textareaCounter.dataset.min);

                    if(value < min || value >= max){
                        count.classList.remove("warning");
                        count.classList.add('danger');
                    } else if(value >= max-5){
                        count.classList.remove("danger");
                        count.classList.add('warning');
                    } else {
                        count.classList.remove("warning");
                        count.classList.remove("danger");
                    }

                    count.textContent = value;
                });
            }
        }
    };

    handleTextareaCharacterCounter();

    // File fields
    const handleFileFieldsDefaultValue = () => {

        const deleteLinks = document.getElementsByClassName("acpt-delete-file");
        const uploadFields = document.getElementsByClassName("acpt-file");

        if(deleteLinks.length > 0){
            for (let i = 0; i < deleteLinks.length; i++) {
                const link = deleteLinks.item(i);
                const target = link.dataset.target;
                const value = link.dataset.value;

                link.addEventListener("click", function (e) {
                    e.preventDefault();

                    const targetEl = document.getElementById(target);
                    const targetValues = targetEl.value.split(",");
                    const newTargetValues = targetValues.filter(v => v !== value);

                    targetEl.value = newTargetValues.join(",");
                    link.parentElement.remove();
                });
            }
        }

        if(uploadFields.length > 0){
            for (let i = 0; i < uploadFields.length; i++) {
                const field = uploadFields.item(i);
                const target = field.dataset.targetId;

                field.addEventListener("change", function (e) {
                    document.getElementById(target).value = '';
                });
            }
        }
    };

    handleFileFieldsDefaultValue();

    // Color picker
    const handleColorPicker = () => {
        const colorPickerInputs = document.getElementsByClassName("acpt-color-picker");

        if(colorPickerInputs.length > 0) {
            for (let i = 0; i < colorPickerInputs.length; i++) {
                const el = colorPickerInputs.item(i);
                const input = el.querySelector("input");
                const label = el.querySelector(".color_val");

                if(input){
                    input.addEventListener("change", function(e){
                        label.innerHTML = e.target.value;
                    });
                }
            }
        }
    };

    handleColorPicker();

    const handleChoices = () => {
        const choicesInputs = document.getElementsByClassName("acpt-select2");

        if(typeof Choices === 'function' && choicesInputs.length > 0) {
            for (let i = 0; i < choicesInputs.length; i++) {
                const el = choicesInputs.item(i);

                if(el){
                    const choices = new Choices(el, {
                        classNames: {
                            containerInner: ['acpt-form-control'],
                            listItems: ['choices__list', 'acpt-choices__list--multiple'],
                            listSingle: ['choices__list', 'acpt-choices__list--single'],
                        }
                    });
                }
            }
        }
    };

    handleChoices();

    // Icon picker
    const handleIconPicker = () => {
        if(typeof IconPicker === 'function'){

            const iconPickerInputs = document.getElementsByClassName("acpt-iconpicker");

            if(iconPickerInputs.length > 0) {
                for (let i = 0; i < iconPickerInputs.length; i++) {

                    const target = iconPickerInputs.item(i).dataset.target;
                    const iconPickerInput = new IconPicker(iconPickerInputs.item(i), {
                        theme: 'bootstrap-5',
                        iconSource: [
                            'Iconoir',
                            'FontAwesome Solid 6',
                        ],
                        closeOnSelect: true
                    });

                    const iconElementInput = document.getElementById(target+"_target");
                    const iconElementValue = document.getElementById(target+"_svg");

                    iconPickerInput.on('select', (icon) => {

                        if (iconElementInput.innerHTML !== '') {
                            iconElementInput.innerHTML = '';
                        }

                        iconElementInput.className = `acpt-selected-icon ${icon.name}`;
                        iconElementInput.innerHTML = icon.svg;
                        iconElementValue.value = icon.svg;
                    });
                }
            }
        }
    };

    handleIconPicker();

    // CodeMirror
    const handleCodeMirror = () => {
        if(typeof CodeMirror === 'function'){
            const codeMirrors = document.getElementsByClassName("acpt-codemirror");

            // check if CodeMirror is already instantiated
            if(codeMirrors.length > 0){
                for (let i = 0; i < codeMirrors.length; i++) {

                    const el = codeMirrors.item(i);

                    if(!el.nextElementSibling.classList.contains("CodeMirror")){
                        CodeMirror.fromTextArea(el, {
                            indentUnit: 2,
                            tabSize: 2,
                            mode: 'htmlmixed',
                            lineNumbers: true
                        })
                    }
                }
            }
        }
    };

    handleCodeMirror();

    // Barcode
    const handleBarcodeGenerator = () => {
        if(typeof JsBarcode === "function"){
            const barCodes = document.getElementsByClassName("acpt-barcode-wrapper");

            if(barCodes.length > 0){
                for (let i = 0; i < barCodes.length; i++) {
                    const el = barCodes.item(i);

                    const value = el.querySelector(".value");
                    const format = el.querySelector(".format");
                    const color = el.querySelector(".color");
                    const bgColor = el.querySelector(".bgColor");
                    const clearButton = el.querySelector(".clear-barcode");
                    const barcodeSvg = el.querySelector(".acpt-barcode-svg");
                    const barcodeErrors = el.querySelector(".acpt-barcode-errors");
                    const barcodeSvgId = barcodeSvg.id;
                    const barcodeSvgElement = document.getElementById(`acpt-barcode-${barcodeSvgId}`);
                    const barcodeValueInput =  document.getElementById(`barcode_value_${barcodeSvgId.replace("acpt-barcode-", "")}`);

                    /**
                     * Clear the UI
                     */
                    const clearUI = () => {
                        barcodeSvg.innerHTML = `<svg class="acpt-barcode" id="${barcodeSvgId}"></svg>`;
                    };

                    const clearErrors = () => {
                        barcodeSvgElement.classList.remove("has-errors");
                        barcodeErrors.innerHTML = ``;
                    };

                    const addError = (err) => {

                        console.log(
                            barcodeSvgElement
                        );

                        barcodeSvgElement.classList.add("has-errors");
                        barcodeErrors.innerHTML = err;
                    };

                    /**
                     * Clear the form
                     */
                    const clearForm = () => {
                        clearUI();
                        value.value = '';
                        format.value = 'code128';
                        color.value = '#000000';
                        bgColor.value = '#ffffff';
                        barcodeValueInput.value = '';
                    };

                    /**
                     *
                     * @param text
                     * @return {*}
                     */
                    function escapeHtml(text) {
                        var map = {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;'
                        };

                        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
                    }

                    /**
                     * Generate barcode and populate the form
                     */
                    const generateBarcode = () => {
                        try {
                            clearErrors();

                            console.log(
                                barcodeSvgId,
                                value.value
                            );

                            JsBarcode(`#${barcodeSvgId}`, value.value, {
                                format: format.value ? format.value : "code128",
                                background: bgColor.value ? bgColor.value : "#ffffff",
                                lineColor: color.value ? color.value : "#000000",
                            });

                            const barcodeValue = {
                                svg: escapeHtml(barcodeSvgElement.outerHTML),
                                format: format.value ? format.value : "code128",
                                bgColor: bgColor.value ? bgColor.value : "#ffffff",
                                color: color.value ? color.value : "#000000",
                            };

                            barcodeValueInput.value = JSON.stringify(barcodeValue);
                        } catch (e) {
                            console.log(e);
                            clearForm();
                            addError(`The value "${value.value}" is not valid for ${format.value} format`);
                        }
                    };

                    // clear the form
                    clearButton.addEventListener("click", function(e){
                        e.preventDefault();
                        clearForm();
                    });

                    value.addEventListener("keyup", debounce((e) => {
                        generateBarcode();
                    }, 1000));

                    format.addEventListener("change", function(e){
                        generateBarcode();
                    });

                    color.addEventListener("change", debounce((e) => {
                        generateBarcode();
                    }, 1000));

                    bgColor.addEventListener("change", debounce((e) => {
                        generateBarcode();
                    }, 1000));
                }
            }
        }
    };

    handleBarcodeGenerator();

    // QR Code
    const handleQRCodeGenerator = () => {
        if(typeof QRCode === "function"){
            const qrCodes = document.getElementsByClassName("acpt-qr-code-wrapper");

            if(qrCodes.length > 0){
                for (let i = 0; i < qrCodes.length; i++) {
                    const el = qrCodes.item(i);
                    const url = el.querySelector(".url");
                    const resolution = el.querySelector(".resolution");
                    const colorDark = el.querySelector(".color-dark");
                    const colorLight = el.querySelector(".color-light");
                    const clearButton = el.querySelector(".clear-qr-code");
                    const QRCodeImage = el.querySelector(".acpt-qr-code");
                    const QRCodeImageId = QRCodeImage.id;
                    const QRCodeValueInput = document.getElementById(`qr_code_value_${QRCodeImageId.replace("acpt-qr-code-", "")}`);

                    /**
                     * Clear the UI
                     */
                    const clearUI = () => {
                        QRCodeImage.innerHTML = "";
                    };

                    /**
                     * Clear the form
                     */
                    const clearForm = () => {
                        clearUI();
                        url.value = '';
                        resolution.value = 100;
                        colorDark.value = '#000000';
                        colorLight.value = '#ffffff';
                        QRCodeValueInput.value = '';
                    };

                    /**
                     *
                     * @return {boolean}
                     */
                    const isURLValid = (urlString) => {
                        try {
                            return Boolean(new URL(urlString));
                        }
                        catch(e){
                            return false;
                        }
                    };

                    /**
                     * Generate QR Code and populate the form
                     */
                    const generateQRCode = () => {
                        if (!isURLValid(url.value)) {
                            clearForm();
                            return;
                        }

                        clearUI();
                        const element = document.getElementById(QRCodeImageId);

                        const qrCode = new QRCode(element, {
                            text: url.value,
                            width: resolution.value,
                            height: resolution.value,
                            colorDark : colorDark.value ? colorDark.value :"#000000",
                            colorLight : colorLight.value ? colorLight.value : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });

                        const QRCodeImageSrc = element.children[0].toDataURL("image/png");

                        const QRCodeValue = {
                            img: QRCodeImageSrc,
                            resolution: resolution.value,
                            colorDark : colorDark.value ? colorDark.value :"#000000",
                            colorLight : colorLight.value ? colorLight.value : "#ffffff"
                        };

                        if(QRCodeValueInput){
                            QRCodeValueInput.value = JSON.stringify(QRCodeValue);
                        }
                    };

                    // clear the form
                    clearButton.addEventListener("click", function(e){
                        e.preventDefault();
                        clearForm();
                    });

                    url.addEventListener("keyup", debounce((e) => {
                        generateQRCode();
                    }, 1000));

                    resolution.addEventListener("change", function(e){
                        generateQRCode();
                    });

                    colorDark.addEventListener("change", debounce((e) => {
                        generateQRCode();
                    }, 1000));

                    colorLight.addEventListener("change", debounce((e) => {
                        generateQRCode();
                    }, 1000));
                }
            }
        }
    };

    handleQRCodeGenerator();

    // Quill
    const handleQuill = () => {
        if(typeof Quill === 'function'){
            const quills = document.getElementsByClassName("acpt-quill");

            if(quills.length > 0){
                for (let i = 0; i < quills.length; i++) {

                    const el = quills.item(i);
                    const input  = document.getElementById(`${el.id}_hidden`);
                    const form = input.closest("form");
                    const button = form.querySelector('[type="submit"]');
                    const textareaCounter = el.nextElementSibling;
                    const errors = textareaCounter.nextElementSibling;
                    const max = parseInt(textareaCounter.dataset.max);
                    const min = parseInt(textareaCounter.dataset.min);
                    const rows = parseInt(el.dataset.rows);
                    const cols = parseInt(el.dataset.cols);

                    // check if Quill is already instantiated
                    if(!el.previousElementSibling.classList.contains("ql-toolbar")){

                        const addMinLengthError = () => {

                            const error = `Min length ${min}`;

                            input.setCustomValidity(error);
                            input.reportValidity();
                            el.classList.add("invalid");

                            if(errors){
                                errors.innerHTML = `<li>${error}</li>`;
                            }

                            if(button){
                                button.disabled = true;
                            }
                        };

                        const removeMinLengthError = () => {
                            input.setCustomValidity("");
                            input.reportValidity();
                            el.classList.remove("invalid");

                            if(errors){
                                errors.innerHTML= '';
                            }

                            if(button){
                                button.disabled = false;
                            }
                        };

                        const quill = new Quill(`#${escapeIdSelector(el.id)}`, {
                            modules: {
                                toolbar: [
                                    ['bold', 'italic'],
                                    ['link', 'blockquote', 'code-block', 'image'],
                                    [
                                        { list: 'ordered' },
                                        { list: 'bullet' }
                                    ],
                                ],
                            },
                            theme: 'snow'
                        });

                        // set editor height
                        const element = document.getElementById(`${escapeIdSelector(el.id)}`);

                        if(element){
                            const qlEditor = element.querySelector(".ql-editor");
                            qlEditor.style.height = `${(rows * 25)}px`;
                        }

                        // min length error handling
                        if(button){
                            button.addEventListener("click", function (e) {
                                const val   = quill.getLength()-1;

                                if (val < min) {
                                    e.preventDefault();
                                    addMinLengthError();
                                }
                            });
                        }

                        // untouched quill elements
                        form.addEventListener("submit", function (e) {

                            const val   = quill.getLength()-1;

                            if (val < min) {
                                e.preventDefault();
                                addMinLengthError();
                            }
                        });

                        // update editor
                        quill.on('text-change', function(delta, oldDelta, source) {

                            const text  = quill.getText();
                            const val   = quill.getLength()-1;
                            const value = quill.container.firstChild.innerHTML;
                            const count = textareaCounter.querySelector(".count");

                            if(input){

                                // MIN
                                if (val < min) {
                                    addMinLengthError();
                                } else {
                                    removeMinLengthError();
                                }

                                // MAX
                                if (val >= max) {
                                    quill.history.undo();
                                }

                                // Char counter CSS classes
                                if(count){
                                    if(val < min || val >= max){
                                        count.classList.remove("warning");
                                        count.classList.add('danger');
                                    } else if(val >= (max-5)){
                                        count.classList.remove("danger");
                                        count.classList.add('warning');
                                    } else {
                                        count.classList.remove("warning");
                                        count.classList.remove("danger");
                                    }

                                    count.textContent = val;
                                }

                                input.value = value;
                            }
                        });
                    }
                }
            }
        }
    };

    handleQuill();

    // List field
    const handleListFields = () => {

        // Add elements to the list
        const addElementsLink = document.getElementsByClassName("list-add-element");

        if(addElementsLink.length > 0){
            for (let i = 0; i < addElementsLink.length; i++) {
                const link = addElementsLink.item(i);

                link.addEventListener("click", function (e) {
                    e.preventDefault();

                    const $targetId = link.dataset.targetId;
                    const $parentName = link.dataset.parentName;
                    const $listWrapper = document.getElementById($targetId);
                    const $nextId = $listWrapper.children.length;

                    const newElement = document.createElement('div');
                    newElement.classList.add("actp-form-inline");
                    newElement.id = $parentName+"_"+$nextId;
                    newElement.insertAdjacentHTML('beforeend', `
                        <input
                                id='${$parentName}_${$nextId}' 
                                name='${$parentName}[]' 
                                type='text'
                                class="actp-form-control"
                            />
                            <a 
                                class='list-remove-element' 
                                data-target-id='${$parentName}_${$nextId}' 
                                href='#'
                                title='Remove element'
                            >
                                <svg xmlns='http://www.w3.org/2000/svg' 
                                    width='24' 
                                    height='24' 
                                    viewBox='0 0 24 24' 
                                    style='fill: rgba(0, 0, 0, 1);transform: ;msFilter:;'
                                >
                                    <path d='M7 11h10v2H7z'></path>
                                    <path d='M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z'></path>
                                </svg>
                            </a>
                    `);

                    $listWrapper.append(newElement);

                    handleDeleteElements();
                });
            }
        }

        // Remove list element
        const handleDeleteElements = () => {

            const removeListLinks = document.getElementsByClassName("list-remove-element");

            if(removeListLinks.length > 0){
                for (let i = 0; i < removeListLinks.length; i++) {
                    const link = removeListLinks.item(i);

                    link.addEventListener("click", function (e) {
                        e.preventDefault();

                        const $targetId = link.dataset.targetId;
                        const $target = document.getElementById($targetId);

                        if($target){
                            $target.remove();
                        }
                    });
                }
            }
        };

        handleDeleteElements();
    };

    handleListFields();

    // Repeater field
    const handleRepeaterFields = () => {

        // Add elements to the list
        const addElementsLink = document.getElementsByClassName("add-grouped-element");

        if(addElementsLink.length > 0){
            for (let i = 0; i < addElementsLink.length; i++) {
                const link = addElementsLink.item(i);

                link.addEventListener("click", function (e) {
                    e.preventDefault();

                    const id = link.dataset.groupId;
                    const layout = link.dataset.layout;
                    const mediaType = link.dataset.mediaType;
                    const parentIndex = link.dataset.parentIndex;
                    const parentName = link.dataset.parentName;
                    const formId = link.dataset.formId;

                    const noRecordsMessageDiv = document.querySelectorAll('[data-message-id="'+id+'"]');
                    const list = document.getElementById(id);

                    const data = {
                        "id": id,
                        "mediaType": mediaType,
                        "layout": layout,
                        "index": findIndex(id, layout),
                        "parentName": parentName,
                        "parentIndex": parentIndex,
                        "frontEnd": true,

                        "formId": formId
                    };

                    makeAjaxCall("generateGroupedFieldsAction", data)
                        .then(response => {
                            return response.text();
                        })
                        .then(response => {
                            const fields = JSON.parse(response);

                            if(fields.fields){

                                list.insertAdjacentHTML('beforeend', fields.fields);
                                handleDeleteNestedElements();
                                handleFileFieldsDefaultValue();
                                handleTextareaCharacterCounter();
                                handlePhoneFields();
                                handleIconPicker();
                                handleCodeMirror();
                                handleQuill();
                                handleBarcodeGenerator();
                                handleQRCodeGenerator();
                                handleColorPicker();
                                handleChoices();

                                // dispatch custom event
                                const evt = new Event("acpt_grouped_element_added");
                                document.dispatchEvent(evt);

                                if(noRecordsMessageDiv){
                                    for (let i = 0; i < noRecordsMessageDiv.length; i++) {
                                        const div = noRecordsMessageDiv.item(i);
                                        div.remove();
                                    }
                                }
                            }

                        })
                        .catch(e => {
                            console.error(e);
                        });
                });
            }
        }

        // calculate element index
        const findIndex = (id, layout) => {
            const list = document.getElementById(id);
            let index = 0;

            if(!list){
                return index;
            }

            if(layout === 'table'){
                index = list.querySelectorAll("tr.sortable-li").length;
            } else {
                index = list.querySelectorAll("li").length;
            }

            return index;
        };

        // Remove repeater element
        const handleDeleteNestedElements = () => {

            const removeListLinks = document.getElementsByClassName("remove-grouped-element");

            if(removeListLinks.length > 0){
                for (let i = 0; i < removeListLinks.length; i++) {
                    const link = removeListLinks.item(i);

                    link.addEventListener("click", function (e) {
                        e.preventDefault();

                        const parentId = link.dataset.parentId;
                        const id = link.dataset.targetId;
                        const layout = link.dataset.layout;
                        const element = link.dataset.element;
                        const elements = link.dataset.elements;
                        const $target = document.getElementById(id);
                        const parentList = $target.parentNode;
                        const parentListId = parentList.id;
                        const minBlocks = parentList.dataset.minBlocks;
                        const maxBlocks = parentList.dataset.maxBlocks;
                        const fieldsCount = layout === 'table' ? parentList.querySelectorAll("tr").length : parentList.querySelectorAll("li").length;
                        const addButton = document.querySelector(`.add-grouped-element[data-group-id="${parentId}"]`);

                        const newBlocksAllowed = () => {
                            if(typeof maxBlocks === 'undefined'){
                                return true;
                            }

                            return fieldsCount < maxBlocks;
                        };

                        const checkButton = () => {
                            if(!newBlocksAllowed()){
                                addButton.setAttribute('disabled', 'disabled');
                            } else {
                                addButton.removeAttribute('disabled')
                            }
                        };

                        $target.remove();
                        checkButton();

                        let parentListElementCount;
                        if(layout === 'table'){
                            parentListElementCount = (parentList.querySelectorAll('tr').length - 1);
                        } else {
                            parentListElementCount = parentList.querySelectorAll('li').length;
                        }

                        if(parentListElementCount === 0){
                            const warningMessage = `No fields saved, generate the first one clicking on "Add ${element}" button`;
                            const warningElement = `<p data-message-id="${parentId}" class="update-nag notice notice-warning inline no-records">${warningMessage}</p>`;
                            parentList.innerHTML = warningElement;
                        }

                        const evt = new Event("acpt_grouped_element_removed");
                        document.dispatchEvent(evt);
                    });
                }
            }
        };

        handleDeleteNestedElements();
    };

    handleRepeaterFields();
};
