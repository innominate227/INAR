<?php
/* DO NOT REMOVE */
if (!defined('QUADODO_IN_SYSTEM')) {
exit;
}
/*****************/
?>
<link rel="stylesheet" type="text/css" HREF="html/form.css" />
<fieldset>
	<legend>
		<?php echo REGISTER_LABEL; ?>
	</legend>
	<form action="register.php<?php if (isset($_GET['code'])) { ?>?code=<?php echo htmlentities($_GET['code']); } ?>" method="post">
		<input type="hidden" name="process" value="true" />
		<input type="hidden" name="random_id" value="<?php echo $random_id; ?>" />
		<table>			
			<tr>
				<td>
					<?php echo EMAIL_LABEL; ?>

				</td>
				<td>
					<input type="text" name="email" maxlength="100" value="<?php if (isset($_POST['email'])) { echo $_POST['email']; } ?>" />
				</td>
			</tr>
<?php
/* START SECURITY IMAGE */
if ($qls->config['security_image'] == 'yes') {
?>
			<tr>
				<td colspan="2" align="center">
					<img src="security_image.php?id=<?php echo $random_id; ?>" border="0" alt="Security Image" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo SECURITY_CODE_LABEL; ?>
				</td>
				<td>
					<input type="text" name="security_code" maxlength="8" />
				</td>
			</tr>
<?php
}
/* END SECURITY IMAGE */
?>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					<input type="submit" value="<?php echo REGISTER_SUBMIT_LABEL; ?>" />
				</td>
			</tr>
		</table>
	</form>
</fieldset>