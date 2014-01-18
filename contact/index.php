<?php

require_once('../inc/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = trim($_POST["name"]);
	$email = trim($_POST["email"]);
	$message = trim($_POST["message"]);
	
	if ($name == '' OR $email == '' OR $message == '') {
		$error_message =  'Blank name, email or message. D&apos;oh!';
	}
	
	if (!isset($error_message)) {
		foreach ( $_POST as $value) {
			if( stripos($value, 'Content-Type:') !== FALSE ) {
				$error_message =   'There was a problem with the information you entered.';
			}
		}
	}
	
	if (!isset($error_message) && $_POST["address"] != '') {
		$error_message = 'Your form submission has an error.';
	}
	
	require_once(ROOT_PATH . 'inc/phpmailer/class.phpmailer.php');
	$mail = new PHPMailer();
	
	if (!isset($error_message) && !$mail->ValidateAddress($email)){
		$error_message = 'You must enter a valid email address.';
	}
	
	if (!isset($error_message)) {
		$email_body = "";
		
		$email_body = $email_body . "Name: " . $name . "<br />";
		$email_body = $email_body . "Email: " . $email . "<br />";
		$email_body = $email_body . "Message: " . $message;
		
		$mail->SetFrom($email,$name);
			
		$address = "orders@shirts4mike.com";
		$mail->AddAddress($address, "Shirts 4 Mike");
		
		$mail->Subject    = "Shirts 4 Mike Contact Form Submission |" . $name;
		
		$mail->MsgHTML($email_body);
			
		if($mail->Send()) {
			header("Location: " . BASE_URL . "contact/?status=sent");
			exit;
		} else {
		  $error_message = 'There was a problem sending the email: ' . $mail->ErrorInfo;
		}
		
	} // end if request_method
}
?>
<?php 
$pageTitle = "Contact Mike";
$section = "contact";
include(ROOT_PATH . 'inc/header.php'); ?>

	<div class="section page">
	
		<div class="wrapper">
		
			<h1>Contact</h1>
			
			<?php if (isset($_GET["status"]) AND $_GET["status"] == "sent") { ?>
				<p>Thanks bud.</p>
			<?php } else { ?>	
				
				<?php if (!isset($error_message)) { 
					echo '<p>I&apos;d love to hear from you! Complete the form to send me an email!</p>';
					
					} else {	
						echo '<p class="message">' . $error_message . '</p>';
					}
				?>
				
				<form method="POST" action="<?php echo BASE_URL; ?>contact/">
					<table>
					<tr>
						<th><label for="name">Name</label></th>
						<td><input type="text" name="name" placeholder="Your name" id="name" value="<?php if (isset($name)) { echo htmlspecialchars($name); } ?>" /></td>
					</tr>
					<tr>
						<th><label for="email">Email</label></th>
						<td><input type="email" name="email" placeholder="Your email" id="email" value="<?php if (isset($email)) { echo htmlspecialchars($email); } ?>" /></td>
					</tr>
					<tr style="display: none!important;">
						<th><label for="address" >Address</label></th>
						<td><input type="text" name="address" id="address" /></td>
						<p style="display: none!important;">Humans (and frogs): please leave this field blank.</p>
					</tr>
					<tr>
						<th><label for="message">Message</label></th>
						<td><textarea name="message" placeholder="Your message" id="message"><?php if (isset($message)) { echo htmlspecialchars($message); } ?></textarea></td>
					</tr>
					</table>
					<input type="submit" value="Send" />
				</form>
			<?php } ?>
		</div>
	
	</div>
	
<?php include(ROOT_PATH . 'inc/footer.php'); ?>