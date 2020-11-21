<?php

//TODO: Takes care of the form submission [work as the post office]

// 4. It returns proper info in JSON format [Receipts!] [checked!]
//   a. what is AJAX?? - send out data request without reloading the page
//   b. What is JSON?? - data serialization format that the server can understand
//   c. How to build JSON(in php) - see below

header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json; charset=UTF-8');
$results = [];
$visitor_name = '';
$visitor_email= '';
$visitor_message= '';

// 1. check the submission --> validate the data [is there any danger about the package or non-mailable items?]
if(isset($_POST['firstname'])){
    $visitor_name = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
}
if(isset($_POST['lastname'])){
    $visitor_name .= ' '.filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
}
if(isset($_POST['email'])){
    $visitor_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
}
if(isset($_POST['message'])){
    $visitor_message = filter_var(htmlspecialchars($_POST['message']), FILTER_SANITIZE_STRING);
    //we dont want anything harmful to be injected into our databases. This can stop bad actors
}

$results['name'] = $visitor_name;
$results['message'] = $visitor_message;


// 2. Prepare the email [print out the labels and apply to package/ prepare the package in a certain format]
$email_subject = 'Inquiry from Portfolio Site from'. $visitor_name;
$email_recipient = 'selena.elise.wells@gmail.com';
$email_message = sprintf('Name: %s, Email: %s, Message: %s', $visitor_name, $visitor_email, $visitor_message);
//creates a template to insert the user data into a formated string

//make sure you run the code in PHP 7.4+
//otherwise you would need to $email_headers a string
$email_headers = array(
    'From' =>'selenawells@selenawells.ca',
    'Reply-To' => $visitor_email
    //Best practise  but it may need to to have an email set up via your domaine - do it this way so that the emails are not flagged by the browser

    //You can do this if the above is not possible
    // 'From' => $visitor_email
);


// 3. Send out the email [send the package]

$email_result = mail($email_recipient, $email_subject, $email_message, $email_headers);
if($email_result){
    $results['message'] = sprintf('Thank you for contacting us, %s. You will get a reply within 24 hours.', $visitor_name);
}else{
    $results['message'] = sprintf('We are sorry but the email did not go through.');
}

echo json_encode($results);

