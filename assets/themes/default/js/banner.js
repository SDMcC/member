$(document).ready(function () {

  $('#bannerSlider').oneByOne({
    className: 'oneByOne1', // the wrapper's name
    easeType: 'random', //'fadeInLeft',  // the ease animation style
    width: 1170, // width of the slider
    height: 400, // height of the slider
    delay: 300, // the delay of the touch/drag tween
    tolerance: 0.25, // the tolerance of the touch/drag  
    enableDrag: true, // enable or disable the drag function by mouse
    showArrow: false, // display the previous/next arrow or not
    showButton: true, // display the circle buttons or not
    slideShow: true, // auto play the slider or not
    slideShowDelay: 3000 // the delay millisecond of the slidershow

  });


  if ($('#bannerWrapper').hasClass('frontpage-slides')) {
    var bg_images = ['frontpage_bg_1.jpg', 'frontpage_bg_2.jpg', 'frontpage_bg_3.jpg'];
    var img = window.location.protocol + '//' + window.location.host + '/assets/themes/default/images/' + bg_images[Math.floor(Math.random() * bg_images.length)];

    $('.frontpage-slides').css({
      'background-image': 'url(' + img + ')'
    });
  }


});
