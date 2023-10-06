/*DataTable*/
var listing = $("#hospital_tbl").DataTable({
    aLengthMenu: [
        [10, 30, 50, -1],
        [10, 30, 50, "All"],
    ],
    iDisplayLength: 10,
    language: {
        search: "",
    },
    ajax: {
        type: "POST",
        url: aurl + "/admin/hospital/listing",
    },
    columns: [
        { data: "0" },
        { data: "1" },
        { data: "2" },
        { data: "3" },
        { data: "4" },
        { data: "5" },
        { data: "6" },
        { data: "7" },
        // { data: "8" },
        { data: "8" }

    ],
});
$(document).ready(function() {

    /* Validation Of Hospital Form */
    $("#hospital_form").validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
            },
            password: {
                required: true,
            },
            contact: {
                required: true,
            },
            address: {
                required: true,
            },

        },
        messages: {
            name: {
                required: "Please Enter Name",
            },
            email: {
                required: "Please Enter Email",
            },
            password: {
                required: "Please Enter Password",
            },
            contact: {
                required: "Please Enter Contact Number",
            },
            address: {
                required: "Please Enter Address",
            },


        },
        highlight: function(element) {
            $(element).removeClass("error");
        },
        normalizer: function(value) {
            return $.trim(value);
        },
    });
});

/* Add Hospital Modal */
$("body").on("click", ".add_hospital", function() {
    $("#hospital_form").validate().resetForm();
    $("#hospital_form").trigger("reset");
    $("#hospital_modal").modal("show");
    $(".id").val('');
    $("#title_hospital_modal").text("Add Hospital");
    $(".submit_hospital").text("Add Hospital");
});

/* Adding And Updating Hospital Modal */
$(".submit_hospital").click(function(event) {
    event.preventDefault();
    var form = $("#hospital_form")[0];
    var formData = new FormData(form);
    if ($("#hospital_form").valid()) {
        $.ajax({
            url: aurl + "/admin/hospital/store",
            type: "POST",
            dataType: "JSON",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#hospital_modal").modal("hide");
                toaster_message(data.message, data.icon);
            },
            error: function(request) {
                toaster_message(
                    "Something Went Wrong! Please Try Again.",
                    "error"
                );
            },
        });
    }
});