jQuery(function(){
    jQuery('div.plugin_rating').click(function(e){
        e.preventDefault();



        var rate = jQuery(e.target).data('rating');

        jQuery('div.plugin_rating').load(
            DOKU_BASE+'lib/exe/ajax.php',
            {
                id: JSINFO.id,
                rating: rate,
                call: 'rating'
            }
        );

    });

});