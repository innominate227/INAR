<strong><?php $clang->eT("Page:"); ?></strong>&nbsp;
<span class='basic'><?php echo $grow['group_name']; ?> (<?php $clang->eT("ID"); ?>:<?php echo $gid; ?>)</span>
<!--</div>-->
<div class='menubar-main'>
    <div class='menubar-left'>
	<ul class='sf-menu'>
		
        <?php if(hasSurveyPermission($surveyid,'surveycontent','update'))
            { ?>
			<li>            
            <a href="<?php echo $this->createUrl("survey/index/action/previewgroup/sid/$surveyid/gid/$gid/"); ?>" target="_blank">
                <img style='float: left;' src='<?php echo $imageurl; ?>preview.png' alt='<?php $clang->eT("Preview current page"); ?>' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>"/>
				<div style='float: right; line-height: 1.2em; padding: 3px;'>Test<br>Page</div>
			</a>
			</li>
            <?php }
            else{ ?>            
            <?php } ?>

        <?php if(hasSurveyPermission($surveyid,'surveycontent','update'))
            { ?>
			<li>             
				<a href="<?php echo $this->createUrl('admin/questiongroup/edit/surveyid/'.$surveyid.'/gid/'.$gid); ?>">
					<img style='float: left;' src='<?php echo $imageurl; ?>edit.png' alt='<?php $clang->eT("Edit current question group"); ?>' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>"/>
					<div style='float: right; line-height: 1.2em; padding: 3px;'>Edit<br>Page</div>
				</a>
			</li>
            <?php } ?>

        <?php 
		if ($inar_menu_only == false) {
			if(hasSurveyPermission($surveyid,'surveycontent','read'))
            { ?>
			<li>				
				<a  target='_blank' href="<?php echo $this->createUrl("admin/expressions/survey_logic_file/sid/{$surveyid}/gid/{$gid}/"); ?>">
					<img style='float: left;' src='<?php echo $imageurl; ?>quality_assurance.png' alt='<?php $clang->eT("Check survey logic for current question group"); ?>' />					
				</a>
			</li>
        <?php } } ?>

        <?php
            if (hasSurveyPermission($surveyid,'surveycontent','delete'))
            {
                if ((($sumcount4 == 0 && $activated != "Y") || $activated != "Y"))
                {
                    if (is_null($condarray))
                    { ?>
					<li>
						<a href='#' onclick="if (confirm('<?php $clang->eT("Deleting this group will also delete any questions and answers it contains. Are you sure you want to continue?","js"); ?>')) { window.open('<?php echo $this->createUrl("admin/questiongroup/delete/surveyid/$surveyid/gid/$gid"); ?>','_top'); }">
							<img style='float: left;' src='<?php echo $imageurl; ?>delete.png' alt='<?php $clang->eT("Delete current question group"); ?>' title='' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>"/>
							<div style='float: right; line-height: 1.2em; padding: 3px;'>Delete<br>Page</div>
						</a>
					</li>

                    <?php }
                    else
                    // TMSW Conditions->Relevance:  Should be allowed to delete group even if there are conditions/relevance, since separate view will show exceptions

                    { ?>
					<li>
						<a href='<?php echo $this->createUrl("admin/questiongroup/view/surveyid/$surveyid/gid/$gid"); ?>' onclick="alert('<?php $clang->eT("Impossible to delete this group because there is at least one question having a condition on its content","js"); ?>')">
							<img style='float: left;' src='<?php echo $imageurl; ?>delete_disabled.png' alt='<?php $clang->eT("Delete current question group"); ?>' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>"/>
							<div style='float: right; line-height: 1.2em; padding: 3px;'>Delete<br>Page</div>
						</a>
					</li>
                    <?php }
                }
                else
                { ?>
                <li><img src='<?php echo $imageurl; ?>blank.gif' alt='' height="<?php echo $iIconSize;?>" width='40' /></li>
                <?php }
            }
			if ($inar_menu_only == false) {
            if(hasSurveyPermission($surveyid,'surveycontent','export'))
            { ?>
			<li>
				<a href='<?php echo $this->createUrl("admin/export/group/surveyid/$surveyid/gid/$gid");?>'>
                <img  style='float: left;' src='<?php echo $imageurl; ?>dumpgroup.png' title='' alt='<?php $clang->eT("Export this question group"); ?>' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>"/></a>
			</li>
            <?php } } ?>
    
		<li>
		<div style='float: right; line-height: 1.2em; padding: 3px;'>
			Question:<br>
			<select class="listboxquestions" name='questionid' id='questionid'
				onchange="window.open(this.options[this.selectedIndex].value, '_top')">
				<?php echo getQuestions($surveyid,$gid,$qid); ?>
			</select>
		</div>
		</li>





        <?php if ($activated == "Y")
            { ?>
				<li>
					<a href='#'>
						<img src='<?php echo $imageurl; ?>add_disabled.png' title='' alt='<?php echo $clang->gT("Disabled").' - '.$clang->gT("This survey is currently active."); ?>' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>" />
					</a>
				</li>
            <?php }
            elseif(hasSurveyPermission($surveyid,'surveycontent','create'))
            { ?>
				<li>
					<a href='<?php echo $this->createUrl("admin/question/addquestion/surveyid/".$surveyid."/gid/".$gid); ?>'>
						<img style='float: left;' src='<?php echo $imageurl; ?>add.png' title='' alt='<?php $clang->eT("Add new question to group"); ?>' width="<?php echo $iIconSize;?>" height="<?php echo $iIconSize;?>" />
						<div style='float: right; line-height: 1.2em; padding: 3px;'>New<br>Question</div>
					</a>
				</li>
            <?php } ?>

        
        
	</ul>
    </div>
</div>





<table id='groupdetails' <?php echo $gshowstyle; ?> >
<tr ><td ><strong>
            <?php $clang->eT("Title"); ?>:</strong></td>
    <td>
        <?php echo $grow['group_name']; ?> (<?php echo $grow['gid']; ?>)</td>
</tr>
<tr>
    <td><strong>
        <?php $clang->eT("Description:"); ?></strong>
    </td>
    <td>
        <?php if (trim($grow['description'])!='') {
                templatereplace($grow['description']);
                echo LimeExpressionManager::GetLastPrettyPrintExpression();
        } ?>
    </td>
</tr>
<?php if (trim($grow['grelevance'])!='') { ?>
    <tr>
        <td><strong>
            <?php $clang->eT("Relevance:"); ?></strong>
        </td>
        <td>
            <?php
                templatereplace('{' . $grow['grelevance'] . '}');
                echo LimeExpressionManager::GetLastPrettyPrintExpression();
            ?>
        </td>
    </tr>
    <?php } ?>
<?php
    if (trim($grow['randomization_group'])!='')
    {?>
    <tr>
        <td><?php $clang->eT("Randomization group:"); ?></td><td><?php echo $grow['randomization_group'];?></td>
    </tr>
    <?php
    }
    // TMSW Conditions->Relevance:  Use relevance equation or different EM query to show dependencies
    if (!is_null($condarray))
    { ?>
    <tr><td><strong>
                <?php $clang->eT("Questions with conditions to this group"); ?>:</strong></td>
        <td>
            <?php foreach ($condarray[$gid] as $depgid => $deprow)
                {
                    foreach ($deprow['conditions'] as $depqid => $depcid)
                    {

                        $listcid=implode("-",$depcid);?>
                    <a href='<?php echo $this->createUrl("admin/conditions/markcid/" . implode("-",$depcid) . "/surveyid/$surveyid/gid/$depgid/qid/$depqid"); ?>'>[QID: <?php echo $depqid; ?>]</a>
                    <?php }
            } ?>
        </td></tr>
    <?php } ?>
