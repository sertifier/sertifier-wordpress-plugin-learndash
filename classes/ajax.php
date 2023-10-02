<?php

class Sertifier_Ajax {

    private $api;

    public function __construct(){
        add_action( 'wp_ajax_get_lessons', array( $this, 'get_lessons' ) );
        add_action( 'wp_ajax_get_users', array( $this, 'get_users' ) );
        
        require_once( plugin_dir_path( SERTIFIER_FILE ) . "/classes/api.php" );
        $this->api = new Sertifier_Api(get_option("sertifier_api_key"));
    }

    public function get_lessons(){
        global $wpdb;
        $course_id = sanitize_text_field($_POST["course_id"]);
		if ( ! function_exists( 'learndash_get_course_lessons_list' ) ) {
			return;
		}

        echo json_encode(learndash_get_course_lessons_list($course_id));
        wp_die(); 
    }

    public function get_users(){
        map_deep($_POST, 'sanitize_text_field');

        global $wpdb;
		
        if(isset($_POST["course_id"]) && !empty($_POST["course_id"])){
			if ( ! function_exists( 'learndash_get_groups_users' ) ) {
				echo "learndash_get_groups_users";
				return;
			}
			$users = learndash_get_groups_users(sanitize_text_field($_POST["course_id"]));
        }else {
			$users = get_users();
		}

        $selectedUserIds = [];

        if(isset($_POST["delivery_id"]) && !empty(@$_POST["delivery_id"])){
            $alreadyRecipients = $this->api->get_recipients($_POST["delivery_id"])->data->recipients;
            $alreadyRecipientsEmails = array_column($alreadyRecipients, "email");
            foreach ($users as $user) {
                if(array_search($user->user_email, $alreadyRecipientsEmails) !== false){
                    $selectedUserIds[] = $user->ID;
                }
            }
        }

        echo json_encode([
            "users" => $users,
            "selectedUserIds" => $selectedUserIds,
        ]);
        wp_die(); 
    }
}