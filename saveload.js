$(document).ready(function() {
    $("#save").click(function(e){
        var jsonData = {};

        var formData = $("#myform").serializeArray();

        $.each(formData, function() {
            if (jsonData[this.name]) {
                if (!jsonData[this.name].push) {
                    jsonData[this.name] = [jsonData[this.name]];
                }
                jsonData[this.name].push(this.value || '');
            } else {
                jsonData[this.name] = this.value || '';
            }
        });
        $.ajax(
        {
            url : "encode.php",
                type: "POST",
                data : jsonData,
                success: function(data)
                {
                    window.location.href = 'save.php';
                }
        });
    });
});

$(document).ready(function() {
    $("#load").click(function(e){
        var fileDialog = $('<input type="file">');
        fileDialog.click();
        fileDialog.on("change",onFileSelected);
    });
});

var onFileSelected = function(e){

    let fn = $(this)[0].files[0];

    let reader = new FileReader();

    reader.readAsText(fn);

    reader.onload = function() {
        let obj = reader.result;
        let data = JSON.parse(obj);
        for (var i in data) {
            $('#'+i).val(data[i]);
        }
    };
};

function resetAll()
{
    var elements = document.getElementsByTagName("input");
    for (var ii=0; ii < elements.length; ii++) {
        if (elements[ii].type == "text") {
            elements[ii].value = "";
        }
    }
    var elements = document.getElementsByTagName("textarea");
    for (var ii=0; ii < elements.length; ii++) {
        elements[ii].value = "";
    }
}
