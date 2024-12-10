export const handlerMenuClasses = () => {
    //Menu Toggle Script
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    // For highlighting activated tabs
    $(".tabs").click(function() {
        $(".tabs").removeClass("active1").addClass("bg-light");
        $(this).addClass("active1").removeClass("bg-light");
        $('#descriptionPayment').html($(this).text().trim())

    });


}