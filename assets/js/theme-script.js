;var Rvdx_Theme_JS;

(function($) {
	'use strict';

	Rvdx_Theme_JS = {

		$window: $( window ),

		$body: $( 'body' ),

		threshold: 768,

		init: function() {
			this.page_preloader_init();
			this.eventsInit();
			this.toTopInit();
			this.responsiveMenuInit();
			this.magnificPopupInit();
			this.swiperInit();
			this.mobilePanelInit();
			this.select2();
			this.jwTestimonialsThumb();
			this.CanvasLineSlider();
			this.headerEllipsBtn();
			this.headerScroll();
		},

		page_preloader_init: function(self) {
			if ($('.page-preloader-cover')[0]) {
				$( window ).on('load', function () {
					$( 'body' ).removeClass( 'website-loading' );

					if ( "onanimationend" in window && 'ontransitionend' in window ) {
						$('.page-preloader-cover')
							.on( 'animationend', removePreLoader )
							.on( 'transitionend', removePreLoader )
							.addClass( 'hide-loader' );
					} else {
						$('.page-preloader-cover').fadeTo(500, 0, removePreLoader);
					}

					function removePreLoader(){
						$( this ).remove();
					}
				});
			}
		},

		toTopInit: function() {
			if ($.isFunction(jQuery.fn.UItoTop)) {
				$().UItoTop({
					text: '',
					scrollSpeed: 600
				});
			}
		},

		responsiveMenuInit: function() {
			$( '.site-header .main-navigation' ).RvdxMenu( { 'threshold' : Rvdx_Theme_JS.threshold } );
		},

		magnificPopupInit: function() {

			if (typeof $.magnificPopup !== 'undefined') {

				//MagnificPopup init
				$('[data-popup="magnificPopup"]').magnificPopup({
					type: 'image'
				});

				$(".gallery > .gallery-item a").filter("[href$='.png'],[href$='.jpg']").magnificPopup({
					type: 'image',
					gallery: {
						enabled: true,
						navigateByImgClick: true,
					},
				});

			}
		},

		swiperInit: function() {
			if (typeof Swiper !== 'undefined') {

				//Swiper carousel init
				var mySwiper = new Swiper('.swiper-container', {
					// Optional parameters
					loop: true,
					spaceBetween: 10,

					// Navigation arrows
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
				})

			}
		},

		mobilePanelInit: function() {
			var $mobilePanel = $( '.rvdx-mobile-panel' ),
				$body        = $( 'body' );

			if ( ! $mobilePanel[0] ) {
				return false;
			}

			var $manuToggle    = $( '.rvdx-mobile-panel__control--mobile-menu', $mobilePanel ),
				$sidebarToggle = $( '.rvdx-mobile-panel__control--sidebar', $mobilePanel );

			$sidebarToggle.on( 'click.rvdx-mobile-panel', function(){
				var toggle = $(this);

				$( '.active', $mobilePanel ).not( toggle ).removeClass( 'active' );
				toggle.toggleClass( 'active' );

				$body.removeClass( 'mobile-menu-visible' ).toggleClass( 'sidebar-visible' );
			} )

			if( $('.rx-mobile-menu')[0] && ! $('.rx-menu-on-mobile-panel.rx-mobile-menu')[0] ){
				$manuToggle.remove();
				return false;
			}

			$manuToggle.on( 'click.rvdx-mobile-panel', function(){
				var toggle = $( this ),
					iconHolder = $( 'i', toggle ),
					icon = iconHolder.attr( 'class' ) ===  iconHolder.data( 'icon' ) ? iconHolder.data( 'close-icon' ) : iconHolder.data( 'icon' ) ;

				$( '.active', $mobilePanel ).not( toggle ).removeClass( 'active' );
				toggle.toggleClass( 'active' );

				$('i', toggle).attr( 'class', icon );

				$body.removeClass( 'sidebar-visible' ).toggleClass( 'mobile-menu-visible' );
			} )
		},

		eventsInit: function() {
			Rvdx_Theme_JS.$window.on( 'resize.RvdxTheme orientationchange.RvdxTheme', Rvdx_Theme_JS.debounce( 50, Rvdx_Theme_JS.watcher.bind( this ) ) ).trigger( 'resize.RvdxTheme' );
		},

		select2: function() {
			var selector = 'select:not([data-type="colorpicker"])';

			if( typeof( elementor ) !== 'undefined' ){
				$( window ).on( 'elementor/frontend/init', function() {
					elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ) {
						$( selector ).select2({
							minimumResultsForSearch: -1
						});
					} );
				} )
			}else{
				$( selector ).select2({
					minimumResultsForSearch: -1
				});
			}
		},
		/**
		 * Resize Event Watcher callback.
		 *
		 * @param  {Object} Resize or Orientationchange event.
		 * @return {void}
		 */
		watcher: function( event ) {

			if ( Rvdx_Theme_JS.isThreshold() ) {
				Rvdx_Theme_JS.$body.addClass( 'mobile-layout' );
				Rvdx_Theme_JS.$body.removeClass( 'desktop-layout' );
			} else {
				Rvdx_Theme_JS.$body.addClass( 'desktop-layout' );
				Rvdx_Theme_JS.$body.removeClass( 'mobile-layout' );
			}
		},

		/**
		 * Get mobile status.
		 *
		 * @return {boolean} Mobile Status
		 */
		isThreshold: function() {
			return ( Rvdx_Theme_JS.$window.width() < Rvdx_Theme_JS.threshold ) ? true : false;
		},

		/**
		 * Debounce the function call
		 *
		 * @param  {number}   threshold The delay.
		 * @param  {Function} callback  The function.
		 */
		debounce: function ( threshold, callback ) {
			var timeout;

			return function debounced( $event ) {
				function delayed() {
					callback.call( this, $event );
					timeout = null;
				}

				if ( timeout ) {
					clearTimeout( timeout );
				}

				timeout = setTimeout( delayed, threshold );
			};
		},

		jwTestimonialsThumb: function(){
			$('.jw-testimonials-thumbnails').slick({
				slidesToShow: 3,
				slidesToScroll: 1,
				asNavFor: '.jw-testimonials__instance',
				focusOnSelect: true
			});
		},

		/* Canvas line slider home page */
		CanvasLineSlider: function(){
			var wave = $('.waves-line')[0],
				revslider = $('rs-module'),
				sineWave;

			if( wave ){
				sineWave = new SineWaves({
					el: wave,
					speed: wave.getAttribute('data-speed') || 5,
					width: function() {
						return $(window).width();
					},

					height: function() {
						return $(window).height();
					},

					ease: wave.getAttribute('data-animation') || 'SineInOut',
					wavesWidth: wave.getAttribute('data-wave-width') || '150%',
					waves: [
					{
						timeModifier: 0.6,
						lineWidth: 5,
						amplitude: -200,
						wavelength: 200
					},
					{
						timeModifier: 0.13,
						lineWidth: 5,
						amplitude: -300,
						wavelength: 300
					}
					],

					// Called on window resize
					resizeEvent: function() {
						var gradient1 = this.ctx.createLinearGradient(0, 0, this.width, 0);
						gradient1.addColorStop(0,"rgba(0, 172, 238, 1)");
						gradient1.addColorStop(0.54,"rgba(239, 165, 6, 1)");
						gradient1.addColorStop(1,"rgba(236, 57, 139, 1)");

						var gradient2 = this.ctx.createLinearGradient(0, 0, this.width, 0);
						gradient2.addColorStop(0,"rgba(32, 171, 208, 1)");
						gradient2.addColorStop(0.50,"rgba(83, 72, 182, 1)");
						gradient2.addColorStop(1,"rgba(234, 8, 140, 1)");

						var index = -1;
						var length = this.waves.length;
						while(++index < length){
							if ( index === 0 ) {
								this.waves[index].strokeStyle = gradient1;
							}
							else {
								this.waves[index].strokeStyle = gradient2;
							}
						}

						// Clean Up
						index = void 0;
						length = void 0;
						gradient1 = void 0;
						gradient2 = void 0;
					}
				});
				$( window ).on( 'scroll.sineWaves', function( e ){
					if( $( window ).scrollTop() > revslider.height() && sineWave.running === true ){
						sineWave.running = false;
						sineWave.update();
					} else if ( $( window ).scrollTop() < revslider.height() && sineWave.running === false ) {
						sineWave.running = true;
						sineWave.update();
					}

				}).trigger('scroll.sineWaves');
			}
		},

		/* Header open btn on mobile */
		headerEllipsBtn: function(){
			$('.btn-header-ellips .elementor-icon').on('click', function(){
				$(this).toggleClass('active');
				$('.header-btn').toggleClass('active');
			});
		},

		/* ---------------------- SCROLL HEADER  --------------------- */
		headerScroll: function(){
			if( !$('.rx-stick-header')[0] ){
				var $window = $(window),
				lastScrollTop = 0,
				st;
				$( window ).on( 'scroll.mySite', function(){
					st = $(this).scrollTop();

					$('header#masthead .header-sticky')[ 0 < st ? 'addClass' : 'removeClass' ]('header-top');

					if ( st < lastScrollTop || $window.width() < 992){
						$('header#masthead .header-sticky')[ 0 === st ? 'removeClass' : 'addClass' ]('header-scroll');
					} else {
						$('header#masthead .header-sticky').removeClass('header-scroll');
					}
					lastScrollTop = st;
				}).trigger('scroll.mySite');
			}
		},


	};


	Rvdx_Theme_JS.init();

}(jQuery));
