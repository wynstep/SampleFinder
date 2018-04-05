<?php

/* Perform Request to the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: PHP page for sending request details to the BCN */

// debugging errors
ini_set('display_errors', '1');

// loading PHPMailer package
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer.php';
include("functions.php");

// initialising variables
session_start();
$cases = $_SESSION["cases"];
$name = $_POST["name"];
$surname = $_POST["surname"];
$institution = $_POST["institution"];
$email = $_POST["email"];
$project = $_POST["project"];
$ncases = $_POST["ncases"];

// loading email
$mail = new PHPMailer(true);
try {
  //Recipients
  $mail->setFrom('bioinformatics.breastcancertissuebank@qmul.ac.uk', 'Breast Cancer Now Tissue Bank');
  $mail->addAddress($_POST["email"], $_POST["name"]);     // Add a recipient
  $mail->addAddress('stefano.pirro@icloud.com');     // Add a recipient

  //Content
  $mail->isHTML(true);                                  // Set email format to HTML
  $mail->Subject = 'Test subject';
  $mail->Body    = createHTMLMessage($name, $surname, $institution, $email, $project, $ncases, $cases);
  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  $mail->send();
  echo 'Message has been sent';
} catch (Exception $e) {
  echo 'Message could not be sent.';
  echo 'Mailer Error: ' . $mail->ErrorInfo;
}

?>
