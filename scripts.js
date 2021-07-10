function readSingleFile(e) {
    var file = e.target.files[0];
    if (!file) {
        return;
    }

    var reader = new FileReader();
    reader.onload = function(e) {
        var contents = e.target.result;
        displayContents(contents);
    };

    reader.readAsText(file);
}

function displayContents(contents) {
    var csv = contents;
    var data = d3.csvParse(csv);
    var nest = d3.nest()
        .key(function(row) {
            return row.stock_name;
        })
        .rollup(function(values) {
            return values;
        })
        .entries(data);

    if (nest[0].key == 'undefined') {
        clearOptions('stock_name');

        alert('Invalid CSV File Uploaded');
    } else {
        clearOptions('stock_name');

        nest.forEach(function(item, index) {
            var option = document.createElement("option");
            option.text = item.key;
            option.value = item.key;
            var select = document.getElementById("stock_name");
            select.appendChild(option);
        });
		
    }

	document.getElementById('csv_data').value = (JSON.stringify(nest));
 	
}

function clearOptions(id) {

    document.getElementById(id).innerText = '';

    var option = document.createElement("option");
    option.text = "Select Stock  Name"
    option.value = "";
    var select = document.getElementById(id);
    select.appendChild(option);

}

document.getElementById('file').addEventListener('change', readSingleFile, false);
document.getElementById('stock_name').addEventListener('change', readSingleFile, false);


$('#file, #stock_name').bind('change', function() {
    if(allFilled()) $('#submit').removeAttr('disabled');
    else $('#submit').attr('disabled', 'disabled');
});

function allFilled() {
    var filled = true;
    $('body input').each(function() {
        if($(this).val() == '') filled = false;
    });
    console.log(filled);
    return filled;
}

$(function() {

    date = new Date();
    nextDate = date.setDate(date.getDate() + 1);

    $("#start_date").datepicker({
        autoclose: true,
        todayHighlight: true
    }).datepicker('update', new Date());

    $("#end_date").datepicker({
        autoclose: true,
        todayHighlight: true
    }).datepicker('update', date);

});
