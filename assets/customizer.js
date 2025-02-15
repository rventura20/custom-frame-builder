jQuery(document).ready(function ($) {
    let canvas = document.getElementById("frame-preview-canvas");
    let ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth > 600 ? 500 : window.innerWidth - 40;
    canvas.height = canvas.width;

    if ($("#uploaded-frame-image").length === 0) {
        $("#cfb-live-preview").append('<img id="uploaded-frame-image" src="" style="position: absolute; width: 100%; height: 100%; max-width: 100%; max-height: 100%; cursor: grab; display: none; touch-action: none; object-fit: contain; left: 50%; top: 50%; transform: translate(-50%, -50%);" />');
    }

    $("#frame-image-upload").change(function () {
        let reader = new FileReader();
        reader.onload = function (event) {
            let img = new Image();
            img.onload = function () {
                let imageElement = $("#uploaded-frame-image");
                let frameElement = $("#cfb-live-preview");

                // Get image dimensions
                let imgWidth = img.width;
                let imgHeight = img.height;

                // Set max size for the frame to prevent excessive scaling
                let maxFrameWidth = 500; // Adjust if needed
                let maxFrameHeight = 500;

                // Scale frame while keeping aspect ratio
                if (imgWidth > imgHeight) {
                    frameElement.css({
                        width: `${maxFrameWidth}px`,
                        height: `${(imgHeight / imgWidth) * maxFrameWidth}px`
                    });
                } else {
                    frameElement.css({
                        height: `${maxFrameHeight}px`,
                        width: `${(imgWidth / imgHeight) * maxFrameHeight}px`
                    });
                }

                // Set image inside the frame
                imageElement.attr("src", event.target.result).css({
                    display: "block",
                    width: "100%",
                    height: "100%",
                    maxWidth: "100%",
                    maxHeight: "100%",
                    left: "50%",
                    top: "50%",
                    transform: "translate(-50%, -50%)"
                });

                imageElement.draggable("destroy").draggable({
                    containment: "#cfb-live-preview",
                    scroll: false
                });

                imageElement.resizable("destroy").resizable({
                    aspectRatio: true,
                    containment: "#cfb-live-preview",
                    stop: function () {
                        $(this).draggable({ containment: "#cfb-live-preview" });
                    }
                });

                enableTouchGestures(imageElement[0]);
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    });

    function enableTouchGestures(imageElement) {
        let scale = 1, startDist = 0, lastScale = 1;

        imageElement.addEventListener("touchstart", function (e) {
            if (e.touches.length === 2) {
                startDist = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
            }
        });

        imageElement.addEventListener("touchmove", function (e) {
            e.preventDefault();
            if (e.touches.length === 2) {
                let newDist = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
                scale = lastScale * (newDist / startDist);
                imageElement.style.transform = `scale(${scale}) translate(-50%, -50%)`;
            }
        });

        imageElement.addEventListener("touchend", function () {
            lastScale = scale;
        });
    }
});
