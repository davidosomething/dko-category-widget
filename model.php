<?php
/**
 * model.php
 * Get data for the DKO_Category_Widget view.
 */
defined('ABSPATH') or die('Cannot access pages directly.');

$category_slug = $instance['category'];
$category = get_category_by_slug($category_slug);
$parent_category_slug = get_top_parent_category_slug($category->term_id);
if ($category_slug == $parent_category_slug) {
  $category_link = get_permalink(get_page_by_path($category_slug)); // bad assumption, @TODO make work with other permalink structures
}
else {
  $category_link = get_category_link($category->term_id);
}

global $in_feature;
if (!is_array($in_feature)) {
  $in_feature = array();
}
$dko_category_widget_query = new WP_Query(array(
  'posts_per_page'  => 3,
  'category_name'   => $category_slug,
  'post__not_in'    => $in_feature
));

$title = trim(apply_filters('widget_title', $instance['title']));
$widget_title = $title ? $title : $category->name;
