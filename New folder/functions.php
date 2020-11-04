<?php

include 'Themalizer/autoload.php';


Themalizer::init(array(
    'prefix' => 'tlizer',
    'customizer_panel' => array(
        'title' => 'Themalizer Panle',
        'description' => 'Theme customization panel'
    )
));







var_dump(Themalizer::get_container());
