<?php
$meet_with_amir = new mwa_functions();

class mwa_functions
{
	//~~~~~
	function __construct ()
	{

		$this->addWPActions();

	}



/*	---------------------------
	PRIMARY HOOKS INTO WP
	--------------------------- */
	function addWPActions ()
	{

        // Register Shortcode
        add_shortcode( 'mwa-home', array( 'mwa_draw', 'draw_homepage' ) );
        add_shortcode( 'amir-dash', array( 'mwa_draw', 'draw_amir_dashboard' ) );

        // Check for other custom actions
        add_action('init', array($this, 'check_for_actions') );

		// Create Media Admin Pages
		//add_action( 'admin_menu', array( $this, 'create_AdminPages' ));

		// Check folder exists
		//add_action('init', array($this, 'checkFolderExists') );

        //Add Front End Jquery and CSS
        add_action( 'wp_footer', array( $this, 'frontendEnqueues' ) );


	}



    function frontendEnqueues ()
    {


        // Register Ajax script for front end
        wp_enqueue_script('mwa_scripts', MWA_PLUGIN_URL.'/js/scripts.js', array( 'jquery' ) ); #Custom AJAX functions
        wp_enqueue_style('mwa_styles', MWA_PLUGIN_URL.'/css/styles.css', '', '1.1' );

        //Localise the JS file
        /*
        $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => wp_create_nonce('mwa_ajax_nonce')
        );
        wp_localize_script( 'mwa_scripts', 'mwa_frontend_ajax', $params );
        */





    }

    function check_for_actions()
    {
        if(isset($_GET['action']) )
        {

            $my_action = $_GET['action'];

            switch ($my_action)
            {
                case "mwa_make_booking":


                    $feedback = mwa_actions::process_booking();

                    $feedback_msg = $feedback['message'];
                    $feedback_type = $feedback['type'];

                    // PRG prevey re submission of form
                    $redirectURL = '?feedback='.$feedback_msg.'&feedback_type='.$feedback_type;
                    wp_redirect($redirectURL);
                    exit();
                break;

                case "mwa_delete_booking":


                    $feedback = mwa_actions::booking_delete();

                    // Execute code (such as database updates) here.
                    // PRG prevey re submission of form

                    $redirectURL = '?feedback='.$feedback;
                    wp_redirect($redirectURL);
                    exit();
                break;


            }
        }



    }

    public static function draw_feebdack()
    {

        $html = '';
        // Hadnle any feedback messages
        if(isset($_GET['feedback']) )
        {
            $feedback = $_GET['feedback'];

            // Firstly check if there is anyt feedback
            if(isset($_GET['errormsg']) )
            {
                $html =  imperialNetworkDraw::imperialFeedback($_GET['errormsg'], 'error');
            }
            else
            {

                switch ($feedback)
                {
                    case "note_deleted":
                        $html= imperialNetworkDraw::imperialFeedback("Note deleted");
                    break;

                    case "note_add":
                        $html= imperialNetworkDraw::imperialFeedback("Note Added");
                    break;

                    case "note_edit":
                         $html= imperialNetworkDraw::imperialFeedback("Note Edited");
                    break;

                    case "note_edit":
                         $html= imperialNetworkDraw::imperialFeedback("Note Edited");
                    break;

                    case "create_slot":
                         $html= imperialNetworkDraw::imperialFeedback("Slots created");
                    break;

                    case "deleted_slot":
                         $html= imperialNetworkDraw::imperialFeedback("Slot Deleted");
                    break;

                    case "slots_deleted":
                         $html= imperialNetworkDraw::imperialFeedback("Slots Deleted");
                    break;

                    case "ssc_added":
                         $html= imperialNetworkDraw::imperialFeedback("Student Support Card Created");
                    break;

                    case "ssc_edited":
                         $html= imperialNetworkDraw::imperialFeedback("Student Support Card Edited");
                    break;

                    case "ssc_deleted":
                         $html= imperialNetworkDraw::imperialFeedback("Student Support Card Deleted");
                    break;

                    case "tutor_settings_updated":
                         $html= imperialNetworkDraw::imperialFeedback("Tutor settings updated");
                    break;










                }
            }

        }

        return $html;


    }



}
?>
