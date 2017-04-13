//Genre Ajax Filtering
jQuery(function($)
{
    // Get the post_type and taxo on load
    var post_type = $(this).find('#post_type').val();
    var taxo = $(this).find('#taxo').val();

    genre_get_posts();


    //If list item is clicked, trigger input change and add css class
    $('#genre-filter a').live('click', function(){
        var input = $(this).find('input');

        //Check if clear all was clicked
        if ( $(this).hasClass('clear-all') )
        {
            $('#genre-filter a').addClass('selected'); //Clear settings
            $('#genre-filter a input').prop('checked', true);
            genre_get_posts(); //Load Posts
        }
        else if (input.is(':checked'))
        {
            input.prop('checked', false);
            $(this).removeClass('selected');
        } else {
            input.prop('checked', true);
            $(this).addClass('selected');
        }
 
        input.trigger("change");
    });
 
    //If input is changed, load posts
    $('#genre-filter input').live('change', function(){
        genre_get_posts(); //Load Posts
    });
 
    //Find Selected Genres
    function getSelectedGenres()
    {
        var genres = []; //Setup empty array
 
        $("#genre-filter a input:checked").each(function() {
            var val = $(this).val();
            genres.push(val); //Push value onto array
        });     
 
        return genres; //Return all of the selected genres in an array
    }
 
    //If pagination is clicked, load correct posts
    $('.genre-filter-navigation a').live('click', function(e){
        e.preventDefault();
 
        var url = $(this).attr('href'); //Grab the URL destination as a string
        var paged = url.split('&paged='); //Split the string at the occurance of &paged=
 
        genre_get_posts(paged[1]); //Load Posts (feed in paged value)
    });
 
    //Main ajax function
    function genre_get_posts(paged)
    {
        var paged_value = paged; //Store the paged value if it's being sent through when the function is called
        var ajax_url = ajax_genre_params.ajax_url; //Get ajax url (added through wp_localize_script)
 
        $.ajax({
            type: 'GET',
            url: ajax_url,
            data: {
                action: 'genre_filter',
                post_type: post_type,
                taxo: taxo,
                genres: getSelectedGenres, //Get array of values from previous function
                paged: paged_value //If paged value is being sent through with function call, store here
            },
            beforeSend: function ()
            {
               $('.ajax-post-loader img').fadeIn("slow");
            },
            success: function(data)
            {
                $('#genre-results').show("slow").html(data).fadeIn("slow");
                $('.ajax-post-loader img').fadeOut("slow");
            },
            error: function()
            {
                                //If an ajax error has occured, do something here...
                $("#genre-results").html('<p>Rien ici!</p>');
            }
        });
    }
 
});