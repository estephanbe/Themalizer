<?php
namespace BoshDev\Helper;


trait widgets {
    
    public function isset_test($var, $msg)
    {
        if (!isset($var)) {
          throw new \Exception($msg);
        }
    }

    public function empty_test($var, $msg)
    {
        if (empty($var)) {
          throw new \Exception($msg);
        }
    }

    public function empty_isset_test($var, $msg)
    {
        $this->isset_test($var, $msg);
        $this->empty_test($var, $msg);
    }

    public function t_check_empty($instance, $default='')
    {
        if (empty($default)) {
            $newVar = !empty( $instance ) ? $instance : '';
        } else {
            $newVar = !empty( $instance ) ? $instance : esc_html__($default, $this->textDomain);
        }
        return $newVar;
    }

    public function t_echo_url ($instance)
    {
        echo esc_url($instance);
    }

    public function t_echo_text ($instance)
    {
        echo esc_html__($instance);
    }

    public function t_echo_attr ($value, $switch='')
    {
        switch ($switch) {
            case 'id':
                echo esc_attr($this->get_field_id($value));
                break;
            case 'name':
                echo esc_attr($this->get_field_name($value));
                break;
            case 'labelValue':
                esc_attr_e( $value, $this->textDomain );
                break;
            
            default:
                echo esc_attr( $value );
                break;
        }
    }

    public function t_form_text_input ($id, $label, $instance, $default='')
    { 
        $this->empty_test($id, 'Instance id is empty, please fill it in your form method for your text input field');
        $this->empty_test($label, 'Field label is empty, please fill it in your form method for your text input field');

        $instance = $this->t_check_empty($instance[$id], $default);

    ?>
        <p>
            <label 
              for="<?php $this->t_echo_attr($id, 'id'); ?>">
                <?php $this->t_echo_attr($label, 'labelValue'); ?>
            </label> 

            <input 
                class="bod_homePageVerse_widget widefat" 
                id="<?php $this->t_echo_attr($id, 'id'); ?>" 
                name="<?php $this->t_echo_attr($id, 'name'); ?>" 
                type="text" 
                value="<?php $this->t_echo_attr($instance); ?>"
            >
        </p>

    <?php
    }

    public function t_form_url_input ($id, $label, $instance)
    { 
        $this->empty_test($id, 'Instance id is empty, please fill it in your form method for your text input field');
        $this->empty_test($label, 'Field label is empty, please fill it in your form method for your text input field');

        $instance = $this->t_check_empty($instance[$id]);

    ?>
        <p>
            <label 
              for="<?php $this->t_echo_attr($id, 'id'); ?>">
                <?php $this->t_echo_attr($label, 'labelValue'); ?>
            </label> 

            <input 
                class="bod_homePageVerse_widget widefat" 
                id="<?php $this->t_echo_attr($id, 'id'); ?>" 
                name="<?php $this->t_echo_attr($id, 'name'); ?>" 
                type="text" 
                value="<?php $this->t_echo_attr($instance); ?>"
            >
        </p>

    <?php
    }

    public function t_form_image_input ($id, $label, $instance)
    { 
        $this->empty_test($id, 'Instance id is empty, please fill it in your form method for your text input field');
        $this->empty_test($label, 'Field label is empty, please fill it in your form method for your text input field');

        $instance = $this->t_check_empty($instance[$id]);

    ?>
        <p>
            <label 
                for="<?php $this->t_echo_attr($id, 'id'); ?>">
                    <?php $this->t_echo_attr($label, 'labelValue'); ?>
            </label>

            <img 
                class="<?php $this->t_echo_attr($id, 'id'); ?>_img" 
                src="<?= $instance; ?>" 
                style="margin:0;padding:0;max-width:100%;display:block"
            />

            <input 
                type="text" 
                class="widefat <?php $this->t_echo_attr($id, 'id'); ?>_url" 
                name="<?php $this->t_echo_attr($id, 'name'); ?>" 
                value="<?= $instance; ?>" 
                style="margin-top:5px;" 
            />

            <input 
                type="button" 
                class="button button-primary js_custom_upload_media" 
                id="<?php $this->t_echo_attr($id, 'id'); ?>"  
                value="Upload Image" 
                style="margin-top:5px;" 
            />
        </p>

    <?php
    }

    public function t_update($new_instance, $old_instance, $ids=[])
    {
        $instance = array();
        $this->empty_test($ids, 'Please fill the ids in your update function for your widget');

        foreach ($ids as $type => $idValues) {
            if ($type == 'text') {
                foreach ($idValues as $id) {
                    $instance[$id] = ( ! empty( $new_instance[$id] ) ) ? sanitize_text_field( $new_instance[$id] ) : '';
                }
            } elseif ($type == 'image') {
                foreach ($idValues as $id) {
                    $instance[$id] = ( ! empty( $new_instance[$id] ) ) ? strip_tags( $new_instance[$id] ) : '';
                }
            } elseif ($type == 'url') {
                foreach ($idValues as $id) {
                    $instance[$id] = ( ! empty( $new_instance[$id] ) ) ? esc_url_raw( $new_instance[$id] ) : '';
                }
            }
        }

        return $instance;
    }



}