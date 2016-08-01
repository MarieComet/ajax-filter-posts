Plugin Name: Ajax Filter Posts
Description: Filter posts by taxonomy with ajax, css based on bootsrap but do what you want.
Version:     1.1
Author:      Marie Comet

This Plugin create simple ajax filters for any post type on your archive, blog pages...
You need to include this in your template :

$new_posts_filter = new Ajax_Filter_Posts();
echo $new_posts_filter->get_genre_filters('post', 'category');

Where the first argument is your post_type slug and the second argument is your taxonomy slug.
