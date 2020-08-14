<?php

$meet_with_amir_db = new meet_with_amir_db();

class meet_with_amir_db
{
	var $DBversion 		= '1.2';

	//~~~~~
	function __construct ()
	{
        add_action( 'init',  array( $this, 'checkCompat' ) );

        global $wpdb;
        global $mwa_bookings;


        $mwa_bookings = $wpdb->prefix . 'mwa_bookings';

	}

	//~~~~~
	function checkCompat ()
	{

		// Get the Current DB and check against this verion
		$currentDBversion = get_option('mwa_db_version');
		$thisDBversion = $this->DBversion;


		if($thisDBversion>$currentDBversion)
		{

			$this->createTables();
			update_option('mwa_db_version', $thisDBversion);
		}
		//$this->createTables();
	}



	function createTables ()
	{


        global $wpdb;
        global $mwa_bookings;


        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$WPversion = substr( get_bloginfo('version'), 0, 3);
		$charset_collate = ( $WPversion >= 3.5 ) ? $wpdb->get_charset_collate() : $this->getCharsetCollate();

		//users table
		$sql = "CREATE TABLE $mwa_bookings (
			booking_id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(50),
            fullname varchar(255),
			booking_date date,
            did_not_show int,
			INDEX booking_date (booking_date),
			PRIMARY KEY (booking_id)

		) $charset_collate;";

		$feedback = dbDelta( $sql );

	}


	function getCharsetCollate ()
	{
		global $wpdb;
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) )
		{
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) )
		{
			$charset_collate .= " COLLATE $wpdb->collate";
		}
		return $charset_collate;
	}

}



?>
