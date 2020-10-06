<?php
class mwa_draw
{
    public static function draw_homepage()
    {

        $html = '';
        $html.='<hr/>';

        $html.= mwa_draw::draw_feedback();

        $html.='<div id="mwa_listener_wrap">';

        if(current_user_can('edit_pages') ) // If they are not editor then don't show
        {
            $html.="<a href='amirs-dashboard' class='imperial-button'>Amir's dashboard</a><hr/>";
        }


        // Does this student have a booking?
        $logged_in_username = imperialNetworkUtils::get_current_username();
        $booking_info = mwa_queries::get_user_booking($logged_in_username);

        if(isset($booking_info->booking_id) )
        {
            $html.=mwa_draw::draw_booking_date($booking_info->booking_id);
        }
        else
        {
            $html.=mwa_draw::draw_calendar();
        }

        $html.='</div>';

        return $html;
    }




    public static function draw_calendar()
    {
        $html = '';

        $month_array = mwa_utils::get_dates();

        $html.='<div class="accessibleResponsiveTabs">';
        $html.='<ul class="tabs">';

        // Create an array of months and weekdays
        $month_count=1;
        foreach ($month_array as $month_name => $my_dates)
        {
            // Create the tabs
            $class='';
            if($month_count==1){$class="active";}
            $tab_id='month_'.$month_name;
            $html.='<li class="'.$class.'" rel="'.$tab_id.'"><a href="#'.$tab_id.'">'.$month_name.'</a></li>';
            $month_count++;
        }



        $html.='</ul>';

        $html.='<div class="tabContainer">';

        // Get ALL dates in an array for lookup
        $all_bookings_array = mwa_queries::get_all_bookings_by_day();

        // Now go through the months and spit them out
        $exit_next_row = false; // Use this to see if we need to exit if all slots are not filled for this date

        foreach($month_array as $this_month => $month_date_array)
        {
            if($exit_next_row==true){break;}

            $this_month_id = 'month_'.$this_month;
            $html.='<h2 class="accordionHeading" rel="'.$this_month_id.'">'.$this_month.'</h2>';

            $temp_table_content='<table class="imperial-table">';
            foreach ($month_date_array as $this_date)
            {

                if($exit_next_row==true){break;}

                $datetime = DateTime::createFromFormat('Y-m-d', $this_date);
                $daystr =  $datetime->format('l jS F, Y');

                // See how many slots are left for this date
                $bookings_made = 0;
                $class = 'successText';

                if(isset($all_bookings_array[$this_date]) )
                {
                    $bookings_made = count($all_bookings_array[$this_date]);
                    $class = 'alertText';
                }


                $bookings_available = 5-$bookings_made;


                if($bookings_available==0)
                {
                    $class = 'failText';
                }

                //echo '$bookings_made = '.$bookings_made.'<br/>';

                if($bookings_made<5)
                {
                    $exit_next_row=true;
                }


                $temp_table_content.= '<tr>';
                $temp_table_content.='<td>'.$daystr.'</td>';
                $temp_table_content.='<td><span class="'.$class.'">'.$bookings_available.' / 5 slots available</span></td>';
                $temp_table_content.='<td>';
                if($bookings_available>=1)
                {
                $temp_table_content.='<button class="imperial-button has-click-event" data-method="confirm_booking_date" data-this_date="'.$this_date.'" data-this_date_str="'.$daystr.'">Book this day</button>';
                }
                else
                {
                    $temp_table_content.='-';
                }
                $temp_table_content.='</td>';
                $temp_table_content.='</tr>';
            }
            $temp_table_content.='</table>';

            $html.='<div id="'.$this_month_id.'" class="tabContent">';
            $html.='<h3 class="ghost contentHeading">'.$temp_table_content.'</h3>';
            $html.=$temp_table_content;


            $html.='</div>';


        }

        $html.='</div> <!-- End of tab content container --->';
        $html.='</div> <!--- End of tab wrap ---->';

        $html.='<script>
        jQuery(document).ready(function () {
        jQuery(".accessibleResponsiveTabs").accessibleResponsiveTabs();
        });
        </script>';

        return $html;

    }


    public static function draw_amir_dashboard()
    {

        if(!current_user_can('edit_pages') ) // If they are not editor then don't show
        {
            return;
        }


        // Has the form been submitted?

        if(isset($_GET['action']) )
        {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            update_option("amir_start_date", $start_date);
            update_option("amir_end_date", $end_date);
        }



        $html='';


        // get the date options
        $start_date = get_option('amir_start_date');
        $end_date = get_option('amir_end_date');


        echo '<form action="?action=update_slot_dates" method="post" class="imperial-form">';

        echo '<div class="form_dates_wrap">';
        echo '<div>';
        $args = array(
        "type" => "date",
        "name" => "start_date",
        "value" => $start_date,
        "ID" => "start_date",
        "label" => "Slots Start Date",
        );
        echo mwa_draw::form_item($args);
        echo '</div>';

        echo '<div>';

        $args = array(
        "type" => "date",
        "name" => "end_date",
        "value" => $end_date,
        "ID" => "end_date",
        "label" => "Slots End Date",
        );
        echo mwa_draw::form_item($args);
        echo '</div>';
        echo '</div>';



        echo '<input type="submit" value="Update Slot Dates" >';
        echo '</form>';



        $next_meeting = '';

        $month_array = mwa_utils::get_dates();

        // Get lookuop table for all bookings
        $all_bookings_array = mwa_queries::get_all_bookings_by_day();

        $i=1;
        foreach ($month_array as $month => $month_dates)
        {

            $html.='<h2>'.$month.'</h2>';
            $html.='<table class="imperial-table">';




            foreach ( $month_dates as $this_date)
            {

                $datetime = DateTime::createFromFormat('Y-m-d', $this_date);
                $daystr =  $datetime->format('l jS F, Y');

                // Get thet students on this date
                $my_students = array();
                if(isset($all_bookings_array[$this_date]) )
                {
                    $my_students = $all_bookings_array[$this_date];
                }

                if($i==1)
                {
                    $next_meeting.='<h2>Next Meeting : '.$daystr.'</h2>';
                    $next_meeting.='You are meeting with:<br/>';

                    $today_array = $my_students;

                    if(count($today_array)==0 )
                    {
                        $next_meeting.= '<strong>You have no students today.</strong>';
                    }

                    $next_meeting.='<div class="imperial-flex-container">';
                    $email_str1 = '';
                    $email_str2 = '';
                    foreach ($today_array as $username)
                    {
                        $student_info = imperialQueries::getUserInfo($username);
                        $full_name = $student_info['first_name'].' '.$student_info['last_name'];
                        $cid = $student_info['userID'];
                        $email = $student_info['email'];

                        $email_str1.=$email.',';
                        $email_str2.=$email.';';


                        // Get the user avatar
                        $args = array(
                            "CID"		=> $cid,
                            'size'	=> "square",
                        );

                        $avatar_url = get_user_avatar_url( $args);
                        $hash = create_hash();
                        $avatar_url = $avatar_url.'?hash='.$hash;

                        $next_meeting.='<a href="https://medlearn.imperial.ac.uk/profile/?username='.$username.'">';
                        $next_meeting.='<div class="mwa_student_wrap" style="text-align:center">';
                        $next_meeting.='<img src="'.$avatar_url.'" width="80px"><br/>';
                        $next_meeting.= $full_name.'<br/>';
                        $next_meeting.='<span class="smallText">'.$cid.'</span>';
                        $next_meeting.='</div>';
                        $next_meeting.='</a>';
                    }



                    $next_meeting.='</div>';
                    if($email_str1)
                    {
                       // $next_meeting.= '<a href="mailto:'.$email_str1.'" class="imperial-button">Email these students</a>' ;
                        $next_meeting.= '<a href="mailto:'.$email_str2.'" class="imperial-button">Email these students (option2 )</a>';
                       // $next_meeting.='<br>'.$email_str2;

                    }
                }
                else
                {

                    $html.='<tr><td>'.$daystr.'</td>';


                    if(count($my_students)==0)
                    {
                        $html.= '<td>No students found</td>';
                    }
                    else
                    {
                        $html.= '<td>';

                        foreach ($my_students as $username)
                        {
                            $student_info = imperialQueries::getUserInfo($username);
                            $full_name = $student_info['first_name'].' '.$student_info['last_name'];
                            $cid = $student_info['userID'];

                            $args = array("cid" => $cid);
                            $avatar_url = get_user_avatar_url( $args );

                            $html.= $full_name.', ';
                        }
                        $html.= '</td>';

                    }


                    $html.='</tr>';
                }
                $i++;


            }
            $html.='</table>';


        }

        return $next_meeting.$html;
    }

    public static function draw_booking_date($booking_id)
    {

        $html = '';
        $booking_info = mwa_queries::get_booking_info($booking_id);
        $this_date = $booking_info->booking_date;

        $datetime = new DateTime($this_date);
        $date_str =  $datetime->format('l jS F, Y');

        $html.='Your booking with Dr Sam is on <strong>'. $date_str .'</strong><hr/>';
        $html.='<button class="imperial-button has-click-event" data-method="delete_booking_check" data-id="'.$booking_id.'">Cancel this booking</button>';

        return $html;

    }


   public static function draw_feedback()
   {

       $html = '';
       // Hadnle any feedback messages
       if(isset($_GET['feedback']) )
       {
           $feedback = $_GET['feedback'];
           $type = '';
           if(isset($_GET['type']) )
           {
               $type = $_GET['feedback_type'];
           }

           // Firstly check if there is anyt feedback

           switch ($feedback)
           {
               case "booking_created":
                   $html= imperialNetworkDraw::imperialFeedback("Booking successful - please check your inbox for an invitation.", $type);
               break;

               case "booking_delete":
                   $html= imperialNetworkDraw::imperialFeedback("Booking deleted.");
               break;




           }

       }

       return $html;


   }

   public static function form_item($args = array())
    {

        $html = '<div class="ek_form_item">';
        $value = '';
        $required = '';

        $type = $args['type'];
        $ID = $args['ID'];
        $label = $args['label'];

        // If no name is given the ID will be the name
        $name = $args['ID'];
        if(isset($args['name']) ){$name = $args['name'];}
        if(isset($args['value']) ){$value = $args['value'];}
        if(isset($args['required']) ){$required = $args['required'];}

        switch ($type)
        {





            case "date":
                if($value==""){$value = date('Y-m-d');}

                $html.= '<label for="'.$ID.'">'.$label.'</label>';
                $html.=  '<input type="text" name="'.$ID.'" id="'.$ID.'" value="'.$value.'"/>';
                $html.=  '<script>
                jQuery(function() {
                    jQuery( "#'.$ID.'" ).datepicker({
                        dateFormat : "yy-mm-dd"
                    });
                });
                </script>';
            break;



        }

        $html.='</div>';

        return $html;

    }

}

?>
