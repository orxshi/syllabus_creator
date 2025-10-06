$(document).ready(function() {

    // ===== SAVE FUNCTION =====
    $("#save").click(function(e){
        e.preventDefault(); // prevent default action

        var jsonData = {};
        var formData = $("#myform").serializeArray();

        // Convert form data into JSON, always keeping arrays for multiple checkboxes
        $.each(formData, function() {
            var name = this.name.replace("[]", ""); // remove brackets
            if (jsonData[name]) {
                if (!Array.isArray(jsonData[name])) {
                    jsonData[name] = [jsonData[name]];
                }
                jsonData[name].push(this.value || '');
            } else {
                // Always store as array if checkbox group
                if (this.name.endsWith("[]")) {
                    jsonData[name] = [this.value || ''];
                } else {
                    jsonData[name] = this.value || '';
                }
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
                let value = data[key];

                // Handle checkbox groups (eligdep, mode, etc.)
                if ($('input[name="'+key+'[]"]').length) {
                    $('input[name="'+key+'[]"]').prop('checked', false); // uncheck all first

                    if (Array.isArray(value)) {
                        value.forEach(function(val){
                            $('input[name="'+key+'[]"][value="'+val+'"]').prop('checked', true);
                        });
                    } else {
                        $('input[name="'+key+'[]"][value="'+value+'"]').prop('checked', true);
                    }
                }
                // Handle other input types
                else {
                    let el = $('#' + key);

                    if (el.is(':checkbox')) {
                        el.prop('checked', value == el.val());
                    } else if (el.is('select')) {
                        el.val(value);
                    } else {
                        el.val(value);
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
