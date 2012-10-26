<!-- This is important to let the collaspable divs work -->
<script type='text/javascript'>    
    var showTextInline="<?php echo $showtextinline ?>";
</script>

<form method='post' name='formbuilder' action='<?php echo Yii::app()->getController()->createUrl("admin/statistics/index/surveyid/753971"); ?>'>
	<input type="hidden" name="completionstate" value="all" id="completionstate" />
	<input type="hidden" name="statlang"  value="en" id="statlang" />
	<input type="hidden" id="outputtypehtml" name="outputtype" value="html" checked="checked" />
	<input type="hidden" id="usegraph" name="usegraph" checked="checked" />
	<input type="hidden" id="showtextinline" name="showtextinline" checked="checked" />
	<input type="hidden" id="filter753971X2X4" name="summary[]" value="753971X2X4" checked="checked" />	
	<input type='hidden' name='summary[]' value='datestampE' />
	<input type='hidden' name='summary[]' value='datestampG' />
	<input type='hidden' name='summary[]' value='datestampL' />
	

    <div class='header ui-widget-header header_statistics'>
        <div style='float:right;'><img src='<?php echo $sImageURL; ?>/maximize.png' id='showreports' alt='<?php $clang->eT("Maximize"); ?>'/><img src='<?php echo $sImageURL; ?>/minimize.png' id='hidereports' alt='<?php $clang->eT("Minimize"); ?>'/></div>
        Dashboard - Reports
    </div>    
    <div id='reports' class='statisticsfilters'>
        <div id='statistics_general_filter'>
            <fieldset style="clear:both;">
			<legend>Report Settings</legend>
			<ul>
				<li>
					<label for="reportType">Report Type: </label>
					<select name="reportType" id="reportType">
						<option value="gender" selected="selected">Gender</option>						
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

<div class='header ui-widget-header header_statistics'>
	<div style='float:right;'><img src='<?php echo $sImageURL; ?>/maximize.png' id='showsurveys' alt='<?php $clang->eT("Maximize"); ?>'/><img src='<?php echo $sImageURL; ?>/minimize.png' id='hidesurveys' alt='<?php $clang->eT("Minimize"); ?>'/></div>
	Dashboard - Surveys
</div>
<div id='surveys' class='statisticsfilters'>	
	<p>	
		<a href='<?php echo Yii::app()->getController()->createUrl("admin/survey/newsurvey"); ?>'>New Survey</a><br>		
		TODO: list all the surveys, button to set all properties of a survey correctly for INAR.
	</p>
</div>
<div style='clear: both'></div>  

