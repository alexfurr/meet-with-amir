<?php
class mwa_queries
{
    public static function get_user_booking($username)
    {

        global $wpdb;
		global $mwa_bookings;

		$sql = "SELECT * FROM $mwa_bookings WHERE username='" . $username."'";

		$booking_info =  $wpdb->get_row( $sql );

		return $booking_info;

    }

    public static function get_booking_info($booking_id)
    {

        global $wpdb;
        global $mwa_bookings;

        $sql = "SELECT * FROM $mwa_bookings WHERE booking_id=".$booking_id;
        $booking_info =  $wpdb->get_row( $sql );
        return $booking_info;
    }

    public static function get_bookings_for_day($date)
    {

        global $wpdb;
        global $mwa_bookings;

        $sql = "SELECT * FROM $mwa_bookings WHERE booking_date='".$date."'";
        $my_bookings =  $wpdb->get_results( $sql );
        return $my_bookings;
    }

    // Returns an array of bookings with the student usernames asn an array and the date as the key
    public static function get_all_bookings_by_day()
    {
        global $wpdb;
        global $mwa_bookings;

        $all_bookings_array = array();
        $sql = "SELECT * FROM $mwa_bookings";
        $all_bookings =  $wpdb->get_results( $sql );

        foreach ($all_bookings as $booking_info)
        {

            $booking_date = $booking_info->booking_date;
            $username = $booking_info->username;

            $all_bookings_array[$booking_date][] = $username;
        }

        return $all_bookings_array;
    }
}


?>
