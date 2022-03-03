$(document).ready(function () {
    window.setTimeout(function () {
        $(".alert")
            .fadeTo(1000, 0)
            .slideUp(1000, function () {
                $(this).remove();
            });
    }, 2000);
});
$(function () {
    // Multiple images preview with JavaScript
    var previewImages = function (input, imgPreviewPlaceholder) {
        if (input.files) {
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function (event) {
                    $($.parseHTML("<img>"))
                        .attr("src", event.target.result)
                        .appendTo(imgPreviewPlaceholder);
                };
                reader.readAsDataURL(input.files[i]);
            }
        }
    };
    $("#images").on("change", function () {
        previewImages(this, "div.images-preview-div");
    });
    $("#images_a").on("change", function () {
        previewImages(this, "div.images-preview-div-2");
    });
});

$(document).on("change", ".up", function () {
    var names = [];
    var length = $(this).get(0).files.length;
    for (var i = 0; i < $(this).get(0).files.length; ++i) {
        names.push($(this).get(0).files[i].name);
    }
    // $("input[name=file]").val(names);
    if (length > 2) {
        var fileName = names.join(", ");
        $(this)
            .closest(".form-group")
            .find(".form-control")
            .attr("value", length + " files selected");
    } else {
        $(this)
            .closest(".form-group")
            .find(".form-control")
            .attr("value", names);
    }
});

$(".DeleteModalBtn").click(function () {
    var id = $(this).data("id");
    var url = $(this).data("url");
    var entity = $(this).data("entity");
    var token = $("meta[name='csrf-token']").attr("content");
    // AJAX request
    $.ajax({
        url: "/" + entity + "/" + url + "/" + id,
        type: "post",
        data: {
            id: id,
            _token: token,
        },
        success: function (response) {
            $("#Modal-body").html(response);
            $("#MainModal").modal("show");
            console.log(response);
        },
    });
});
$(".EditModalBtn").click(function () {
    var id = $(this).data("id");
    var url = $(this).data("url");
    var entity = $(this).data("entity");
    var token = $("meta[name='csrf-token']").attr("content");

    // AJAX request
    $.ajax({
        url: "/" + entity + "/" + url + "/" + id,
        type: "post",
        data: {
            id: id,
            _token: token,
        },
        success: function (response) {
            $("#Modal-body").html(response);
            $("#MainModal").modal("show");
            console.log(response);
        },
    });
});

$(".DetailModalBtn").click(function () {
    var id = $(this).data("id");
    var url = $(this).data("url");
    var entity = $(this).data("entity");
    var token = $("meta[name='csrf-token']").attr("content");

    // AJAX request
    $.ajax({
        url: "/" + entity + "/" + url + "/" + id,
        type: "post",
        data: {
            id: id,
            _token: token,
        },
        success: function (response) {
            $("#Modal-body").html(response);
            $("#MainModal").modal("show");
            console.log(response);
        },
    });
});
$(".EndModalBtn").click(function () {
    var id = $(this).data("id");
    var url = $(this).data("url");
    var entity = $(this).data("entity");
    var token = $("meta[name='csrf-token']").attr("content");

    // AJAX request
    $.ajax({
        url: "/" + entity + "/" + url + "/" + id,
        type: "post",
        data: {
            id: id,
            _token: token,
        },
        success: function (response) {
            $("#Modal-body").html(response);
            $("#MainModal").modal("show");
            console.log(response);
        },
    });
});

function toggle(source) {
    checkboxes = document.getElementsByName("activities[]");
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
}

// $(".showSelect").live("change", function () {
//     var option = $(this.options[index]);
//     var divs = $(option).attr("data-div");

//     $.ajax({
//         url: "/" + entity + "/" + url + "/" + id,
//         type: "post",
//         data: {
//             id: id,
//             _token: token,
//         },
//         success: function (response) {
//             $("#Modal-body").html(response);
//             $("#MainModal").modal("show");
//             console.log(response);
//         },
//     });
// });
