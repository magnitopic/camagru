<?php

$conf_subject = 'Your recent enquiry';

// Who should the confirmation email be from?
$conf_sender = 'Camagru <no-replay@maildrop.cc>';

$msg = $_POST['Name'] . ",\n\nThank you for your recent enquiry. A member of our 
team will respond to your message as soon as possible.";

mail( $_POST['Email'], $conf_subject, $msg, 'From: ' . $conf_sender );
