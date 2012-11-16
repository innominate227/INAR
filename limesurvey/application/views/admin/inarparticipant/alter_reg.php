<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
?>

	
		<div id="wrapper">
		<div id="header">    	
							<ul>							
	
							<li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>">View Surveys</a></li>
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">


<?php	
	if (isset($_POST['process_alter_table'])) 
	{		
		$add_term = $_POST['column_name'];
                $data_type = $_POST['data'];	
                $participants_table = $qls->config['sql_prefix'] . 'participants';
                $sql1= "INSERT INTO `reg_form_fields` (`name`, `visible`) VALUES ('$add_term',1)";
                if ($data_type=="VARCHAR")
                {
                $sql = "ALTER TABLE `inar_participants` ADD `{$add_term}` VARCHAR(255) NOT NULL";
                }
                else if($data_type=="NUMBER")
                {
                $sql = "ALTER TABLE `inar_participants` ADD `{$add_term}` INT(10) NOT NULL";
                //$qls->SQL->query($sql);
                }
                else if($data_type=="DATE")
                {
                $sql = "ALTER TABLE `inar_participants` ADD `{$add_term}` DATE NOT NULL";
                //$qls->SQL->query($sql);
	        }
        
        try
              {
                     $qls->SQL->query($sql);
                     $qls->SQL->query($sql1);
                     $site="new_reg.php";
       
              }
        catch (Exception $e) 
             {
              echo '<div class="error">';
              echo 'Oops. The registration form was not altered !',  $e->getMessage(), "\n";
              echo '</div>';
             }
           
 
} ?>

<div class="col2" style="border-left:none;border-right:0.5em solid #ccc;">
<p> Alter Registration Form. Choose what kind of field (name and type) to add </p>
<form action="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>" method="post">
<input type="hidden" name="process_alter_table" value="yes"/>
<label><span>Add Field </span>
<input type="text" name="column_name" value="<?php echo htmlspecialchars($_POST['column_name']);?>" /> 
</label>
<label><span> Of Type: </span>
<select name="data" style="width:10em;"> 
  <option value="NUMBER">Number</option> 
  <option value="VARCHAR">Text</option>
  <option value="DATE">Date</option>
</select>
</label>
<div style="margin: 0px auto 0px auto; text-align: center;padding:1.4em;">
<input class="myClass" type="submit" value="Update"/>
</div>
</form>
</div>
<div class="col1">
<?php
if(array_key_exists('sub',$_POST))
        {
         print_r($_POST['names']);
         echo "Dropping A Column From The Database - Currently not Implemented!";
     }
?>
<p> Current fields in the Registration Form </p>
<?php
$sql1 = 'SELECT `COLUMN_NAME`'
        . ' FROM `INFORMATION_SCHEMA`.`COLUMNS` '
        . ' WHERE `TABLE_SCHEMA`=\'inar2\' '
        . ' AND `TABLE_NAME`=\'inar_participants\' AND `COLUMN_NAME` != \'id\' AND `COLUMN_NAME` != \'date\';';

$col_names=array();
$results=$qls->SQL->query($sql1);
$count=$qls->SQL->num_rows($results);
while ($row = mysql_fetch_array($results)) 
{
$col_names[]=$row['COLUMN_NAME'];
}?>
<form method='post'>
<?php for($i=0;$i<$count;$i++)
{

$sql5 = "SELECT `visible` FROM `reg_form_fields` WHERE `name`= '$col_names[$i]'";
$result=$qls->SQL->query($sql5);
$vis = mysql_result($result, 0);
?>
<label> <span><?php echo $col_names[$i]; ?> </span> <input name="names[]" type="checkbox" value="<?php echo $i;?>" <?php if($vis)echo 'checked="checked"'?> /> </label>
<?php } ?>
<div style="margin:0px auto;width: 50%;padding:1.4em;">
<input class="myClass" type='submit' name='sub' value='Save Selection'/>
</div>
</form>
</div>
<div id="clear">
</div>
</div>
			
			</div>
		
		</div>
