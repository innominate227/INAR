<?php
	extract($data);
	Yii::app()->loadHelper('admin/htmleditor');
	PrepareEditorScript(false, $this);
?>
<script type="text/javascript">
    standardtemplaterooturl='<?php echo Yii::app()->getConfig('standardtemplaterooturl');?>';
    templaterooturl='<?php echo Yii::app()->getConfig('usertemplaterooturl');?>';
</script>
<?php if ($inar_menu_only == false) { ?>
	<div class='header ui-widget-header'><?php $clang->eT("Create, import, or copy survey"); ?></div>
<?php } else { ?>
	<div class='header ui-widget-header'><?php $clang->eT("Create new survey"); ?></div>
<?php } ?>
<?php
    $this->render('/admin/survey/subview/tab_view',$data);
    $this->render('/admin/survey/subview/tabGeneralNewSurvey_view',$data);	
	if ($inar_menu_only) {
		echo '<div style="display: none;">';
	}	
		$this->render('/admin/survey/subview/tabPresentation_view',$data);
		$this->render('/admin/survey/subview/tabPublication_view',$data);
		$this->render('/admin/survey/subview/tabNotification_view',$data);
		$this->render('/admin/survey/subview/tabTokens_view',$data);		
	if ($inar_menu_only) {
		echo '</div>';
	}
?>

<input type='hidden' id='surveysettingsaction' name='action' value='insertsurvey' />
</form>
<?php
	if ($inar_menu_only) {
		echo '<div style="display: none;">';
	}
		$this->render('/admin/survey/subview/tabImport_view',$data);
		$this->render('/admin/survey/subview/tabCopy_view',$data);
	if ($inar_menu_only) {
		echo '</div>';
	}
?>
</div>

<p><button id='btnSave' onclick="if (isEmpty(document.getElementById('surveyls_title'), '<?php $clang->eT("Error: You have to enter a title for this survey.", 'js');?>')) { document.getElementById('addnewsurvey').submit();}" class='standardbtn' >
        <?php $clang->eT("Save");?>
    </button>
</p>

