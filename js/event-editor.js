// noinspection JSUnresolvedVariable
let nextTicketId = data.ticketsMaxId;
let ticketsManager = {
    addNew: function () {
        let ticketsTable = document.getElementById('the-list');
        let tr = document.createElement('tr');
        let properties = {
            'title': '',
            'dateTimeStart': document.getElementsByName('start').item(0).value,
            'dateTimeEnd': document.getElementsByName('end').item(0).value,
            'price': 0
        };
        tr.dataset.properties = JSON.stringify(properties);
        let fieldId = nextTicketId++;
        tr.setAttribute('id', fieldId + '_tr');
        let title = '';
        let dateTime = '';
        let price = 0;
        tr.innerHTML =
            '<td>' +
            '   <strong>' + title + '</strong>' +
            '   <div class="row-actions">' +
            '       <span class="inline-actions"><a href="javascript:void(0)" onclick="ticketsManager.inlineEdit(\'' + fieldId + '\')" class="editinline" aria-label="Quick edit “' + title + '” inline">Quick Edit</a> | </span>' +
            '       <span class="trash"><a href="javascript:void(0)" onclick="ticketsManager.deleteRow(\'<?= $ticket->t_id ?>\')" class="submitdelete" aria-label="Delete “' + title + '”">Delete</a></span>' +
            '   </div>' +
            '</td>' +
            '<td>' + dateTime + '</td>' +
            '<td>' + price + '</td>'
        ;
        tr.setAttribute('class', 'inactive');
        tr.setAttribute('draggable', 'draggable');
        generalFunctions.removeElement(document.getElementById('no-items'));
        ticketsTable.appendChild(tr);
        ticketsManager.inlineEdit(fieldId);
    },

    inlineEdit: function (fieldId) {
        let tr = document.getElementById(fieldId + '_tr');
        let properties = JSON.parse(tr.dataset.properties);
        tr.setAttribute('class', 'inline-edit-row inline-edit-row-base-field quick-edit-row quick-edit-row-base-field inline-edit-base-field inline-editor');
        let html =
            '<td colspan="5" class="colspanchange">' +
            '   <fieldset class="inline-edit-col-left" style="width: 50%;">' +
            '       <legend class="inline-edit-legend">Quick Edit</legend>' +
            '       <div class="inline-edit-col">'
        ;
        html += ticketsManager.getCustomizationFieldInput(fieldId, 'Title', 'title', 'text', properties.title);
        html += ticketsManager.getCustomizationFieldInput(fieldId, 'Price', 'price', 'number', properties.price);
        html += ticketsManager.getCustomizationFieldInput(fieldId, 'Form', 'form', 'select', properties.form, data.formTitles, data.formKeys);
        html +=
            '       </div>' +
            '   </fieldset>' +
            '   <fieldset class="inline-edit-col-right" style="width: 50%; margin-top: 32px;">' +
            '       <div class="inline-edit-col">'
        ;
        html += ticketsManager.getCustomizationFieldInput(fieldId, 'From', 'dateTimeStart', 'datetimepicker', properties.dateTimeStart);
        html += ticketsManager.getCustomizationFieldInput(fieldId, 'Till', 'dateTimeEnd', 'datetimepicker', properties.dateTimeEnd);
        html +=
            '      </div>' +
            '   </fieldset>' +
            '   <div class="submit inline-edit-save" style="float: none; padding: 10px 0;">' +
            '      <button type="button" class="button cancel alignleft" onclick="ticketsManager.cancelInlineEdit(\'' + fieldId + '\')">Cancel</button>' +
            '      <input type="hidden" id="_inline_edit" name="_inline_edit" value="' + fieldId + '">' +
            '      <button type="button" class="button button-primary save alignright" onclick="ticketsManager.saveInlineEdit(\'' + fieldId + '\')">Update</button>' +
            '      <br class="clear">' +
            '   </div>' +
            '</td>'
        ;
        tr.innerHTML = html;

        let dateTimePickers = $('.inline-edit-row .datetimepicker');
        dateTimePickers.each(function () {
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                inline: false,
                mask: '9999-19-39 29:59',
                format: 'Y-m-d H:i',
                step: 15,
                value: value,
                minDate: document.getElementsByName('start').item(0).value,
                maxDate: document.getElementsByName('end').item(0).value,
            });
        });
        tr.removeAttribute('draggable');

    },

    cancelInlineEdit: function (fieldId) {
        ticketsManager.updateTrForDisplay(fieldId);
    },

    saveInlineEdit: function (fieldId) {
        let tr = document.getElementById(fieldId + '_tr');
        let properties = {
            'title': document.getElementById(fieldId + '_title').value,
            'dateTimeStart': document.getElementById(fieldId + '_dateTimeStart').value,
            'dateTimeEnd': document.getElementById(fieldId + '_dateTimeEnd').value,
            'price': document.getElementById(fieldId + '_price').value
        };
        tr.dataset.properties = JSON.stringify(properties);
        ticketsManager.updateTrForDisplay(fieldId);
        event.preventDefault();
    },

    updateTrForDisplay: function (fieldId) {
        let tr = document.getElementById(fieldId + '_tr');
        let properties = JSON.parse(tr.dataset.properties);
        let title = properties['title'];
        let dateTimeStart = properties['dateTimeStart'];
        let dateTimeEnd = properties['dateTimeEnd'];
        let price = properties['price'];
        tr.innerHTML =
            '<td>' +
            '   <input type="hidden" name="ticketIds[]" value="' + fieldId + '">' +
            '   <input type="hidden" id="tmp" name="ticket_' + fieldId + '" value=\'' + JSON.stringify(properties) + '\'>' +
            '   <strong>' + title + '</strong>' +
            '   <div class="row-actions">' +
            '       <span class="inline-actions"><a href="javascript:void(0)" onclick="ticketsManager.inlineEdit(\'' + fieldId + '\')" class="editinline" aria-label="Quick edit “' + title + '” inline">Quick Edit</a> | </span>' +
            '       <span class="trash"><a href="javascript:void(0)" onclick="ticketsManager.deleteRow(\'<?= $ticket->t_id ?>\')" class="submitdelete" aria-label="Delete “' + title + '”">Delete</a></span>' +
            '   </div>' +
            '</td>' +
            '<td>' + dateTimeStart + ' - ' + dateTimeEnd + '</td>' +
            '<td>' + price + '</td>'
        ;
        tr.setAttribute('class', 'inactive');
        tr.setAttribute('draggable', 'draggable');
    },

    getCustomizationFieldInput: function (fieldId, title, name, type, value, options, optionValues) {
        let html =
            '<label>' +
            '   <span class="title">' + title + '</span>' +
            '   <span class="input-text-wrap">'
        ;
        if (type === 'textarea') {
            html += '<textarea id="' + fieldId + '_' + name + '" name="' + name + '">' + value + '</textarea>';
        } else if (type === 'number') {
            html += '<input type="number" id="' + fieldId + '_' + name + '" name="' + name + '" value="' + value + '" autocomplete="off" onkeydown="ticketsManager.onInlineEditKeyDown(\'' + fieldId + '\')" style="width: 100%;">';
        } else if (type === 'select') {
            if (typeof(optionValues) === 'undefined') {
                optionValues = options;
            }
            html += '<select id="' + fieldId + '_' + name + '" name="' + name + '" style="width: 100%;">';
            for (let i = 0; i < options.length; ++i) {
                html += '<option value="' + optionValues[i] + '">' + options[i] + '</option>';
            }
            html += '</select>';
        } else if (type === 'datetimepicker') {
            html += '<input type="text" id="' + fieldId + '_' + name + '" class="datetimepicker" name="' + name + '" value="' + value + '" autocomplete="off" onkeydown="ticketsManager.onInlineEditKeyDown(\'' + fieldId + '\')">';
        } else if (type === 'checkbox') {
            let checked = value === true || value === 'true' ? 'checked="checked"' : '';
            html += '<input type="hidden" name="' + name + '" value="false">';
            html += '<input type="checkbox" id="' + fieldId + '_' + name + '" name="' + name + '" value="true" ' + checked + '>';
        } else {
            html += '<input type="' + type + '" id="' + fieldId + '_' + name + '" name="' + name + '" value="' + value + '" autocomplete="off" onkeydown="ticketsManager.onInlineEditKeyDown(\'' + fieldId + '\')">';
        }
        html +=
            '   </span>' +
            '</label>'
        ;
        return html;
    },

    onInlineEditKeyDown: function (fieldId) {
        if (event.keyCode === 13) {
            ticketsManager.saveInlineEdit(fieldId);
            event.preventDefault();
            return false;
        }
    },

    deleteRow: function (fieldId) {
        let tr = document.getElementById(fieldId + '_tr');
        generalFunctions.removeElement(tr);
        event.preventDefault();
    }
};
