$(document).ready(function() {

  window.sr = new scrollReveal();

  // $(".menu-carousel").owlCarousel({

  // 	navigation : true, // Show next and prev buttons
  //   singleItem:true,
  //   autoHeight:true,

  // });

  // var testMenu = $(".menu-carousel");

  // testMenu.owlCarousel();

  // $(".next").click(function(){
  //   testMenu.trigger('testMenu.next');
  // })
  // $(".prev").click(function(){
  //   testMenu.trigger('testMenu.prev');
  // })

var owl = $("#menu-carousel");

      owl.owlCarousel({
      	singleItem:true,
      	autoHeight:true,

      });

      $(".owl-carousel").owlCarousel()

      var owl = $(".owl-carousel").data('owlCarousel');

      // owl.jumpTo(3)

      // Custom Navigation Events
      $(".appetizer").click(function(){
        owl.jumpTo(0);
      })
      $(".entree").click(function(){
        owl.jumpTo(1);
      })
      $(".dessert").click(function(){
        owl.jumpTo(2);
      })
      $(".wine").click(function(){
        owl.jumpTo(3);
      })


// if ( $('.menus').length ) {
// 		var menuCarousel = $('.menu-carousel').owlCarousel({
// 			singleItem: true,
// 			items: 1,
// 			nav: true,
// 			mouseDrag: false,
// 			navSpeed: 1000,
// 			animateIn: 'fadeIn',
// 			animateOut: 'fadeOutDown',
// 			navContainer: '.menu-carousel-nav',
// 			navText: ['<span></span>', '<span></span>']
// 		});
// 	};

// 	function menuMeals(){
		
// 		var menuMeals = $('.menu-meals'),
// 			menuMealsThumbnail = menuMeals.owlCarousel({
// 			items: 1,
// 			singleItem: true,
// 			mouseDrag: false,
// 			touchDrag: false
// 		});

// 		menuMeals.find('.owl-item').on('click', function(event) {
// 			var $this = $(this);
// 			$this.addClass('active').siblings().removeClass('active');
// 			menuCarousel.trigger('to.owl.carousel', $this.index());
// 		});

// 		menuCarousel.on('changed.owl.carousel', function(event) {
// 			var activeMenu = event.item.index;
// 			console.log(activeMenu);
// 			menuMeals.find('.owl-item:nth-child('+ (activeMenu + 1) + ')' ).addClass('active').siblings().removeClass('active')
// 		});

// 	};
// 	if ( $('.menu-meals').length ) {
// 		menuMeals();
// 	};

});