<?php
/* DO NOT REMOVE */
if (!defined('QUADODO_IN_SYSTEM')) 
{
echo "huh";
exit;
}?>

<link rel="stylesheet" type="text/css" HREF="html/form.css" />
<fieldset>

	<form action="register.php<?php if (isset($_GET['code'])) { ?>?code=<?php echo htmlentities($_GET['code']); } ?>" method="post">
		<input type="hidden" name="process" value="true" />
		<input type="hidden" name="random_id" value="<?php echo $random_id; ?>" />
		<table>
                 <caption> <?php echo REGISTER_LABEL; ?> </caption>

<?php 
$sql1 = 'SELECT `COLUMN_NAME`,`DATA_TYPE` '
        . ' FROM `INFORMATION_SCHEMA`.`COLUMNS` '
        . ' WHERE `TABLE_SCHEMA`=\'inar2\' '
        . ' AND `TABLE_NAME`=\'inar_participants\' AND `COLUMN_NAME` != \'id\' AND `COLUMN_NAME` != \'date\';';

$col_names=array();
$data_types=array();
$results=$qls->SQL->query($sql1);
$count=$qls->SQL->num_rows($results);
while ($row = mysql_fetch_array($results)) 
{
$col_names[]=$row['COLUMN_NAME'];
$data_types[]=$row['DATA_TYPE'];
}
for($i=0;$i<$count;$i++)
{
?>

<tr>
<td>
<?php echo ucfirst($col_names[$i]).':'; ?>
</td>
<td>
<?php 
if ($data_types[$i]=="varchar")
$type = "text";
else if($data_types[$i]=="int")
$type = "number";
else
$type = "date";
?>
<input type="<?php echo htmlspecialchars($type);?>" name="<?php echo htmlspecialchars($col_names[$i]);?>" maxlength="100" value="<?php if (isset($_POST[$col_names[$i]])) { echo $_POST[$col_names[$i]]; } ?>" />
</td>
</tr>
<?php } ?>
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
					<?php echo 'Enter visible text:' ?>
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
<input type="submit" style="width:70%;padding:1px 1px;font-size:17px;" value="<?php echo REGISTER_SUBMIT_LABEL; ?>" />
</td>
</tr>
</table>
</form>
</fieldset>