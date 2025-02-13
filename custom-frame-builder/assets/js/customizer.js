jQuery(document).ready(function ($) {
    let selectedCategory = "";
    let selectedFrame = "";
    let selectedSize = "8x10";
    let selectedPlexiglass = "none";

    // ✅ Handle Category Selection
    $(".frame-category-btn").click(function () {
        selectedCategory = $(this).data("category");

        $(".frame-category-btn").removeClass("active");
        $(this).addClass("active");

        // Fetch frames dynamically
        $.post(customizer_ajax.ajax_url, {
            action: "cfb_get_frames",
            category: selectedCategory,
            security: customizer_ajax.nonce
        }, function (response) {
            $("#frame-selector").html(response);
            $("#frame-selector").trigger("change");
        });

        // Show Plexiglass option only for Black & Gold Metal
        if (selectedCategory === "black_metal" || selectedCategory === "gold_metal") {
            $("#plexiglass-type").show();
        } else {
            $("#plexiglass-type").hide();
        }
    });

    // ✅ Handle Frame Selection
    $("#frame-selector").change(function () {
        selectedFrame = $(this).val();

        // Update Frame Preview
        $("#frame-preview").attr("src", selectedFrame);

        // Update Price when selecting frame
        updatePrice();
    });

    // ✅ Handle Size Selection
    $("#frame-size").change(function () {
        selectedSize = $(this).val();
        updatePrice();
    });

    // ✅ Handle Plexiglass Selection
    $("#plexiglass-type").change(function () {
        selectedPlexiglass = $(this).val();
        updatePrice();
    });

    // ✅ Function to Fetch & Update Price
    function updatePrice() {
        $.post(customizer_ajax.ajax_url, {
            action: "cfb_get_price",
            frame_id: selectedFrame,
            size: selectedSize,
            plexiglass: selectedPlexiglass,
            security: customizer_ajax.nonce
        }, function (response) {
            $("#frame-price").text("$" + response);
        });
    }

    // ✅ Handle Image Upload & Preview
    $("#user-image").change(function (event) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#preview-canvas").attr("src", e.target.result);
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    // ✅ Handle Add to Cart
    $("#add-to-cart").click(function () {
        $.post(customizer_ajax.ajax_url, {
            action: "cfb_add_to_cart",
            frame_id: selectedFrame,
            size: selectedSize,
            plexiglass: selectedPlexiglass,
            user_image: $("#preview-canvas").attr("src"),
            security: customizer_ajax.nonce
        }, function (response) {
            if (response.success) {
                alert("Added to cart!");
                window.location.href = "/cart"; // Redirect to cart page
            } else {
                alert("Error adding to cart.");
            }
        });
    });

    // ✅ Trigger first category selection on load
    $(".frame-category-btn").first().trigger("click");
});
