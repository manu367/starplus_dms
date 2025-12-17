function stopHandler(event, obj) {
    var elem = obj.item[0];
    var sampleSortableGroup = $(".js-sortable-group").first().clone().html("");

    var idCount = 1;
    $(".menu_list .alert_box").each(function () {
        $(this).attr("id", "m_" + idCount);
        idCount++;
    });
}

$(".js-sortable-parent").sortable().disableSelection();

var sortableGroup = $(".js-sortable-group")
    .sortable({
        connectWith: ".js-drop-target",

        stop: stopHandler,
    })
    .disableSelection();

function myFunction_dashboard() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("sortable1");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("div")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

function myFunction_dashboard2() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput2");
    filter = input.value.toUpperCase();
    ul = document.getElementById("sortable2");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("div")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}