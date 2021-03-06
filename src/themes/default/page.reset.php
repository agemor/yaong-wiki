<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 23
 */

require_once YAONGWIKI_CORE_DIR . "/page.reset.processor.php";

$page = process();

if (isset($page["redirect"])) {
    redirect($page["redirect"]);
    exit();
}

$page["title"] = "Reset Password" . " - " . SettingsManager::get_instance()->get("site_title");
?>

<?php require_once __DIR__ . "/frame.header.php"; ?>
<div class="container">

  <div class="title my-4">
    <h2>Reset Password</h2>
  </div>

  <?php if (isset($page["result"]) && $page["result"] !== true) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo($page["message"]);?>
  </div>
  <?php } ?>

  <?php if (isset($page["message"]) && $page["message"] == "success") { ?>
  <p>Your new password is sent to your email address. Please check your mailbox.</p>

  <?php } else { ?>
  <form action="./?reset" method="post">
		<div class="row my-4">
			<div class="col-md-6">
				<p>Please enter your email address when you signed up. Your new password will be sent to this email address.</p>
				<div class="form-group">
					<label for="emailInput">Email Address</label>
					<input type="text" name="user-email" class="form-control" id="emailInput" value="<?php echo(HttpVarsManager::get_instance()->get("user-email"));?>" required>
				</div>
        <div class="my-3">
        <?php if (SettingsManager::get_instance()->get("recaptcha_enable") == "true") { ?>
          <label for="recaptchInput">Recaptcha</label>
          <script src='https://www.google.com/recaptcha/api.js'></script>
          <div class="g-recaptcha" data-sitekey="<?php echo(SettingsManager::get_instance()->get("recaptcha_public_key"));?>" ></div>
        <?php } ?>
        </div>
				<button type="submit" class="btn btn-primary mt-1">Request new password</button>
				<a href="./?signin" class="btn btn-default">Go back</a>
			</div>
		</div>
	</form>
  <?php } ?>
</div>
<?php require_once __DIR__ . "/frame.footer.php"; ?>