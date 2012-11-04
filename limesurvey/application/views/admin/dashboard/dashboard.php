<!-- This is important to let the collaspable divs work -->
<script type='text/javascript'>    
    var showTextInline="<?php echo $showtextinline ?>";
</script>


<div class='header ui-widget-header header_statistics'>
	<div style='float:right;'><img src='<?php echo $sImageURL; ?>/maximize.png' id='showsurveys' alt='<?php $clang->eT("Maximize"); ?>'/><img src='<?php echo $sImageURL; ?>/minimize.png' id='hidesurveys' alt='<?php $clang->eT("Minimize"); ?>'/></div>
	Dashboard - Surveys
</div>
<div id='surveys' class='statisticsfilters'  <?php if ($view!='' && $view!='surveys') { echo " style='display:none' "; } ?>>	
	<p>	
		<font size="4"><a href='<?php echo Yii::app()->getController()->createUrl("admin/survey/newsurvey"); ?>'>Create New Survey</a></font><br>		
	
		<table class='users'>		
		<thead><tr><th class='header' style='width:30%'>Name</th><th class='header' style='width:10%'>Active</th><th class='header' style='width:10%'>Edit</th><th class='header' style='width:10%'>Export</th><th class='header' style='width:40%'>Issues</th></tr></thead>
		<tbody>
		<?php
		$even_odd = 'odd';
		foreach($surveys as $survey)
		{					
			echo "<tr class='" . $even_odd . "'><td>" . $survey['title'] . "</td><td>" . $survey['active'] . "</td>";
			echo "<td><a href='" . Yii::app()->getController()->createUrl('admin/survey/view/surveyid/' . $survey['id']) . "'>Edit</a></td>";
			echo "<td>";
			if ($survey['active'] == 'Y')
			{
				echo "<a href='" . Yii::app()->getController()->createUrl('admin/export/exportresults/surveyid/' . $survey['id']) . "'>Export</a>";
			}
			echo "</td>";
			echo "<td>" . $survey['issues'] . "</td></tr>";
			if ($even_odd == 'odd') { $even_odd = 'even'; } else { $even_odd = 'odd'; }
		}
		?>				
		</tbody>
		</table>
	</p>
</div>
<div style='clear: both'></div>    


<form method='post' name='formbuilder' action='<?php echo Yii::app()->getController()->createUrl("admin/statistics/"); ?>'>	
	<input name='run_saved_report' value='yes' type='hidden' />
    <div class='header ui-widget-header header_statistics'>
        <div style='float:right;'><img src='<?php echo $sImageURL; ?>/maximize.png' id='showreports' alt='<?php $clang->eT("Maximize"); ?>'/><img src='<?php echo $sImageURL; ?>/minimize.png' id='hidereports' alt='<?php $clang->eT("Minimize"); ?>'/></div>
        Dashboard - Reports
    </div>    
    <div id='reports' class='statisticsfilters' <?php if ($view!='' && $view!='reports') { echo " style='display:none' "; } ?>>
        <div id='statistics_general_filter'>
            <fieldset style="clear:both;">
			<legend>Report Settings</legend>
			<ul>
				<li>
					<label for="reportType">Report Type: </label>
					<select name="report_name" id="report_name">
					<?php
					foreach($report_names as $report_name)
					{					
						echo "<option value='" . $report_name ."' selected='selected'>" . $report_name . "</option>";
					}
					?>
					</select>
				</li>				
				<li>
					<label for="datestampE">Start Date:</label>				
					<input class='popupdatetime' size='12' id='datestampG' name='datestampG' value='' type='text' />
				</li>
				<li>
					<label for="datestampE">End Date:</label>
					<input class='popupdatetime' size='12' id='datestampL' name='datestampL' value='' type='text' />
				</li>
			</ul>
			</fieldset>	            
        </div>
        <p>
            <input type='submit' value='View Report' />            
        </p>
    </div>
</form>
<div style='clear: both'></div>  

