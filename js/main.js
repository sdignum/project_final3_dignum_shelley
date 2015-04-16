$(document).ready(function() {

  window.sr = new scrollReveal();
  
  $('#topbar').scrollupbar();

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

      if( $('.floating-labels').length > 0 ) floatLabels();
 

});