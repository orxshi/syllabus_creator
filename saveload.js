$(document).ready(function() {

    // ===== SAVE FUNCTION =====
    $("#save").click(function(e){
        e.preventDefault(); // prevent default action

        var jsonData = {};
        var formData = $("#myform").serializeArray();

        // Convert form data into JSON, handling multiple values
        $.each(formData, function() {
            if (jsonData[this.name]) {
                if (!Array.isArray(jsonData[this.name])) {
                    jsonData[this.name] = [jsonData[this.name]];
                }
                jsonData[this.name].push(this.value || '');
            } else {
                jsonData[this.name] = this.value || '';
            }
        });

        // AJAX to save JSON
        $.ajax({
            url: "encode.php",
            type: "POST",
            data: jsonData,
            success: function(data) {
                window.location.href = 'save.php';
            }
        });
    });

    // ===== LOAD FUNCTION =====
    $("#load").click(function(e){
        e.preventDefault();

        var fileDialog = $('<input type="file">');
        fileDialog.click();

        fileDialog.on("change", onFileSelected);
    });

    // ===== FILE READ & FORM POPULATION =====
    var onFileSelected = function(e){
        let file = $(this)[0].files[0];
        let reader = new FileReader();

        reader.readAsText(file);

        reader.onload = function() {
            let obj = reader.result;
            let data = JSON.parse(obj);

            for (var key in data) {

                // Handle checkboxes (arrays)
                if (Array.isArray(data[key])) {
                    data[key].forEach(function(val) {
                        $('input[name="'+key+'"][value="'+val+'"]').prop('checked', true);
                    });
                } else {
                    // Handle text, textarea, select
                    let el = $('#' + key);

                    if (el.is(':checkbox')) {
                        el.prop('checked', data[key] == el.val());
                    } else if (el.is('select')) {
                        el.val(data[key]);
                    } else {
                        el.val(data[key]);
                    }
                }
            }
        };
    };

});

// ===== RESET FUNCTION =====
function resetAll() {
    // Reset all text inputs
    $("input[type='text']").val('');

    // Reset all textareas
    $("textarea").val('');

    // Reset all checkboxes
    $("input[type='checkbox']").prop('checked', false);

    // Reset all selects
    $("select").prop('selectedIndex', 0);
}
