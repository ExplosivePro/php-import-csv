// '.tbl-content' consumed little space for vertical scrollbar, scrollbar width depend on browser/os/platfrom. Here calculate the scollbar width .
$(window).on("load resize ", function() {
    var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
    $('.tbl-header').css({'padding-right':scrollWidth});
}).resize();

$(document).ready(function() {
    $("#frmCSVImport").on("submit", function () {
        
        $("#response").attr("class", "");
        $("#response").html("");
        var fileType = ".csv";
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
        if (!regex.test($("#file").val().toLowerCase())) {
                $("#response").addClass("error");
                $("#response").addClass("display-block");
            $("#response").html("Invalid File. Upload : <b>" + fileType + "</b> Files.");
            return false;
        }
        return true;
    });
});
function handleChange(event) {
    var fake_path = event.target.value;
    fake_path = fake_path ? fake_path.split("\\")[2]: "Select Csv";
    fake_path = fake_path.length > 13 ? fake_path.substr(0, 10) + "...": fake_path;
    $("#fileSelect")[0].innerText = fake_path;
}

$('.columns').fSelect();
$('.fs-label').text();
var value = [].map.call($('.fs-option.selected'),function(el){return el.dataset.value});