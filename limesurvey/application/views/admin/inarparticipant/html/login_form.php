<?php
/* DO NOT REMOVE */
if (!defined('QUADODO_IN_SYSTEM')) {
exit;
}
/*****************/
?>
<link rel="stylesheet" type="text/css" HREF="html/form.css" />
<fieldset>

	<form action="login_process.php" method="post">
		<input type="hidden" name="process" value="true" />
		<table>
                <caption> <?php echo LOGIN_LABEL; ?> </caption>
			<tr>
				<td>
					<?php echo USERNAME_LABEL; ?>

				</td>
				<td>
					<input type="text" name="username" maxlength="<?php echo $qls->config['max_username']; ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo PASSWORD_LABEL; ?>

				</td>
				<td>
					<input type="password" name="password" maxlength="<?php echo $qls->config['max_password']; ?>" />
				</td>
			</tr>
			
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
<input type="submit" style="width:70%;padding:1px 1px;font-size:17px;" value="<?php echo LOGIN_SUBMIT_LABEL; ?>" />
				</td>
			</tr>
		</table>
	</form>
</fieldset>