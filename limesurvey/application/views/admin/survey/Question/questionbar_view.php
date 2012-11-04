<?php
$aReplacementData=array();
if (isset($tmp_survlangs)) { ?>
    <div class="langpopup" id="previewquestionpopup"><?php $clang->eT("Please select a language:"); ?><ul>
            <?php foreach ($tmp_survlangs as $tmp_lang)
                { ?>
                <li><a target='_blank' onclick="$('#previewquestion').qtip('hide');" href='<?php echo $this->createUrl("survey/index/action/previewquestion/sid/" . $surveyid . "/gid/" . $gid . "/qid/" . $qid . "/lang/" . $tmp_lang); ?>' accesskey='d'><?php echo getLanguageNameFromCode($tmp_lang,false); ?></a></li>
                <?php } ?>
        </ul></div>
    <?php } ?>
<div class='menubar-title ui-widget-header'>
    <strong><?php $clang->eT("Question"); ?></strong> <span class='basic'><?php echo ellipsize(FlattenText($qrrow['question']),200); ?> (<?php echo $clang->gT("ID").":".$qid; ?>)</span>
</div>
<div class='menubar-main'>
    <div class='menubar-left'>
	<ul class='sf-menu'>
        <?php if(hasSurveyPermission($surveyid,'surveycontent','read'))
            {
                if (count(Survey::model()->findByPk($surveyid)->additionalLanguages) == 0)
                { ?>
				<li>
					<a accesskey='q' href="<?php echo $this->createUrl("survey/index/action/previewquestion/sid/" . $surveyid . "/gid/" . $gid . "/qid/" . $qid); ?>" target="_blank">
						<img style='float: left;' src='<?php echo $sImageURL; ?>preview.png' alt='<?php $clang->eT("Preview this question"); ?>' />
						<div style='float: right; line-height: 1.2em; padding: 3px;'>Test<br>Question</div>
					</a>
				</li>                
                <?php } else { ?>
				<li>
					<a accesskey='q' id='previewquestion'>
						<img style='float: left;' src='<?php echo $sImageURL; ?>preview.png' title='' alt='<?php $clang->eT("Preview This Question"); ?>' />
						<div style='float: right; line-height: 1.2em; padding: 3px;'>Test<br>Question</div>
					</a>
                </li>   
                <?php }
        } ?>


        <?php  if(hasSurveyPermission($surveyid,'surveycontent','update'))
            { ?>
				<li>
				<a href='<?php echo $this->createUrl("admin/question/editquestion/surveyid/".$surveyid."/gid/".$gid."/qid/".$qid); ?>'>
					<img style='float: left;' src='<?php echo $sImageURL; ?>edit.png' alt='<?php $clang->eT("Edit Current Question"); ?>' />
					<div style='float: right; line-height: 1.2em; padding: 3px;'>Edit<br>Question</div>
				</a>
				</li>
            <?php } ?>

        <?php 
		if ($inar_menu_only == false) {
		if(hasSurveyPermission($surveyid,'surveycontent','read'))
            { ?>
				<li>
					<a target='_blank' href="<?php echo $this->createUrl("admin/expressions/survey_logic_file/sid/{$surveyid}/gid/{$gid}/qid/{$qid}/"); ?>">
						<img style='float: left;' src='<?php echo $sImageURL; ?>quality_assurance.png' alt='<?php $clang->eT("Check survey logic for current question"); ?>' />						
					</a>
				</li>
            <?php } }?>
        <?php if ($activated != "Y")
            {?>
				<li>
					<a href='#'
					onclick="if (confirm('<?php $clang->eT("Deleting this question will also delete any answer options and subquestions it includes. Are you sure you want to continue?","js"); ?>')) { <?php echo convertGETtoPOST($this->createUrl("admin/question/delete/surveyid/$surveyid/gid/$gid/qid/$qid")); ?>}">
						<img style='float: left;' style='<?php echo (hasSurveyPermission($surveyid,'surveycontent','delete')?'':'visibility: hidden;');?>' src='<?php echo $sImageURL; ?>delete.png' alt='<?php $clang->eT("Delete current question"); ?>'/>
						<div style='float: right; line-height: 1.2em; padding: 3px;'>Delete<br>Question</div>
					</a>
				</li>
            <?php }
            else
            { ?>
				<li>
					<a href='<?php echo $this->createUrl('admin/survey/view/surveyid/'.$surveyid.'/gid/'.$gid.'/qid/'.$qid); ?>'
					onclick="alert('<?php $clang->eT("You can't delete this question because the survey is currently active.","js"); ?>')">
						<img style='float: left;' src='<?php echo $sImageURL; ?>delete_disabled.png' alt='<?php $clang->eT("Disabled - Delete current question"); ?>' />
						<div style='float: right; line-height: 1.2em; padding: 3px;'>Delete<br>Question</div>
					</a>
				</li>
            <?php }



			if ($inar_menu_only == false) {
            if(hasSurveyPermission($surveyid,'surveycontent','export'))
            { ?>
				<li>
					<a href='<?php echo $this->createUrl("admin/export/question/surveyid/$surveyid/gid/$gid/qid/$qid");?>'>
						<img style='float: left;' src='<?php echo $sImageURL; ?>dumpquestion.png' alt='<?php $clang->eT("Export this question"); ?>' />						
					</a>
				</li>
            <?php } } ?>

        

        <?php 
		
			if ($inar_menu_only == false) {
				if(hasSurveyPermission($surveyid,'surveycontent','create'))
				{
					if ($activated != "Y")
					{ ?>
						<li>
							<a href='<?php echo $this->createUrl("admin/question/copyquestion/surveyid/$surveyid/gid/$gid/qid/$qid");?>'>
								<img style='float: left;' src='<?php echo $sImageURL; ?>copy.png'  alt='<?php $clang->eT("Copy Current Question"); ?>' />								
							</a>
						</li>						
					<?php }
					else
					{ ?>
						<li>
							<a href='#' onclick="alert('<?php $clang->eT("You can't copy a question if the survey is active.","js"); ?>')">
								<img style='float: left;' src='<?php echo $sImageURL; ?>copy_disabled.png' alt='<?php $clang->eT("Copy Current Question"); ?>' />								
							</a>
						</li>						
					<?php }
				}
				else
				{ ?>				
				<?php } 

				if(hasSurveyPermission($surveyid,'surveycontent','update'))
				{ ?>
					<li>
						<a href="<?php echo $this->createUrl("admin/conditions/index/subaction/editconditionsform/surveyid/$surveyid/gid/$gid/qid/$qid"); ?>">
							<img style='float: left;' src='<?php echo $sImageURL; ?>conditions.png' alt='<?php $clang->eT("Set conditions for this question"); ?>'  />							
						</a>
					</li>				
				<?php }
				else
				{ ?>				
				<?php }
			
			}





            if(hasSurveyPermission($surveyid,'surveycontent','read'))
            {
                if ($qtypes[$qrrow['type']]['subquestions'] >0)
                { ?>
					<li>
						<a href='<?php echo $this->createUrl('admin/question/subquestions/surveyid/'.$surveyid.'/gid/'.$gid.'/qid/'.$qid); ?>'>
							<img style='float: left;' src='<?php echo $sImageURL; ?><?php if ($qtypes[$qrrow['type']]['subquestions']==1){?>subquestions.png<?php } else {?>subquestions2d.png<?php } ?>' alt='<?php $clang->eT("Edit subquestions for this question"); ?>' />
							<div style='float: right; line-height: 1.2em; padding: 3px;'>Edit<br>Subquestions</div>
						</a>
					</li>
                <?php }
            }
            else
            { ?>            
            <?php }




            if(hasSurveyPermission($surveyid,'surveycontent','read') && $qtypes[$qrrow['type']]['answerscales'] > 0)
            { ?>
				<li>
					<a href='<?php echo $this->createUrl('admin/question/answeroptions/surveyid/'.$surveyid.'/gid/'.$gid.'/qid/'.$qid); ?>'>
						<img style='float: left;' src='<?php echo $sImageURL; ?>answers.png' alt='<?php $clang->eT("Edit answer options for this question"); ?>' />
						<div style='float: right; line-height: 1.2em; padding: 3px;'>Edit<br>Options</div>
					</a>
				</li>
            <?php }
            else
            { ?>            
            <?php }




            if(hasSurveyPermission($surveyid,'surveycontent','read') && $qtypes[$qrrow['type']]['hasdefaultvalues'] >0)
            { ?>
				<li>
					<a href='<?php echo $this->createUrl('admin/question/editdefaultvalues/surveyid/'.$surveyid.'/gid/'.$gid.'/qid/'.$qid); ?>'>
						<img style='float: left;' src='<?php echo $sImageURL; ?>defaultanswers.png' alt='<?php $clang->eT("Edit default answers for this question"); ?>' />
						<div style='float: right; line-height: 1.2em; padding: 3px;'>Default<br>Answers</div>
					</a>
				</li>
            <?php } ?>
	</ul>
    </div>    
</div>

<p style='margin:0;font-size:1px;line-height:1px;height:1px;'>&nbsp;</p>


<table  id='questiondetails' <?php echo $qshowstyle; ?>><tr><td><strong>
            <?php $clang->eT("Code:"); ?></strong></td>
        <td><?php echo $qrrow['title']; ?>
            <?php if ($qrrow['type'] != "X")
                {
                    if ($qrrow['mandatory'] == "Y") { ?>
                    : (<i><?php $clang->eT("Mandatory Question"); ?></i>)
                    <?php }
                    else { ?>
                    : (<i><?php $clang->eT("Optional Question"); ?></i>)
                    <?php }
            } ?>
        </td></tr>
    <tr><td><strong>
            <?php $clang->eT("Question:"); ?></strong></td><td>
            <?php
                templatereplace($qrrow['question'],array(),$aReplacementData,'Unspecified', false ,$qid);
                echo FlattenText(LimeExpressionManager::GetLastPrettyPrintExpression(), true);
        ?></td></tr>
    <tr><td><strong>
            <?php $clang->eT("Help:"); ?></strong></td><td>
            <?php
                if (trim($qrrow['help'])!=''){
                    templatereplace($qrrow['help'],array(),$aReplacementData,'Unspecified', false ,$qid);
                    echo FlattenText(LimeExpressionManager::GetLastPrettyPrintExpression(), true);
            } ?>
        </td></tr>
    <?php if ($qrrow['preg'])
        { ?>
        <tr ><td><strong>
                <?php $clang->eT("Validation:"); ?></strong></td><td><?php echo htmlspecialchars($qrrow['preg']); ?>
            </td></tr>
        <?php } ?>

    <tr><td><strong>
            <?php $clang->eT("Type:"); ?></strong></td><td><?php echo $qtypes[$qrrow['type']]['description']; ?>
        </td></tr>
    <?php if ($qct == 0 && $qtypes[$qrrow['type']]['answerscales'] >0)
        { ?>
        <tr ><td></td><td>
                <span class='statusentryhighlight'>
                    <?php $clang->eT("Warning"); ?>: <a href='<?php echo $this->createUrl("admin/question/answeroptions/surveyid/$surveyid/gid/$gid/qid/$qid"); ?>'><?php $clang->eT("You need to add answer options to this question"); ?>
                        <img src='<?php echo $sImageURL; ?>answers_20.png' title='<?php $clang->eT("Edit answer options for this question"); ?>' /></a></span></td></tr>
        <?php }


        if($sqct == 0 && $qtypes[$qrrow['type']]['subquestions'] >0)
        { ?>
        <tr ><td></td><td>
                <span class='statusentryhighlight'>
                    <?php $clang->eT("Warning"); ?>: <a href='<?php echo $this->createUrl("admin/question/subquestions/surveyid/$surveyid/gid/$gid/qid/$qid"); ?>'><?php $clang->eT("You need to add subquestions to this question"); ?>
                        <img src='<?php echo $sImageURL; ?><?php if ($qtypes[$qrrow['type']]['subquestions']==1){?>subquestions_20<?php } else {?>subquestions2d_20<?php } ?>.png' title='<?php $clang->eT("Edit subquestions for this question"); ?>' /></a></span></td></tr>
        <?php }

        if ($qrrow['type'] == "M" or $qrrow['type'] == "P")
        { ?>
        <tr>
            <td><strong>
                <?php $clang->eT("Option 'Other':"); ?></strong></td>
            <td>
                <?php if ($qrrow['other'] == "Y") { ?>
                    <?php $clang->eT("Yes"); ?>
                    <?php } else
                    { ?>
                    <?php $clang->eT("No"); ?>

                    <?php } ?>
            </td></tr>
        <?php }
        if (isset($qrrow['mandatory']) and ($qrrow['type'] != "X") and ($qrrow['type'] != "|"))
        { ?>
        <tr>
            <td><strong>
                <?php $clang->eT("Mandatory:"); ?></strong></td>
            <td>
                <?php if ($qrrow['mandatory'] == "Y") { ?>
                    <?php $clang->eT("Yes"); ?>
                    <?php } else
                    { ?>
                    <?php $clang->eT("No"); ?>

                    <?php } ?>
            </td>
        </tr>
        <?php } ?>
    <?php if (trim($qrrow['relevance']) != '') { ?>
        <tr>
            <td><?php $clang->eT("Relevance equation:"); ?></td>
            <td>
                <?php
                    LimeExpressionManager::ProcessString("{" . $qrrow['relevance'] . "}", $qid);    // tests Relevance equation so can pretty-print it
                    echo LimeExpressionManager::GetLastPrettyPrintExpression();
                ?>
            </td>
        </tr>
        <?php } ?>
    <?php
        $sCurrentCategory='';
        foreach ($advancedsettings as $aAdvancedSetting)
        { ?>
        <tr>
            <td><?php echo $aAdvancedSetting['caption'];?>:</td>
            <td><?php
                    if ($aAdvancedSetting['i18n']==false)  echo htmlspecialchars($aAdvancedSetting['value']); else echo htmlspecialchars($aAdvancedSetting[$baselang]['value'])?>
            </td>
        </tr>
        <?php } ?>
            </table>
