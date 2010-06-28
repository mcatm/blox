// jQuery SmoothScroll | Version 09-11-02
$(function() {
	$('a[href*=#]').click(function() {

	   // duration in ms
	   var duration = 200;

	   // easing values: swing | linear
	   var easing = 'swing';

	   // get / set parameters
	   var newHash = this.hash;
	   var target=$(this.hash).offset().top;
	   var oldLocation=window.location.href.replace(window.location.hash, '');
	   var newLocation=this;

	   // make sure it's the same location      
	   if(oldLocation+newHash==newLocation){
		  // animate to target and set the hash to the window.location after the animation
		  $('html:not(:animated),body:not(:animated)').animate({ scrollTop: target }, duration, easing, function() {

			 // add new hash to the browser location
			 window.location.href=newLocation;
		  });

		  // cancel default click action
		  return false;
	   }
	});
});