(function ($) {
	$(function () {
		var $slider = $('#hero-slider');
		if (!$slider.length) return;

		var $track = $slider.find('.slides-track');
		var $slides = $track.find('.slide');
		var $dots = $slider.find('.dots span');
		var total = $slides.length;
		if (total < 2) return;

		var current = 0;
		var autoplay;
		var sliderWidth = $slider.width();
		var dragging = false;
		var startX = 0;
		var deltaX = 0;
		var SWIPE_RATIO = 0.2; 

		function setTransition(on) {
			$track.toggleClass('transition', on);
		}

		function render(offsetPercent) {
			var basePercent = -current * 100;
			$track.css('transform', 'translateX(' + (basePercent + offsetPercent) + '%)');
		}

		function goTo(index, animate) {
			current = (index + total) % total;
			setTransition(animate !== false);
			render(0);
			$dots.removeClass('active').eq(current).addClass('active');
		}

		function next() {
			goTo(current + 1);
		}

		function prev() {
			goTo(current - 1);
		}

		function startAutoplay() {
			clearInterval(autoplay);
			autoplay = setInterval(next, 4000);
		}

		$dots.on('click', function () {
			goTo($(this).index());
			startAutoplay();
		});

		$(window).on('resize', function () {
			sliderWidth = $slider.width();
		});

		function dragStart(x) {
			dragging = true;
			startX = x;
			deltaX = 0;
			sliderWidth = $slider.width();
			clearInterval(autoplay);
			setTransition(false);
			$slider.addClass('dragging');
		}

		function dragMove(x) {
			if (!dragging) return;
			deltaX = x - startX;
			render((deltaX / sliderWidth) * 100);
		}

		function dragEnd() {
			if (!dragging) return;
			dragging = false;
			$slider.removeClass('dragging');

			var ratio = deltaX / sliderWidth;
			if (ratio > SWIPE_RATIO) {
				prev();
			} else if (ratio < -SWIPE_RATIO) {
				next();
			} else {
				goTo(current);
			}
			deltaX = 0;
			startAutoplay();
		}

		// Mouse drag
		$slider.on('mousedown', function (e) {
			dragStart(e.pageX);
			e.preventDefault();
		});
		$(document).on('mousemove', function (e) {
			dragMove(e.pageX);
		});
		$(document).on('mouseup', function () {
			dragEnd();
		});

		// Touch swipe
		$slider.on('touchstart', function (e) {
			dragStart(e.originalEvent.touches[0].pageX);
		});
		$slider.on('touchmove', function (e) {
			dragMove(e.originalEvent.touches[0].pageX);
		});
		$slider.on('touchend', function () {
			dragEnd();
		});

		goTo(0, false);
		startAutoplay();
	});
})(jQuery);
