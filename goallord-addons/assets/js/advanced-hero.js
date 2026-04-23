/**
 * Goallord Addons — Advanced Hero
 * Minimal, dependency-free slider + entrance reveal.
 *
 *  - Slide transition: moves .goallord-ah__track by translateX for slide mode
 *  - Fade transition: toggles .is-active on slides (CSS handles opacity)
 *  - Touch swipe, arrow + dot + keyboard nav, autoplay + pause-on-hover, loop
 *  - IntersectionObserver entrance reveal sets .is-visible on the wrapper
 *  - Per-slide reveal: when the active slide changes, the new one gets
 *    .has-revealed so its staggered elements animate in
 */
( function () {
	'use strict';

	var ROOT_SELECTOR = '.goallord-ah';
	var prefersReduced = typeof window.matchMedia === 'function' &&
		window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ------------------------ Entrance reveal ------------------------ */

	function revealRoot( root ) {
		if ( ! root || root.classList.contains( 'is-visible' ) ) return;
		root.classList.add( 'is-visible' );
		var active = root.querySelector( '.goallord-ah__slide.is-active' );
		if ( active ) active.classList.add( 'has-revealed' );
	}

	function initReveal( scope ) {
		var roots = scope.querySelectorAll( ROOT_SELECTOR + '[data-goallord-ah-animate="1"]' );
		if ( ! roots.length ) return;

		if ( prefersReduced || typeof window.IntersectionObserver !== 'function' ) {
			for ( var i = 0; i < roots.length; i++ ) revealRoot( roots[ i ] );
			return;
		}

		var io = new IntersectionObserver( function ( entries, obs ) {
			for ( var i = 0; i < entries.length; i++ ) {
				var e = entries[ i ];
				if ( e.isIntersecting ) {
					revealRoot( e.target );
					obs.unobserve( e.target );
				}
			}
		}, { root: null, threshold: 0.1, rootMargin: '0px 0px -40px 0px' } );

		for ( var j = 0; j < roots.length; j++ ) io.observe( roots[ j ] );
	}

	/* ------------------------ Slider ------------------------ */

	function initSliders( scope ) {
		var sliders = scope.querySelectorAll( ROOT_SELECTOR + '[data-slider="1"]' );
		for ( var i = 0; i < sliders.length; i++ ) mountSlider( sliders[ i ] );
	}

	function mountSlider( root ) {
		if ( root.__goallordAh ) return; // already mounted

		var track   = root.querySelector( '.goallord-ah__track' );
		var slides  = Array.prototype.slice.call( root.querySelectorAll( '.goallord-ah__slide' ) );
		var prev    = root.querySelector( '.goallord-ah__arrow--prev' );
		var next    = root.querySelector( '.goallord-ah__arrow--next' );
		var dots    = Array.prototype.slice.call( root.querySelectorAll( '.goallord-ah__dot' ) );

		if ( ! track || slides.length <= 1 ) return;

		var isFade   = root.classList.contains( 'goallord-ah--transition-fade' );
		var loop     = root.dataset.loop === '1';
		var swipe    = root.dataset.swipe === '1';
		var keyboard = root.dataset.keyboard === '1';
		var autoplay = root.dataset.autoplay === '1' && ! prefersReduced;
		var ap_ms    = parseInt( root.dataset.autoplayMs || '6000', 10 );
		var pauseOnHover = root.dataset.pauseHover === '1';

		var state = { index: 0, timer: null, paused: false };

		function setActive( newIndex ) {
			if ( newIndex === state.index ) return;
			var total = slides.length;
			if ( loop ) {
				newIndex = ( newIndex + total ) % total;
			} else {
				if ( newIndex < 0 ) newIndex = 0;
				if ( newIndex >= total ) newIndex = total - 1;
			}

			// Swap active class
			slides[ state.index ].classList.remove( 'is-active', 'has-revealed' );
			slides[ state.index ].setAttribute( 'aria-hidden', 'true' );
			slides[ newIndex ].classList.add( 'is-active' );
			slides[ newIndex ].setAttribute( 'aria-hidden', 'false' );

			// Dots
			if ( dots.length ) {
				dots.forEach( function ( d, i ) {
					var on = ( i === newIndex );
					d.classList.toggle( 'is-active', on );
					if ( on ) d.setAttribute( 'aria-current', 'true' );
					else d.removeAttribute( 'aria-current' );
				} );
			}

			// Move track or rely on CSS fade
			if ( ! isFade ) {
				track.style.transform = 'translate3d(' + ( -newIndex * 100 ) + '%, 0, 0)';
			}

			state.index = newIndex;

			// Trigger entrance animation for the new active slide
			// Force reflow, then add .has-revealed next frame.
			requestAnimationFrame( function () {
				requestAnimationFrame( function () {
					if ( root.classList.contains( 'is-visible' ) ) {
						slides[ newIndex ].classList.add( 'has-revealed' );
					}
				} );
			} );
		}

		function goNext() { setActive( state.index + 1 ); }
		function goPrev() { setActive( state.index - 1 ); }

		function startAutoplay() {
			if ( ! autoplay || state.timer || state.paused ) return;
			state.timer = setInterval( goNext, ap_ms );
		}
		function stopAutoplay() {
			if ( state.timer ) { clearInterval( state.timer ); state.timer = null; }
		}

		if ( prev ) prev.addEventListener( 'click', function () { goPrev(); resetAutoplay(); } );
		if ( next ) next.addEventListener( 'click', function () { goNext(); resetAutoplay(); } );

		dots.forEach( function ( d ) {
			d.addEventListener( 'click', function () {
				var to = parseInt( d.getAttribute( 'data-slide-to' ), 10 );
				if ( ! isNaN( to ) ) { setActive( to ); resetAutoplay(); }
			} );
		} );

		function resetAutoplay() {
			if ( ! autoplay ) return;
			stopAutoplay();
			startAutoplay();
		}

		if ( pauseOnHover ) {
			root.addEventListener( 'mouseenter', function () { state.paused = true; stopAutoplay(); } );
			root.addEventListener( 'mouseleave', function () { state.paused = false; startAutoplay(); } );
			root.addEventListener( 'focusin',    function () { state.paused = true; stopAutoplay(); } );
			root.addEventListener( 'focusout',   function () { state.paused = false; startAutoplay(); } );
		}

		if ( keyboard ) {
			root.setAttribute( 'tabindex', '0' );
			root.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'ArrowLeft' )  { goPrev(); resetAutoplay(); }
				if ( e.key === 'ArrowRight' ) { goNext(); resetAutoplay(); }
			} );
		}

		if ( swipe ) attachSwipe( root, goPrev, goNext, resetAutoplay );

		// Pause when tab is hidden — browsers throttle intervals too, but
		// this also stops visible flicker when returning.
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) stopAutoplay(); else startAutoplay();
		} );

		root.__goallordAh = { goNext: goNext, goPrev: goPrev, setActive: setActive };

		startAutoplay();
	}

	function attachSwipe( root, goPrev, goNext, resetAutoplay ) {
		var startX = null, startY = null, tracking = false;
		var THRESHOLD = 40;

		root.addEventListener( 'touchstart', function ( e ) {
			if ( ! e.touches || ! e.touches[ 0 ] ) return;
			startX = e.touches[ 0 ].clientX;
			startY = e.touches[ 0 ].clientY;
			tracking = true;
		}, { passive: true } );

		root.addEventListener( 'touchend', function ( e ) {
			if ( ! tracking || startX === null ) return;
			tracking = false;
			var endX = ( e.changedTouches && e.changedTouches[ 0 ] ) ? e.changedTouches[ 0 ].clientX : startX;
			var endY = ( e.changedTouches && e.changedTouches[ 0 ] ) ? e.changedTouches[ 0 ].clientY : startY;
			var dx = endX - startX;
			var dy = endY - startY;
			// only horizontal swipes
			if ( Math.abs( dx ) > THRESHOLD && Math.abs( dx ) > Math.abs( dy ) ) {
				if ( dx < 0 ) goNext(); else goPrev();
				resetAutoplay();
			}
			startX = null; startY = null;
		}, { passive: true } );
	}

	/* ------------------------ Boot ------------------------ */

	function init( scope ) {
		scope = scope || document;
		initReveal( scope );
		initSliders( scope );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { init( document ); } );
	} else {
		init( document );
	}

	// Elementor editor hook — re-init when widget is added/edited.
	if ( window.jQuery && typeof window.elementorFrontend !== 'undefined' ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction(
					'frontend/element_ready/goallord-advanced-hero.default',
					function ( $scope ) {
						// In editor preview, force reveal immediately.
						if ( window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode() ) {
							var roots = $scope[ 0 ].querySelectorAll( ROOT_SELECTOR + '[data-goallord-ah-animate="1"]' );
							for ( var i = 0; i < roots.length; i++ ) revealRoot( roots[ i ] );
						}
						init( $scope[ 0 ] );
					}
				);
			}
		} );
	}
} )();
