// let link = document.querySelectorAll('.navbar-nav .nav-item .nav-link');
// let linkActive = document.querySelector('.navbar-nav .nav-item .nav-link.active');
// console.log(link);
// for (let i = 0; i < link.length; i++) {
//   link[i].addEventListener("click", function (e) {
//     //
//     // linkValue = e.target.innerText;
//     let linkValue = this;
//     console.log(linkValue);

//     //to change ative link style
//     linkActive.classList.remove("active");
//     linkValue.classList.add("active");
//     linkActive = linkValue;
//   });
// }

// navbar scroll

(() => {
  const navbar = document.querySelector(".navbar");
  if (!navbar) return;

  let lastSolidState = null;
  let framePending = false;

  const syncNavbarOpacity = () => {
    const solid = window.scrollY > 300;
    if (solid !== lastSolidState) {
      navbar.classList.toggle("bg-opacity-50", !solid);
      lastSolidState = solid;
    }
    framePending = false;
  };

  window.addEventListener(
    "scroll",
    () => {
      if (framePending) return;
      framePending = true;
      window.requestAnimationFrame(syncNavbarOpacity);
    },
    { passive: true }
  );

  syncNavbarOpacity();
})();

// home video

$(document).on("click", "#body-overlay", function (e) {
  e.preventDefault();
  $("#body-overlay").removeClass("active");
  $(".video-popup").removeClass("active");
  $(".video-popup iframe").attr("src", "");
});
$(document).on("click", ".contactIcon", function (e) {
  e.preventDefault();

  $(".video-popup iframe").attr(
    "src",
    "https://www.youtube.com/embed/BOXYG4llnKY?color=white"
  );
  $(".video-popup").addClass("active");
  $("#body-overlay").addClass("active");
});

$(document).ready(function () {
  // home team
  if ($(".teamSlider").length) {
    $(".teamSlider").slick({
      autoplay: true,
      autoplaySpeed: 3800,
      pauseOnHover: true,
      arrows: false,
      dots: true,
      speed: 650,
      centerMode: true,
      centerPadding: "60px",
      slidesToShow: 3,
      slickCurrentSlide: arguments,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            arrows: false,
            centerMode: true,
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 768,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "40px",
            slidesToShow: 1,
          },
        },
        {
          breakpoint: 480,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "20px",
            slidesToShow: 1,
          },
        },
      ],
    });
  }

  if ($(".logoSlider").length) {
    $(".logoSlider").slick({
      autoplay: true,
      autoplaySpeed: 3600,
      pauseOnHover: true,
      arrows: false,
      speed: 600,
      centerMode: true,
      centerPadding: "160px",
      slidesToShow: 3,
      slickCurrentSlide: arguments,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "100px",
            slidesToShow: 3,
          },
        },
        {
          breakpoint: 768,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "80px",
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 480,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "50px",
            slidesToShow: 1,
          },
        },
      ],
    });
  }

  // about page
  if ($(".feedbackSlider").length) {
    $(".feedbackSlider").slick({
      autoplay: true,
      autoplaySpeed: 4200,
      pauseOnHover: true,
      arrows: false,
      dots: true,
      speed: 700,
      centerMode: true,
      centerPadding: "60px",
      slidesToShow: 2,
      slickCurrentSlide: arguments,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            arrows: false,
            centerMode: true,
            slidesToShow: 1,
          },
        },
        {
          breakpoint: 768,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "40px",
            slidesToShow: 1,
          },
        },
        {
          breakpoint: 480,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "20px",
            slidesToShow: 1,
          },
        },
      ],
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  $(".counter").countTo();
});

// about

$(".soluationHead li").on("click", function () {
  // console.log($(this).attr("data-soluation"));
  let attrValue = $(this).attr("data-soluation");
  $(this).parent().next().find(`p`).addClass("d-none");
  $(this).parent().next().find(`p.${attrValue}`).removeClass("d-none");
});

// portfolio
document.addEventListener("DOMContentLoaded", function () {
  if (!document.querySelector("#portfolio")) {
    return;
  }

  mixitup("#portfolio");
  $(function () {
    let allImages = Array.from(
      document.querySelectorAll("#portfolio .item img")
    );
    let currentIndex;
    let portfolioLayer = document.querySelector("#portfolio .portfolioLayer");
    let isLayerOpen = false;
    // console.log(allImages);

    function getNextImg() {
      currentIndex++;
      // console.log(currentIndex);
      if (currentIndex == allImages.length) {
        currentIndex = 0;
      }
      $("#portfolio .portfolioLayer figure").css(
        "background-image",
        `url(${allImages[currentIndex].getAttribute("src")})`
      );
    }

    function getPrevImg() {
      currentIndex--;
      // console.log(currentIndex);
      if (currentIndex == -1) {
        currentIndex = allImages.length - 1;
      }
      $("#portfolio .portfolioLayer figure").css(
        "background-image",
        `url(${allImages[currentIndex].getAttribute("src")})`
      );
    }
    function closeImg() {
      $("#portfolio .portfolioLayer").addClass("d-none");
      isLayerOpen = false;
    }

    $("#nextIcon").on("click", getNextImg);
    $("#prevIcon").on("click", getPrevImg);
    $("#closeIcon").on("click", closeImg);

    document.addEventListener("keydown", function (e) {
      if (!isLayerOpen) return;
      if (e.key == "ArrowRight") {
        getNextImg();
      } else if (e.key == "ArrowLeft") {
        getPrevImg();
      } else if (e.key == "Escape") {
        closeImg();
      }
    });

    $("#portfolio .portfolioLayer").on("click", function (e) {
      if (!isLayerOpen) return;
      if (e.target == portfolioLayer) {
        closeImg();
      }
    });

    $("#portfolio .item img").on("click", function () {
      $("#portfolio .portfolioLayer").removeClass("d-none");
      isLayerOpen = true;
      // console.log($(this).attr("src"));
      currentIndex = allImages.indexOf(this);
      let currentSrc = $(this).attr("src");
      $("#portfolio .portfolioLayer figure").css(
        "background-image",
        `url(${currentSrc})`
      );
    });
  });
});

// FAQS

$(function () {
  $(".problemInfo .plus").on("click", function () {
    $(".problemDesc").addClass("d-none");
    $(this).parent().siblings().removeClass("d-none");
    $(".minus").addClass("d-none");
    $(".plus").removeClass("d-none");
    $(this).parent().find(".plus").addClass("d-none");
    $(this).parent().find(".minus").removeClass("d-none");
  });
  $(".problemInfo .minus").on("click", function () {
    $(this).parent().siblings().toggleClass("d-none");
    $(".minus").addClass("d-none");
    $(".plus").removeClass("d-none");
  });
});

// services

$(".work .workHead li").on("click", function () {
  let attrValue = $(this).attr("data-soluation");
  $(this).parent().next().find(`.row`).addClass("d-none");
  $(this).parent().next().find(`.row.${attrValue}`).removeClass("d-none");
});

// home

$(function () {
  if ($(".skitter-large").length) {
    $(".skitter-large").skitter({
      navigation: true,
      dots: false,
      interval: 4500,
      with_animations: ["fade", "swapBars"],
    });
  }
});
