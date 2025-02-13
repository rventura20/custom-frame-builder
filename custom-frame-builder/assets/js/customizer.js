jQuery(document).ready(function($) {
    $("#add-to-cart").click(function() {
        const canvas = document.getElementById('preview-canvas');
        const finalImage = canvas.toDataURL("image/png");

        $.post(cfbVars.ajaxurl, {
            action: "cfb_add_to_cart",
            image: finalImage,
            frame: $("#frame-selector").val()
        }, function(response) {
            if (response.success) {
                alert("Added to cart!");
                window.location.href = "/cart";
            } else {
                alert("Error: " + response.data);
            }
        });
    });
});
