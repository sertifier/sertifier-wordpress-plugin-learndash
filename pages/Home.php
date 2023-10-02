<?php

class Sertifier_Credentials_Home {

    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_menu' ));
    }

    public function add_menu(){
        add_submenu_page( 
            'sertifier_home',
            'Home',
            'Home',
            'list_users',
            'sertifier_home',
            array( $this, 'home_page' )
        );
    }

    public function home_page(){
        include(sprintf("%s/templates/home.php", plugin_dir_path( SERTIFIER_FILE )));
    }
}
