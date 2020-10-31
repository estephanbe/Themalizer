<?php 
namespace Themalizer\Register;

use Helper\tests;
/**
 * Register Post Types

These args where not included in the code, it will be listed here for further review:
- rest_base
- rest_controller_class
- menu_icon
- capabilities
- map_meta_cap
- register_meta_box_cb
*/

class PostType
{
  use tests; 

  public $singular;
  public $plural;
  public $slug;
  public $description;
  public $args = array(
    'public'              => true,
    'hierarchical'        => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'show_in_rest'        => true,
    'menu_position'       => 5,
    'menu_icon'           => null,
    'capability_type'     => 'post',
    'supports'            => array(
      'title',
      'editor',
      'author',
      'thumbnail',
      'excerpt',
      'custom-fields',
      'trackbacks',
      'comments',
      'revisions',
      'page-attributes',
      'post-formats',
    ),
    'taxonomies' => array('post_tag'),
    'has_archive'         => true,
    'rewrite'             => true,
    'query_var'           => true,
    'can_export'          => true
  );

  
  function __construct($init_class, $provided_args=[])
  {
    $this->isInit_test($init_class, 'Make sure the "init" argument is instance of INIT class');
    $this->process_args($init_class, $provided_args);
    $this->add_lables_to_args($init_class);
    $this->register();
  }

  public function process_args ($init_class, $provided_args)
  {
    $default_taxes = $this->args['taxonomies']; // save the default taxonomies to add them later on again. 

    // fill properties
    foreach ($provided_args as $key => $value) {
      if ($key == 'args') { // over ride the value one by one if it's args
        foreach ($value as $arg_key => $arg_value) {
          $this->args[$arg_key] = $arg_value;
        }
      } elseif ($key == 'singular') {
        $this->singular = $value;
      } elseif ($key == 'plural') {
        $this->plural = $value;
      } elseif ($key == 'description') {
        $this->description = $value;
      }
    }

    $this->args['taxonomies'] = array_merge($this->args['taxonomies'], $default_taxes); // add the default taxes to the provided taxes

    // check properties
    $this->empty_test($this->singular, 'Empty post singular name');
    $this->empty_test($this->plural, 'Empty post plural name');
    $this->empty_test($this->description, 'Empty post description');

    $this->args['description'] = $this->description; // add the description to the args array

    $this->slug = $init_class->prefix . '_' . str_replace(' ', '_', $this->singular); // create post slug
  }

  public function labels ($singular, $plurar, $textDomain)
  {
    return array(
      'name'               => __( "$plurar", "$textDomain" ),
      "singular_name"      => __( "$singular", "$textDomain" ),
      "add_new"            => _x( "Add New $singular", "$textDomain", "$textDomain" ),
      "add_new_item"       => __( "Add New $singular", "$textDomain" ),
      "edit_item"          => __( "Edit $singular", "$textDomain" ),
      "new_item"           => __( "New $singular", "$textDomain" ),
      "view_item"          => __( "View $singular", "$textDomain" ),
      "search_items"       => __( "Search $plurar", "$textDomain" ),
      "not_found"          => __( "No $plurar found", "$textDomain" ),
      "not_found_in_trash" => __( "No $plurar found in Trash", "$textDomain" ),
      "all_items" => __( "All $plurar", "$textDomain" ),
      "archives" => __( "$singular Archives", "$textDomain" ),
      "parent_item_colon"  => __( "Parent $singular:", "$textDomain" ),
      "menu_name"          => __( "$plurar", "$textDomain" ),
    );
  }

  public function add_lables_to_args ($init_class)
  {
    $labels = $this->labels($this->singular, $this->plural, $init_class->textDomain);
    $this->args['labels'] = $labels;
  }

  public function register_post_type() 
  {
    register_post_type($this->slug, $this->args);
  }

  public function get_post_obj () {
    $this->obj = get_post_type_object( $this->slug );
  }

  public function register(){
    add_action('init', [$this, 'register_post_type']);
    add_action('wp', [$this, 'get_post_obj']);
  }
}


