<?php

return [
    [
        'key' => 'IMUSIC',
        'store' => 'iMusic',
        'format_name_lp' => 'LP', // What does webshop see as vinyl?
        'product_page_url' => 'https://www.imusic.nl',
        'shipping' => 5,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://imusic.nl/vinyl/search?_form=searchForm&advanced=1&combined=[SEARCH_ARTIST]&artist=&title=&tracks=&composer=&released=&releaseDate=&label=&releaseCountryId=&releaseCode=&genreId=&mediaGroupId=6&mediaId=&languageId=&subtitleId=&priceRange=&price=&discountPercent=&sort=relevance&search=',

        // Get all result items (cards) after search
        'search_items' => '<div class="media-body"|||fa-shopping-cart"></span>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => '<h5 class="media-heading">|||</h5>',
            'search_item_album' => '<h4 class="media-heading">|||</h4>',
            'search_item_price' => ' class="btn btn-sm btn-success price">|||</button>',
            'search_item_format' => '<acronym title="|||</acronym>',
            'search_item_page' => '<a href="|||"',
        ],
    ],
    [
        'key' => 'PLATO',
        'store' => 'Plato Mania',
        'format_name_lp' => 'LP', // What does webshop see as vinyl?
        'product_page_url' => 'https://www.platomania.nl',
        'shipping' => 5.95,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://www.platomania.nl/search/results/?q=[SEARCH_ARTIST]+[SEARCH_ALBUM]&format=vinyl',

        // Get all result items (cards) after search
        'search_items' => '<article class="|||</article>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => '<h1 class="product-card__artist">|||</h1>',
            'search_item_album' => '<h2 class="product-card__title">|||</h2>',
            'search_item_price' => '<div class="article__price" money="true">|||</div>',
            'search_item_format' => '<div class="article__medium">|||</div>',
            'search_item_page' => '<div class="article__image-container">|||<div class="article__image' . "\r\n" . '<a href="|||">',
        ],
    ],
    [
        'key' => 'SOUNDS',
        'store' => 'Sounds',
        'format_name_lp' => 'LP', // What does webshop see as vinyl?
        'product_page_url' => 'https://www.soundsdelft.nl',
        'shipping' => 6,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://www.soundsdelft.nl/search/[SEARCH_ARTIST]+[SEARCH_ALBUM]/1/rel/lp',

        // Get all result items (cards) after search
        'search_items' => 'search-product|||Winkelwagentje',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => '<h5>||| - ',
            'search_item_album' => '<h5>|||</h5>' . "\r\n" . '">|||</a>',
            'search_item_price' => 'product-price">|||</span>',
            'search_item_format' => '<div class="product-info">|||</div>',
            'search_item_page' => 'product-link">|||</a>' . "\r\n" . '<a href="|||">',
        ],
    ],
    [
        // Large single product page
        'key' => 'LARGE',
        'store' => 'Large',
        'format_name_lp' => 'LP', // What does webshop see as vinyl?
        'product_page_url' => 'https://www.large.nl',
        'shipping' => 0,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://www.large.nl/search?q=[SEARCH_ARTIST]%20[SEARCH_ALBUM]%20Vinyl',

        // Get all result items (cards) after search
        'search_items' => 'js-product-details-container|||data-masterid',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => 'product-name|||</div>' . "\r\n" . '<span class="bold">|||</span>',
            'search_item_album' => '<h5>|||</h5>' . "\r\n" . '">|||</a>',
            'search_item_price' => 'product-price">|||</span>',
            'search_item_format' => '<div class="product-info">|||</div>',
            'search_item_page' => 'product-link">|||</a>' . "\r\n" . '<a href="|||">',
        ],
        'product' => [
            'search_item_artist' => '" title="Meer van |||"',
            'search_item_album' => '<meta property="og:title" content="|||" />',
            'search_item_price' => 'currentprice">|||</span>',
            'search_item_format' => 'data-product-type="|||"',
            'search_item_page' => '<meta property="og:url" content="|||"',
        ],
    ],
    [
        'key' => 'KROESE',
        'store' => 'Kroese Online',
        'format_name_lp' => 'LP', // What does webshop see as vinyl?
        'product_page_url' => 'https://www.kroese-online.nl',
        'shipping' => 5,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://www.kroese-online.nl/zoekresultaat/?Artist=[SEARCH_ARTIST]',
        // 'search_url_cd' => 'https://www.kroese-online.nl/zoekresultaat/?Artist=[SEARCH_ARTIST]',

        // Get all result items (cards) after search
        'search_items' => '<tr|||</tr>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => 'SearchFilterArtist|||</td>' . "\r\n" . '">|||</a>',
            'search_item_album' => 'TitelLink|||</td>' . "\r\n" . '">|||</a>',
            'search_item_price' => 'Price Clickable|||td>' . "\r\n" . '">|||</',
            'search_item_format' => 'Format Clickable|||td>' . "\r\n" . '">|||</',
            'search_item_page' => 'TitelLink|||</td>' . "\r\n" . "href='|||'",
        ],
    ],

    [
        'key' => 'FUN',
        'store' => 'Fun Records',
        'format_name_cd' => 'CD', // What does webshop see as CD?
        'product_page_url' => 'https://www.funrecords.de',
        'shipping' => 3,
        'local_scrape' => false,

        // Search pages
        'search_url_cd' => 'https://www.funrecords.de/en/catalogue/?search=[SEARCH_ARTIST]+[SEARCH_ALBUM]&mode=0&types%5B%5D=1',

        // Get all result items (cards) after search
        'search_items' => '<li id="item|||</li>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => ' alt="|||,',
            'search_item_album' => 'alt="|||"' . "\r\n" . ', |||(',
            'search_item_price' => 'class="col_right_25"><p>||| EUR ',
            'search_item_format' => '</a></span></p><p>|||</p>',
            'search_item_page' => '<a href="|||"',
        ],
    ],
    [
        'key' => 'GETBACK',
        'store' => 'GetBackMusic',
        'format_name_lp' => '', // What does webshop see as vinyl? BOTH
        'product_page_url' => 'https://www.getbackmusic.nl',
        'shipping' => 3,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://www.getbackmusic.nl/search?q=[SEARCH_ARTIST]+[SEARCH_ALBUM]&options%5Bprefix%5D=last',

        // Get all result items (cards) after search
        'search_items' => '<li class="list-view-item">|||</li>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => '<span class="product-card__title">|||-',
            'search_item_album' => '<span class="product-card__title">|||/span>' . "\r\n" . '-|||<',
            'search_item_price' => '<span class="price-item price-item--sale">|||</span>',
            'search_item_format' => '<span class="product-card__title">|||/span>' . "\r\n" . '-|||<',
            'search_item_page' => '<a class="full-width-link" href="|||?',
        ],
    ],

    [
        'key' => 'NOVIO',
        'store' => 'Novio Music',
        'format_name_cd' => 'CD', // What does webshop see as vinyl? BOTH
        'product_page_url' => 'https://noviomusic.nl/',
        'shipping' => 3,
        'local_scrape' => false,

        // Search pages
        'search_url_cd' => 'https://noviomusic.nl/?s=[SEARCH_ARTIST]+[SEARCH_ALBUM]&post_type=product',

        // Get all result items (cards) after search
        'search_items' => '<li class="product type-product|||</li>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => '<h2 class="woocommerce-loop-product__title">||| &#8211;',
            'search_item_album' => '<h2 class="woocommerce-loop-product__title">|||/h2>' . "\r\n" . '&#8211; |||<',
            'search_item_price' => '<bdi>|||</bdi>',
            'search_item_format' => 'FIXED:cd',
            'search_item_page' => '<a href="|||"',
        ],
    ],

    [
        'key' => 'MEDIMOPS',
        'store' => 'Medi Mops',
        'format_name_cd' => 'CD', // What does webshop see as vinyl? BOTH
        'product_page_url' => 'https://medimops.de/',
        'shipping' => 3,
        'local_scrape' => true,

        // Search pages
        'search_url_cd' => 'https://www.medimops.de/musik-C0255882/Audio%20CD,/?searchparam=[SEARCH_ARTIST]+[SEARCH_ALBUM]&fcIsSearch=1',

        // Get all result items (cards) after search
        'search_items' => 'data-testid="product-container|||ProductSearch_moreDetails___1lat ',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => 'Von<!-- --> <!--$-->|||</a>',
            'search_item_album' => '<div class="ProductSearch_title|||</div>',
            'search_item_price' => 'product-price-display-mobile">|||</span>',
            'search_item_format' => 'FIXED:cd',
            'search_item_page' => '<a href="|||">',
        ],
    ],

    [
        'key' => 'IMPERICON',
        'store' => 'Impericon',
        'format_name_lp' => 'Vinyl', // What does webshop see as vinyl? BOTH
        'product_page_url' => 'https://www.impericon.com',
        'shipping' => 3,
        'local_scrape' => false,

        // Search pages
        'search_url_lp' => 'https://www.impericon.com/nl/search?q=[SEARCH_ARTIST]+[SEARCH_ALBUM]+vinyl',

        // Get all result items (cards) after search
        'search_items' => '<product-item class="product-item">|||</product-item>',

        // Get some fields from these item (card)
        'results' => [
            'search_item_artist' => 'class="product-item-meta__title">||| - ',
            'search_item_album' => 'class="product-item-meta__title">|||/a>' . "\r\n" . '- |||<',
            'search_item_price' => 'Aanbiedingsprijs</span>|||</span>',
            'search_item_format' => 'class="product-item-meta__title">|||/a>' . "\r\n" . '- |||<',
            'search_item_page' => '<div class="product-item-meta"><a href="|||">',
        ],
    ],

];
