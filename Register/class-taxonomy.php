<?php 
namespace BoshDev\Register;

use BoshDev\Helper\tests;

/**
 * Register New Taxonomy
*/

class Taxonomy
{
  use tests; 

  public $singular;
  public $plural;
  public $slug;
  public $posts_scope = [];
  public $args = [
    'labels' => [],
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => false,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'hierarchical' => true,
    'query_var' => true,
  ];

  
  function __construct($init_class, $provided_args=[])
  {
    $this->isInit_test($init_class, 'Make sure the "init" argument is instance of INIT class');
    $this->process_provided_args($init_class,$provided_args);
    $this->generate_labels($init_class);
    $this->register();
  }

  public function process_provided_args ($init_class, $provided_args) 
  {
    // fill properties
    foreach ($provided_args as $key => $value) {
      if ($key == 'args') { // over ride the value one by one if it's args
        foreach ($value as $arg_key => $arg_value) {
          $this->args[$arg_key] = $arg_value;
        }
      } elseif ($key == 'posts_scope') {
          foreach ($value as $post_slug) {
            array_push($this->posts_scope, $post_slug);
          }
      } elseif ($key == 'singular') {
          $this->singular = $value;
      } elseif ($key == 'plural') {
          $this->plural = $value;
      }
    }

    // check properties
    $this->empty_test($this->singular, 'Empty taxonomy singular name');
    $this->empty_test($this->plural, 'Empty taxonomy plural name');
    $this->empty_test($this->posts_scope, 'Empty taxonomy posts_scope');

    $this->slug = $init_class->prefix . '_' . str_replace(' ', '_', $this->singular) . '_tax'; // create post slug
  }

  public function generate_labels ($init_class)
  {
    $singular = $this->singular;
    $plural = $this->plural;
    $TextDomain = $init_class->textDomain;
    $tax_labels = array(
      "name"                  => _x( $plural, $plural, $TextDomain ),
      "singular_name"         => _x( $singular, $singular, $TextDomain ),
      "search_items"          => __( "Search " . $plural, $TextDomain ),
      "all_items"             => __( "All " . $plural, $TextDomain ),
      "parent_item"           => __( "Parent " . $singular, $TextDomain ),
      "parent_item_colon"     => __( "Parent " . $singular, $TextDomain ),
      "edit_item"             => __( "Edit " . $singular, $TextDomain ),
      "update_item"           => __( "Update " . $singular, $TextDomain ),
      "add_new_item"          => __( "Add New " . $singular, $TextDomain ),
      "new_item_name"         => __( "New " . $singular . " Name", $TextDomain ),
      "add_or_remove_items"   => __( "Add or remove " . $plural, $TextDomain ),
      "choose_from_most_used" => __( "Choose from most used " . $plural, $TextDomain ),
      "menu_name"             => __( $plural, $TextDomain ),
    );

    $this->args['labels'] = $tax_labels;
  }

  public function register (){
    $this->obj = register_taxonomy( $this->slug, $this->posts_scope, $this->args );
  }
 
}


