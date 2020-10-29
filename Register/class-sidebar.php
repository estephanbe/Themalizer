<?php 
namespace BoshDev\Register;

use BoshDev\Helper\tests;


class Sidebar
{
  use tests;
  
  function __construct($init, $args=[])
  {
    $this->isInit_test($init, 'Make sure the "init" argument is instance of INIT class');
    $this->init = $init;
    $this->process_args($args);
    add_action( 'widgets_init', [$this, 'registerSidebar'] );
  }

  public function registerSidebar () 
  {
    register_sidebar( array(
      'name'          => $this->name,
      'id'            => $this->id,
      'description' => $this->description,
      'class' => $this->class,
      'before_widget' => $this->befWid,
      'after_widget'  => $this->aftWid,
      'before_title'  => $this->befTitle,
      'after_title'   => $this->aftTitle,
    ) );
  }

  public function echo ($jQuery=[])
  {
    
    if (is_active_sidebar( $this->id )) {
      dynamic_sidebar( $this->id );
      if (!empty($jQuery)) {
        $this->domScript($jQuery);
      }
    }

  }

  private function domScript($jQuery)
  {
    ?>
    <script>
    jQuery(document).ready(function($) { 
      <?php  
      echo "\r\n";
    
      foreach ($jQuery as $selector => $action) { // lood through jQuery set
        if (!is_array($action)) { // if action is str, then the method has no value like .show()
          echo "$('$selector').$action();\r\n";
        } else { // the action is a method which has value
          foreach ($action as $method => $methodValue) { // loop through each method to apply it on the selector
            if (!is_array($methodValue)) { // if the method value is str, the method has one value;
              echo "$('$selector').$method('$methodValue');\r\n";
            } else { // the method value is an array with a key as the first method value and a value as the second method value
              foreach ($methodValue as $methodFirstArg => $methodSecondArg) {
                echo "$('$selector').$method('$methodFirstArg', '$methodSecondArg');\r\n";
              }              
            }
          } // foreach $action
        } // if !is_array($action)
      } // foreach $jQuery

      ?>
    });
    </script>
    <?php 
  }

  private function process_args ($args)
  {
    $this->empty_test($args, 'please add sidebar args');
    $this->isset_test($args['name'], 'please add the name of your sidebar');
    $this->args = (OBJECT) $args;

    $this->name = $this->args->name;
    $this->id = $this->init->prefix . '_' . strtolower(str_replace(' ', '_', $this->name));

    $this->befWid = isset($this->args->before_widget) ? $this->args->before_widget : '';
    $this->aftWid = isset($this->args->after_widget) ? $this->args->after_widget : '';
    $this->befTitle = isset($this->args->before_title) ? $this->args->before_title : '';
    $this->aftTitle = isset($this->args->after_title) ? $this->args->after_title : '';
    $this->description = isset($this->args->description) ? $this->args->description : '';
    $this->class = isset($this->args->class) ? $this->args->class : '';
  }

}


