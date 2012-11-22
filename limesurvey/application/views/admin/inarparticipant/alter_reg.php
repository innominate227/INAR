
<html>
<head>
<style>
div#header
{
width:100%;
background:#F0F8FF;
overflow: hidden;
margin:0;
}
div#header ul{	
	  
		width:100%;
		list-style:none;
		margin:0;
		padding-left:1%;
                border-top:1px solid #F6EAFA;
                border-bottom:1px solid grey;

		
	}
	div#header ul li {
		display:inline;
		list-style:none;
		margin:0;
		padding:0;
                line-height:1.4em;
	}

	div#header ul li a 
	{
		margin:0;
                padding: 0.05em 1em;
		text-align:center;
		font-size:115%;
	        background:#d7ecff;
		text-decoration:none;
		line-height:1.4em;
                color:black;
                font-weight:bold;
	}
       div#header ul li a:hover
	{
	background:#FFFFE0;
	color:maroon;
	}
       div#header ul li a.active
	   {
        background:#FFFFE0;
        color:DarkGreen;
        }

#content_inner_wrapper label{
display:block;
margin-bottom:0.2em;
color:black;
}
#content_inner_wrapper form
{
text-align:center;
padding:0.5em;
}
#content_inner_wrapper label span
{
padding-right:0.5em;
}
.errr
{
text-align:center;
padding:0.5em;
color:maroon;
}
</style>
</head>	
		<div id="wrapper">
		<div id="header">    	
							<ul>							
	
							<li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a class="active" href="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>">Configure Registration</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>">Assign Participants To Surveys</a></li>
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">


<?php	
	if (isset($_POST['process_alter_table'])) 
	{		
		$add_term = $_POST['column_name'];
                $data_type = $_POST['data'];	
                $participants_table = 'inar_participants';
                $sql1= "INSERT INTO `reg_form_fields` (`name`, `visible`) VALUES ('$add_term',1)";
                if ($data_type=="VARCHAR")
                {
                $sql = "ALTER TABLE `inar_participants` ADD `{$add_term}` VARCHAR(255) NOT NULL";
                }
                else if($data_type=="NUMBER")
                {
                $sql = "ALTER TABLE `inar_participants` ADD `{$add_term}` INT(10) NOT NULL";                
                }
                else if($data_type=="DATE")
                {
                $sql = "ALTER TABLE `inar_participants` ADD `{$add_term}` DATE NOT NULL";                
				}
        
        try
              {
					$command=Yii::app()->db->createCommand($sql);			
					$command->execute();
					
					$command=Yii::app()->db->createCommand($sql1);			
					$command->execute();			  
                     $site="new_reg.php";
       
              }
        catch (Exception $e) 
             {
              echo '<div class="errr">';
              echo 'Oops. The registration form was not altered! ',  $e->getMessage(), "\n";
              echo '</div>';
             }
           
 
} ?>

<?php
if(array_key_exists('sub',$_POST))
        {
          echo '<div class="errr">';
         echo "Hiding a Field From The Registration Form - Currently not Implemented!";
         echo '</div>';
     }
?>
<p style="font-weight:bold;"> Current fields in the Registration Form </p>
<?php
$sql1 = 'SELECT `COLUMN_NAME`'
        . ' FROM `INFORMATION_SCHEMA`.`COLUMNS` '
        . ' WHERE `TABLE_SCHEMA`=\'inar2b\' '
        . ' AND `TABLE_NAME`=\'inar_participants\' AND `COLUMN_NAME` != \'id\' AND `COLUMN_NAME` != \'date\';';

$col_names=array();

$command=Yii::app()->db->createCommand($sql1);			
$results=$command->queryAll();
$count=count($results);
foreach ($results as $row) 
{
$col_names[]=$row['COLUMN_NAME'];
}?>
<form method='post'>
<?php for($i=0;$i<$count;$i++)
{

$sql5 = "SELECT `visible` FROM `reg_form_fields` WHERE `name`= '$col_names[$i]'";

$command=Yii::app()->db->createCommand($sql5);			
$results=$command->queryAll();
$vis = $results[0];
?>
<label> <span><?php echo $col_names[$i]; ?> </span> <input name="names[]" type="checkbox" value="<?php echo $i;?>" <?php if($vis)echo 'checked="checked"'?> /> </label>
<?php } ?>
<div style="margin:0px auto;width: 50%;padding:1.4em;">
<input class="myClass" type='submit' name='sub' value='Save Selection'/>
</div>
</form>

<p style="font-weight:bold;"> Alter Registration Form.</p>
<p>  Choose what kind of field (name and type) to add </p>
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
		
		</div>
</div>
</html>