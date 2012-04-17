<?php
/**
 * Plugin Name: DKO Category Widget
 * Description: Shows posts from selected category, excluding those in the $dko_category_widget_exclude global variable
 * Version: 1.0
 * Author: David O'Trakoun
 * Author URI: http://www.davidosomething.com
 */

defined('ABSPATH') or die('Cannot access pages directly.');

class DKO_Category_Widget extends WP_Widget
{
  protected $widget = array(
    'name'        => 'DKO Category Widget',
    'description' => 'Select a category from which to display posts.',
    'fields'      => array(
      array(
        'name'  => 'Title',
        'desc'  => 'The category name is the default title, use this if you want to override it.',
        'id'    => 'title',
        'type'  => 'text',
        'std'   => ''
      ),
      array(
        'name'  => 'Category',
        'desc'  => 'Pick the category (or subcategory) to load posts from for this widget.',
        'id'    => 'category',
        'type'  => 'select',
        'options' => array() // set later
      )
    )
  );

  public function __construct() {
    $categories = get_categories(array(
      'hide_empty' => 0
    ));
    foreach ($categories as $category) {
      $this->widget['fields'][1]['options'][$category->slug] = $category->name;
    }

    parent::__construct(
      get_class($this),
      $this->widget['name'],
      array('description' => $this->widget['description'])
    );
  }

  /**
   * Widget View
   * @param array $sidebar
   * @param array $params
   */
  function widget($args, $instance) {
    extract($args);

    if (!function_exists('get_top_parent_category_slug')) {
      function get_top_parent_category_slug($cat) {
        $curr_cat = get_category_parents($cat, false, '/' ,true);
        $curr_cat = explode('/', $curr_cat);
        return $curr_cat[0];
      }
    }

    if (!function_exists('get_single_tag')) {
      function get_single_tag($post_id = '') {
        global $post;
        if (!$post_id) {
          $post_id = $post->ID;
        }
        $tags = get_the_tags($post_id);
        if (!is_array($tags)) {
          return '';
        }

        $tag = array_shift($tags);
        return $tag;
      }
    }

    require 'model.php';
    if ($dko_category_widget_query->have_posts()) {
      echo $args['before_widget'];
      require 'view.php';
      echo $args['after_widget'];
    }
  }

  /**
   * Administration Form
   * @param array $instance
   * @return boolean
   */
  function form($instance) {
    require 'form.php';
  }

  /**
   * Update the Administrative parameters
   * @param array $new_instance
   * @param array $old_instance
   * @return array
   */
  function update($new_instance, $old_instance) {
    $instance = wp_parse_args($new_instance, $old_instance);
    return $instance;
  }
} // DKO_Category_Widget()

add_action('widgets_init', function() { return register_widget("DKO_Category_Widget"); });
