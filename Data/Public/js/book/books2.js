var Books = (function() {

	var transEndEventNames = {
			'WebkitTransition' : 'webkitTransitionEnd',
			'MozTransition' : 'transitionend',
			'OTransition' : 'oTransitionEnd',
			'msTransition' : 'MSTransitionEnd',
			'transition' : 'transitionend'
		}, 
		transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		$books = $( '#bk-list > li > div.bk-book' ), booksCount = $books.length, currentbook = -1; 
	
	function init() {

		$books.each( function( i ) {
			
			var $book = $( this ),
				$other = $books.not( $book ),
				$parent = $book.parent(),
				$page = $book.children( 'div.bk-page' ),
				$content = $page.children( 'div.bk-content' ), current = 0;

			if( i < booksCount / 2 ) {
				$parent.css( 'z-index', i ).data( 'stackval', i );
			}
			else {
				$parent.css( 'z-index', booksCount - 1 - i ).data( 'stackval', booksCount - 1 - i );	
			}

			$book.on( 'click', function() {
				if( currentbook !== -1 && currentbook !== $parent.index() ) {
					closeCurrent();
				}
				if( $book.data( 'opened' ) ) {
					$book.data( 'opened', false ).removeClass( 'bk-viewinside' ).on( transEndEventName, function() {
						$( this ).off( transEndEventName ).removeClass( 'bk-outside' );
						$parent.css( 'z-index', $parent.data( 'stackval' ) );
						currentbook = -1;
						//清除高度
						$(this).find("div").not(".bk-top").not(".bk-bottom").not(".bk-content").height("");
					} );
				}
				else {
					$book.data( 'opened', true ).addClass( 'bk-outside' ).on( transEndEventName, function() {
						$( this ).off( transEndEventName ).addClass( 'bk-viewinside' );
						$parent.css( 'z-index', booksCount );
						currentbook = $parent.index();
						//增加高度
						$(this).find("div").not(".bk-top").not(".bk-bottom").not(".bk-content").height(600);
						/*
						//正面
						$(this).children().find(".bk-cover").height(600);
						//翻开左边
						$(this).children().find(".bk-cover-back").height(600);
						//白色
						$(this).children().eq(1).height(600);
						//白色底面
						$(this).children().eq(2).height(600);
						//背面竖条
						console.log($(this).children().eq(4));
						$(this).children().eq(4).height(600);
						*/

					} );
					current = 0;
					$content.removeClass( 'bk-content-current' ).eq( current ).addClass( 'bk-content-current' );
				}

			} );

			if( $content.length > 1 ) {

				var $navPrev = $( '<span class="bk-page-prev">&lt;</span>' ),
					$navNext = $( '<span class="bk-page-next">&gt;</span>' );
				
				$page.append( $( '<nav></nav>' ).append( $navPrev, $navNext ) );

				$navPrev.on( 'click', function() {
					if( current > 0 ) {
						--current;
						$content.removeClass( 'bk-content-current' ).eq( current ).addClass( 'bk-content-current' );
					}
					return false;
				} );

				$navNext.on( 'click', function() {
					if( current < $content.length - 1 ) {
						++current;
						$content.removeClass( 'bk-content-current' ).eq( current ).addClass( 'bk-content-current' );
					}
					return false;
				} );

			}
			
		} );

	}

	function closeCurrent() {

		var $book = $books.eq( currentbook ),
			$parent = $book.parent();
		
		$book.data( 'opened', false ).removeClass( 'bk-viewinside' ).on( transEndEventName, function(e) {
			$( this ).off( transEndEventName ).removeClass( 'bk-outside' );
			$parent.css( 'z-index', $parent.data( 'stackval' ) );
			//清除高度
			$(this).find("div").not(".bk-top").not(".bk-bottom").not(".bk-content").height("");
		} );

	}

	return { init : init };

})();