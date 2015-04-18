$(document).ready(function() {

	window.sr = new scrollReveal();

	// Owl Carousel Plugin

	var owl = $("#menu-carousel");

      owl.owlCarousel({
      	singleItem:true,
      	autoHeight:true,

      });

      $(".owl-carousel").owlCarousel();

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

    //DatPicker Plugin

      $('.datepicker').pickadate({
      	format: 'mmm dd, yyyy',
      	container: '#wrapper'
      });

      $('.timepicker').pickatime({
      	min: [15,0],
      	max: [22,0],
      	container: '#wrapper'
      });
 

});