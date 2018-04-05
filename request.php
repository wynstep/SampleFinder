<?php
/* Perform request section for the Sample Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: The request section contains the sample form */

// getting the POST variables
$count_cases = $_POST["count_cases"];
// here we set the session variable (protect from inspect)
session_start();
$_SESSION["cases"] = $_POST["cases"];

error_reporting(E_ALL);
ini_set('display_errors', 'on');

// printing the whole HTML block code
echo <<< EOT
<p class="info" style="padding-left:30px"> In order to proceed with your request, please fill in the details below </p>
<!-- Personal Information -->
<form action="scripts/PerformRequest.php" method="POST">
  <fieldset class="filter">
    <legend>Principal investigator</legend>
    <table class="info">
      <tr>
        <td style="width:20%">Name<sup>*</sup>:</td>
        <td><input type="text" name="name" required></td>
        <td style="width:20%">Surname<sup>*</sup>:</td>
        <td><input type="text" name="surname" required></td>
      </tr>
      <tr>
      <td style="width:20%">Institution<sup>*</sup>:</td>
      <td><input type="text" name="institution" required></td>
      <td style="width:20%">Email address<sup>*</sup>:</td>
      <td><input type="email" name="email" required placeholder="Enter a valid email address"></td>
      </tr>
    </table>
  </fieldset>
  <fieldset class="filter">
    <legend>Project details</legend>
    <table class="info">
      <tr>
        <td>Brief description of your project<sup>*</sup>:</td>
        <td colspan=3><textarea name="project" rows="10" cols="150" maxlength="500" required></textarea></td>
      </tr>
      <tr>
        <td>Number of samples required<sup>*</sup>:</td>
        <td>
          <input id="ncases" name="ncases" required><p class="info"> out of <b style="font-size:20px">$count_cases </p>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          <center>
            <input type="checkbox" name="conditions_use" value="yes" required> <p class="info">I accept the conditions....</p>
          </center>
        </td>
      </tr>
    </table>
  </fieldset>
  <div class="perform_request">
    <input type="submit" id="submit_request" value="Perform the request">
  </div>
</form>

<!-- Load Javascripts -->
<script>LoadSpinner($count_cases);</script>
<!-- Load Javascripts -->
EOT;

?>
