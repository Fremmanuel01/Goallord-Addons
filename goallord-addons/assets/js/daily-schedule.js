/**
 * Goallord Addons — Daily Schedule
 * Entrance reveal via IntersectionObserver.
 * Adds .is-visible to the widget wrapper when it scrolls into view,
 * which triggers both the item fade-in and the timeline rail grow.
 */
( function () {
	'use strict';

	var SELECTOR = '.goallord-ds[data-goallord-ds-animate="1"]';
	var VISIBLE_CLASS = 'is-visible';

	var prefersReducedMotion =
		typeof window.matchMedia === 'function' &&
		window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function reveal( el ) {
		if ( ! el || el.classList.contains( VISIBLE_CLASS ) ) {
			return;
		}
		el.classList.add( VISIBLE_CLASS );
	}

	function init( root ) {
		var scope = root && root.querySelectorAll ? root : document;
		var nodes = scope.querySelectorAll( SELECTOR );

		if ( ! nodes.length ) {
			return;
		}

		if ( prefersReducedMotion || typeof window.IntersectionObserver !== 'function' ) {
			for ( var i = 0; i < nodes.length; i++ ) {
				reveal( nodes[ i ] );
			}
			return;
		}

		var observer = new IntersectionObserver( function ( entries, obs ) {
			for ( var i = 0; i < entries.length; i++ ) {
				var entry = entries[ i ];
				if ( entry.isIntersecting ) {
					reveal( entry.target );
					obs.unobserve( entry.target );
				}
			}
		}, {
			root: null,
			threshold: 0.1,
			rootMargin: '0px 0px -40px 0px'
		} );

		for ( var j = 0; j < nodes.length; j++ ) {
			observer.observe( nodes[ j ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { init( document ); } );
	} else {
		init( document );
	}

	// Elementor editor hook — rerun when this widget is added/edited.
	if ( window.jQuery && typeof window.elementorFrontend !== 'undefined' ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction(
					'frontend/element_ready/goallord-daily-schedule.default',
					function ( $scope ) {
						if ( window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode() ) {
							var roots = $scope[ 0 ].querySelectorAll( SELECTOR );
							for ( var i = 0; i < roots.length; i++ ) {
								reveal( roots[ i ] );
							}
							return;
						}
						init( $scope[ 0 ] );
					}
				);
			}
		} );
	}
} )();
