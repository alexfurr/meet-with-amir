<?php
class mwa_actions
{
    public static function process_booking()
    {
        $logged_in_username = imperialNetworkUtils::get_current_username();

        // Do they already have a slot?
        $booking_info = mwa_queries::get_user_booking($logged_in_username);
        if(isset($booking_info->booking_id) )
        {
            return;
        }



        $date = $_GET['date'];

        // Count the slots on this datein case someone else has beat them
        $this_date_bookings = mwa_queries::get_bookings_for_day($date);
        $booking_count = count($this_date_bookings);


        if($booking_count>=5)
        {
            return array(
                "type" => "error",
                "message"   => "No available slots",
            );
        }

        // Get the full name of this user and add it for lookup so we are not hitting the DB on Amirs page
        $this_user_meta = imperialQueries::getUserInfo($logged_in_username);
        $fullname = $this_user_meta['first_name'].' '.$this_user_meta['last_name'];

        global $wpdb;
        global $mwa_bookings;

        $wpdb->query( $wpdb->prepare(
        "INSERT INTO ".$mwa_bookings." (username, fullname, booking_date)
        VALUES ( %s, %s, %s )",
        array(
            $logged_in_username,
            $fullname,
            $date
            )
        ));




        // Also send the invite
        //mwa_utils::test_message();

        // Process the location
        /*
        if($date<="2020-10-23")
        {
            $this_location = "CX Reynolds R1";
        }
        else
        {
            $this_location = "CCX lab block 7th floor clinical skills suite";
        }
        */

        $this_location = "CX Reynolds R1";



        mwa_utils::sendIcalEvent($date, $this_location, $logged_in_username);



        return array(
            "type" => "success",
            "message"   => "booking_created",
        );

    }

    public static function booking_delete()
    {
        $can_delete=false;
        $logged_in_username = imperialNetworkUtils::get_current_username();
        $booking_id = $_GET['id'];

        $booking_info = mwa_queries::get_booking_info($booking_id);
        $username = $booking_info->username;

        // If this is the same username thendelete the slot
        if($logged_in_username==$username){$can_delete=true;}

        global $wpdb;
        global $mwa_bookings;

        if($can_delete==false)
        {
            return 'booking_delete_error';

        }
        else
        {

            $SQL = "DELETE FROM  ".$mwa_bookings." WHERE booking_id = ".$booking_id;
            $wpdb->query( $SQL );

            return 'booking_delete';
        }





    }

}
?>
