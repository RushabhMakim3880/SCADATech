(function ($) {
    let currentIndex = 0;
    let items = [];
    let isPlaying = false;
    let slideshowIntervalRef;
    let zoomLevel = 1;
    let translateX = 0, translateY = 0;
    let isDragging = false;
    let lastX, lastY;
    let dragged = false;
    let config = {};

    function createBaseStructure() {
        if (!$("#customSlideshow").length) {
            $("body").append(`
                <div id="customSlideshow" class="slideshow-container" style="display: none;">
                    <button class="play-pause-btn">Play</button>
                    <span class="close-btn">&times;</span>
                    <button class="prev-btn">&#10094;</button>
                    <button class="next-btn">&#10095;</button>
                    <div class="slide-wrapper">
                        <img id="mainSlide" src="" alt="Slideshow Image">
                    </div>
                    <div class="thumbnails"></div>
                </div>
            `);
        }
    }

    function openSlideshow(imageList, startIdx = 0, options = {}) {
        items = imageList;
        currentIndex = startIdx;
        config = $.extend({
            isLightbox: false,
            showNavButtons: true,
            showThumbnails: true,
            showPlayButton: true,
            autoSlideshow: false,
            slideshowInterval: 3000,
        }, options);

        createBaseStructure();
        const $slideshow = $("#customSlideshow");
        let $mainSlide = $("#mainSlide");
        let $thumbnails = $(".thumbnails");

        resetZoom();

        // Controls visibility
        $(".play-pause-btn").toggle(config.showPlayButton && !config.isLightbox);
        $(".prev-btn, .next-btn").toggle(config.showNavButtons && !config.isLightbox);
        $(".thumbnails").toggle(config.showThumbnails && !config.isLightbox);

        showSlide(currentIndex, false);

        $thumbnails.empty();
        if (config.showThumbnails && !config.isLightbox) {
            $.each(items, function (index, src) {
                let $thumb = $("<img>").attr("src", src.thumb).addClass(index === currentIndex ? "active" : "");
                $thumb.on("click", function (e) {
                    e.stopPropagation();
                    showSlide(index);
                });
                $thumbnails.append($thumb);
            });
            centerActiveThumbnail(true);
        }

        $slideshow.fadeIn().css("display", "flex");
        stopSlideshow();
        preloadImages(items);

        if (config.autoSlideshow && !config.isLightbox) {
            startSlideshow();
        }

        $(".play-pause-btn").off().on("click", function (e) {
            e.stopPropagation();
            isPlaying ? stopSlideshow() : startSlideshow();
        });

        $(".prev-btn").off().on("click", function (e) {
            e.stopPropagation();
            prevSlide();
        });

        $(".next-btn").off().on("click", function (e) {
            e.stopPropagation();
            nextSlide();
        });

        $(".close-btn").off().on("click", function (e) {
            e.stopPropagation();
            closeViewer();
        });

        centerActiveThumbnail();
    }

    function showSlide(index, animate = true) {
        let $mainSlide = $("#mainSlide");
        let direction = (index > currentIndex) ? "left" : "right";
        if (currentIndex === 0 && index === items.length - 1) direction = "left";
        if (currentIndex === items.length - 1 && index === 0) direction = "right";

        $mainSlide.addClass("oldSlides");
        $mainSlide.removeAttr("id");

        let newImage = $("<img>")
            .attr("src", items[index].full)
            .addClass("slide-transition")
            .css({
                position: "absolute",
                top: 0,
                left: direction === "left" ? "100%" : "-100%",
                width: "100%",
                height: "auto",
                maxHeight: "100%",
                objectFit: "contain",
                opacity: 0,
                transition: animate ? "left 0.5s ease-in-out, opacity 0.5s ease-in-out" : "none",
            });

        $mainSlide.parent().append(newImage);

        setTimeout(() => {
            newImage.css({ left: "0", opacity: 1 });
            $mainSlide.css({ left: direction === "left" ? "-100%" : "100%", opacity: 0 });
        }, 10);

        setTimeout(() => {
            // $(".slide-wrapper img.oldSlides").remove();
            resetZoom();
            $mainSlide.remove();
            newImage.attr("id", "mainSlide");
        }, 500);

        currentIndex = index;
        $(".thumbnails img").removeClass("active").eq(currentIndex).addClass("active");
        centerActiveThumbnail();
    }

    function nextSlide() {
        showSlide((currentIndex + 1) % items.length);
    }

    function prevSlide() {
        showSlide((currentIndex - 1 + items.length) % items.length);
    }

    function startSlideshow() {
        isPlaying = true;
        $(".play-pause-btn").text("Pause");
        slideshowIntervalRef = setInterval(nextSlide, config.slideshowInterval);
    }

    function stopSlideshow() {
        isPlaying = false;
        $(".play-pause-btn").text("Play");
        clearInterval(slideshowIntervalRef);
    }

    function applyZoom() {
        $("#mainSlide").css("transform", `scale(${zoomLevel}) translate(${translateX}px, ${translateY}px)`);
        $("#mainSlide").css("cursor", zoomLevel > 1 ? "grab" : "zoom-in");
    }

    function resetZoom() {
        zoomLevel = 1;
        translateX = 0;
        translateY = 0;
        applyZoom();
        isDragging = false;
        dragged = false;
    }

    function centerActiveThumbnail(initialLoad = false) {
        let $thumbs = $(".thumbnails");
        let $active = $thumbs.find(".active");
        if ($active.length) {
            let containerWidth = $thumbs.width();
            let thumbOffset = $active.position().left + $active.width() / 2;
            let scroll = thumbOffset - containerWidth / 2;
            if (initialLoad) {
                $thumbs.css("transform", `translateX(${-scroll}px)`);
            } else {
                $thumbs.css("transition", "transform 0.3s ease-in-out");
                $thumbs.css("transform", `translateX(${-scroll}px)`);
            }
        }
    }

    function closeViewer() {
        $("#customSlideshow").fadeOut();
        stopSlideshow();
    }

    function preloadImages(list) {
        list.forEach(img => {
            const fullImg = new Image();
            fullImg.src = img.full;
            if (img.thumb) {
                const thumbImg = new Image();
                thumbImg.src = img.thumb;
            }
        });
    }

    // Passive fix
    document.addEventListener("wheel", function (event) {
        if (!$("#customSlideshow").is(":visible")) return;
        event.preventDefault();
        let scaleFactor = event.deltaY > 0 ? -0.1 : 0.1;
        zoomLevel = Math.max(1, Math.min(3, zoomLevel + scaleFactor));
        applyZoom();
    }, { passive: false });

    // Delegated events
    $(document).on("mousedown", "#mainSlide", function (event) {
        if (zoomLevel > 1) {
            isDragging = true;
            dragged = false;
            lastX = event.clientX;
            lastY = event.clientY;
            $("#mainSlide").css("cursor", "grabbing");
            event.preventDefault();
        }
    });

    $(document).on("mousemove", function (event) {
        if (isDragging) {
            event.preventDefault();
            let dx = event.clientX - lastX;
            let dy = event.clientY - lastY;
            lastX = event.clientX;
            lastY = event.clientY;
            translateX += dx;
            translateY += dy;
            applyZoom();
            dragged = true;
        }
    });

    $(document).on("mouseup", function () {
        isDragging = false;
        if (zoomLevel > 1) {
            $("#mainSlide").css("cursor", "grab");
        } else {
            $("#mainSlide").css("cursor", "zoom-in");
        }
    });

    $("#customSlideshow").on("click", function (event) {
        if (dragged) {
            event.stopImmediatePropagation();
            dragged = false;
            return;
        }
        if (!config.isLightbox && config.showNavButtons) {
            if (event.pageX < $(window).width() / 2) prevSlide();
            else nextSlide();
        }
    });

    $(document).on("click", "#mainSlide", function (event) {
        if (dragged) {
            event.stopImmediatePropagation();
            dragged = false;
            return;
        }
        if (zoomLevel > 1) {
            resetZoom();
        }
    });

    $(document).on("keydown", function (e) {
        if ($("#customSlideshow").is(":visible") && !config.isLightbox && config.showNavButtons) {
            if (e.key === "ArrowRight") nextSlide();
            else if (e.key === "ArrowLeft") prevSlide();
        }
        if ($("#customSlideshow").is(":visible")) {
            if (e.key === "Escape") closeViewer();
        }
    });

    window.CustomImageViewer = {
        openSlideshow,
    };
})(jQuery);

// Example usage: For slideshow with thumbnails

// $images = [
//     { full: "https://picsum.photos/800/600?random=1", thumb: "https://picsum.photos/100/75?random=1" },
//     { full: "https://picsum.photos/800/600?random=2", thumb: "https://picsum.photos/100/75?random=2" },
//     { full: "https://picsum.photos/800/600?random=3", thumb: "https://picsum.photos/100/75?random=3" },
//     { full: "https://picsum.photos/800/600?random=4", thumb: "https://picsum.photos/100/75?random=4" },
//     { full: "https://picsum.photos/800/600?random=5", thumb: "https://picsum.photos/100/75?random=5" }
// ];
// CustomImageViewer.openSlideshow($images, 0);

// Example usage: For single lightbox image
// var image = [{ full: "https://picsum.photos/800/600?random=1", thumb: "" }];
// CustomImageViewer.openSlideshow(image, 0, { isLightbox: true });
