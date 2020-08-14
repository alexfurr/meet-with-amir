<?php
class mwa_utils
{
    public static function get_dates()
    {

        $start_date = '2020-10-01';
        $end_date = '2020-11-30';

        $today = date('Y-m-d');

        $month_array = array();
        $dates = array();
        $current = strtotime($start_date);
        $end_date = strtotime($end_date);
        $stepVal = '+1 day';
        while( $current <= $end_date )
        {
            $this_date = date('Y-m-d', $current);
            $current = strtotime($stepVal, $current);

            if($this_date<$today) // if it's in the past then move on
            {
                continue;
            }

            $datetime = DateTime::createFromFormat('Y-m-d', $this_date);
            $dayname =  $datetime->format('D');
            $monthname =  $datetime->format('F');

            if($dayname=="Sat" || $dayname=="Sun")
            {
                continue;
            }

            if(!isset($month_array[$monthname]) )
            {
                $month_array[$monthname] = array();
            }

            $month_array[$monthname][] = $this_date;
        }

        return $month_array;
    }



    function sendIcalEvent($meeting_date, $username)
    {

        $meeting_date_time = $meeting_date." 17:00";
        $end_date_time = $meeting_date." 19:15";


        // Create unique ID for this item
        $UID = md5(uniqid(mt_rand(), true)) . "@medlearn.imperial.ac.uk\r\n";
    	$location = "TBC";

    	$subject = 'Meeting with Dr Amir Sam';
    	$method="REQUEST";
    	$status="CONFIRMED";

        $message_body = "Thanks for booking a meeting with Dr Amir Sam<br/>";

    	$student_info = imperialQueries::getUserInfo($username);
    	$student_name = $student_info['first_name'].' '.$tuteeInfo['last_name'];
    	$student_email = $student_info['email'];


    	$from_name = "MedLearn";
    	$from_address = "NoNotReply@medlearn.imperial.ac.uk";

        //Create Email Headers
        $mime_boundary = "----Meeting Booking----".MD5(TIME());

    	$headers='';
        //$headers = "From: ".$from_name." <".$from_address.">\n";
        //$headers .= "Reply-To: ".$tuteeName." <".$tuteeEmail.">\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
        $headers .= "Content-class: urn:content-classes:calendarmessage\n";

        //Create Email Body (HTML)
        $message = "--$mime_boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\n";
        $message .= "Content-Transfer-Encoding: 8bit\n\n";
        $message .= "<html>\n";
        $message .= "<body>\n";
        $message .= '<p>Dear '.$student_name.',</p>';
        $message .= '<p>'.$message_body.'</p>';
        $message .= "</body>\n";
        $message .= "</html>\n";
        $message .= "--$mime_boundary\r\n";


    	// Convert to UTC
    	$tz_from = 'Europe/London';
    	$tz_to = 'UTC';
    	$format = 'Ymd\THis\Z';

    	// Create Start Date
    	$dt = new DateTime($meeting_date_time, new DateTimeZone($tz_from));
    	$dt->setTimeZone(new DateTimeZone($tz_to));
    	$startDateICS =  $dt->format($format) . "\n";

    	// Create End Date
        $dt = new DateTime($end_date_time, new DateTimeZone($tz_from));
        $dt->setTimeZone(new DateTimeZone($tz_to));
        $endDateICS =  $dt->format($format) . "\n";

        $ical = 'BEGIN:VCALENDAR' . "\r\n" .
        'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
        'VERSION:2.0' . "\r\n" .
        'METHOD:'.$method. "\r\n" .


        'BEGIN:VEVENT' . "\r\n" .
        'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
        'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT\r\n"' .
        'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
        'UID:'.$UID."\r\n" .
        'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
        'DTSTART:'.$startDateICS. "\r\n" .
        'DTEND:'.$endDateICS. "\r\n" .

        'TRANSP:OPAQUE'. "\r\n" .
        'SEQUENCE:1'. "\r\n" .
    	'STATUS:'.$status.'' .
        'SUMMARY:' . $subject . "\r\n" .
        'LOCATION:' . $location . "\r\n" .
        'CLASS:PUBLIC'. "\r\n" .
        'PRIORITY:5'. "\r\n" .
        'BEGIN:VALARM' . "\r\n" .
        'TRIGGER:-PT15M' . "\r\n" .
        'ACTION:DISPLAY' . "\r\n" .
        'DESCRIPTION:Reminder' . "\r\n" .
        'END:VALARM' . "\r\n" .
        'END:VEVENT'. "\r\n" .
        'END:VCALENDAR'. "\r\n";
        $message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST'."\n";
        $message .= "Content-Transfer-Encoding: 8bit\n\n";
        $message .= $ical;
        $mailsent = mail($to_address, $subject, $message, $headers);

        return ($mailsent)?(true):(false);
    }


}

?>
