/**
 * Created by moridrin on 4-6-17.
 */

function mp_ssv_add_ticket(fieldID, fieldTitle, valueStart, valueEnd) {
    var container = document.getElementById("tickets-placeholder");

    var ticketDiv = document.createElement("div");
    ticketDiv.setAttribute("id", fieldID);

    var button = document.createElement("button");
    button.setAttribute("id", "ticket_header_" + fieldID);
    button.setAttribute("type", "button");
    button.setAttribute("class", "ssv-accordion");
    button.innerHTML = fieldTitle;
    button.setAttribute("onclick", "mp_ssv_set_accordion_click_event()");
    ticketDiv.appendChild(button);

    var panelDiv = document.createElement("div");
    panelDiv.setAttribute("class", "panel");

    var ticketFieldsDiv = document.createElement("div");
    ticketFieldsDiv.appendChild(getEventFieldTitle(fieldID, fieldTitle));
    ticketFieldsDiv.appendChild(getStartTicketDate(fieldID, valueStart));
    ticketFieldsDiv.appendChild(getEndTicketDate(fieldID, valueEnd));

    var customFieldsDiv = document.createElement("div");

    var customFieldsPlaceholder = document.createElement("table");
    customFieldsPlaceholder.setAttribute("id", "custom-ticket-fields-" + fieldID);
    customFieldsPlaceholder.setAttribute("class", "sortable");
    customFieldsDiv.appendChild(customFieldsPlaceholder);

    var customFieldsButton = document.createElement("button");
    customFieldsButton.setAttribute("type", "button");
    customFieldsButton.innerHTML = "Add Custom Field";
    customFieldsButton.setAttribute("onclick", "mp_ssv_add_new_custom_field_to_container('custom-ticket-fields-" + fieldID + "')");
    customFieldsDiv.appendChild(customFieldsButton);

    panelDiv.appendChild(ticketFieldsDiv);
    panelDiv.appendChild(customFieldsDiv);
    ticketDiv.appendChild(panelDiv);
    container.appendChild(ticketDiv);
    ssv_init_date_time_pickers();
    button.click();
}

//noinspection JSUnusedGlobalSymbols
function mp_ssv_set_accordion_click_event() {
    event.preventDefault();
    event.srcElement.classList.toggle("active");
    console.log(event.srcElement.classList.contains('active'));
    var panel = event.srcElement.nextElementSibling;
    if (event.srcElement.classList.contains('active')) {
        panel.setAttribute("style", "display: block;")
    } else {
        panel.setAttribute("style", "display: none;")
    }
    // if (panel.style.maxHeight) {
    //     panel.style.maxHeight = null;
    // } else {
    //     panel.style.maxHeight = panel.scrollHeight + "px";
    // }
}

//noinspection JSUnusedGlobalSymbols
function mp_ssv_update_ticket_title(id) {
    var header = document.getElementById("ticket_header_" + id);
    header.innerHTML = event.srcElement.value;
}

function mp_ssv_update_event_start_date() {
    //document.getElementById("event_start_date").value;
}

function mp_ssv_update_event_end_date() {
    //document.getElementById("event_end_date").value;
}

function getEventFieldTitle(fieldID, value) {
    var fieldTitle = document.createElement("input");
    fieldTitle.setAttribute("id", fieldID + "_title");
    fieldTitle.setAttribute("name", "custom_field_" + fieldID + "_title");
    fieldTitle.setAttribute("style", "width: 100%;");
    fieldTitle.setAttribute("oninput", "mp_ssv_update_ticket_title(" + fieldID + ")");
    if (value) {
        fieldTitle.setAttribute("value", value);
    }
    var fieldTitleLabel = document.createElement("label");
    fieldTitleLabel.setAttribute("style", "white-space: nowrap;");
    fieldTitleLabel.setAttribute("for", fieldID + "_field_title");
    fieldTitleLabel.innerHTML = "Field Title";
    var fieldTitleTD = document.createElement("td");
    fieldTitleTD.setAttribute("id", fieldID + "_field_title_td");
    fieldTitleTD.appendChild(fieldTitleLabel);
    fieldTitleTD.appendChild(getBR());
    fieldTitleTD.appendChild(fieldTitle);
    return fieldTitleTD;
}

function getStartTicketDate(fieldID, value) {
    var startDate = getDateFromDateTime(document.getElementById("event_start_date").value);
    var endDate = getDateFromDateTime(document.getElementById("event_end_date").value);
    var dateField = document.createElement("input");
    dateField.setAttribute("id", fieldID + "_date_start");
    dateField.setAttribute("name", "custom_field_" + fieldID + "_date_start");
    dateField.setAttribute("class", "datetimepicker");
    dateField.setAttribute("minDate", startDate);
    dateField.setAttribute("maxDate", endDate);
    if (value) {
        dateField.setAttribute("value", value);
    }
    var dateTD = document.createElement("td");
    dateTD.setAttribute("id", fieldID + "_date_td");
    var dateLabel = document.createElement("label");
    dateLabel.setAttribute("style", "white-space: nowrap;");
    dateLabel.setAttribute("for", fieldID + "_date");
    dateLabel.innerHTML = "Start";
    dateTD.appendChild(dateLabel);
    dateTD.appendChild(getBR());
    dateTD.appendChild(dateField);
    return dateTD;
}

function getEndTicketDate(fieldID, value) {
    var startDate = getDateFromDateTime(document.getElementById("event_start_date").value);
    var endDate = getDateFromDateTime(document.getElementById("event_end_date").value);
    var dateField = document.createElement("input");
    dateField.setAttribute("id", fieldID + "_date_end");
    dateField.setAttribute("name", "custom_field_" + fieldID + "_date_end");
    dateField.setAttribute("class", "datetimepicker");
    dateField.setAttribute("minDate", startDate);
    dateField.setAttribute("maxDate", endDate);
    if (value) {
        dateField.setAttribute("value", value);
    }
    var dateTD = document.createElement("td");
    dateTD.setAttribute("id", fieldID + "_date_td");
    var dateLabel = document.createElement("label");
    dateLabel.setAttribute("style", "white-space: nowrap;");
    dateLabel.setAttribute("for", fieldID + "_date");
    dateLabel.innerHTML = "End";
    dateTD.appendChild(dateLabel);
    dateTD.appendChild(getBR());
    dateTD.appendChild(dateField);
    return dateTD;
}

function getDateFromDateTime(dateTime) {
    var date = new Date(dateTime);
    var year = date.getFullYear();
    var month = date.getMonth() < 10 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1);
    var day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
    return year + "-" + month + "-" + day;
}

// function getTimeFromDateTime(dateTime) {
//     var date = new Date(dateTime);
//     var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
//     var minutes = date.getMinutes() === 0 ? "00" : (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes());
//     return hours + ":" + minutes;
// }
