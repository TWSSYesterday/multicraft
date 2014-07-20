<?php
/**
 *
 *   Copyright © 2010-2012 by xhost.ch GmbH
 *
 *   All rights reserved.
 *
 **/

$this->pageTitle=Yii::app()->name . ' - '.Yii::t('admin', 'Update Minecraft');
$this->breadcrumbs=array(
    Yii::t('admin', 'Settings')=>array('index'),
    Yii::t('admin', 'Update Minecraft'),
);

$this->menu=array(
    array(
        'label'=>Yii::t('admin', 'Add or Remove Files'),
        'url'=>array('daemon/files'),
        'icon'=>'file',
    ),
    array(
        'label'=>Yii::t('admin', 'Back'),
        'url'=>array('daemon/index'),
        'icon'=>'arrow-left',
    ),
);
?>

<form action="" method="post">
<h3>Update file</h3>
<br>
<div class="row">
    <div class="col-md-3">
        <?php echo CHtml::dropDownList('update-target', '', $jars) ?>
    </div>
    <div class="col-md-3">
         <?php echo CHtml::dropDownList('update-file', '', $file) ?>
    </div>
    <div class="col-md-6">
        <?php echo Yii::t('admin', 'All Servers:') ?>
        <?php
        echo CHtml::ajaxSubmitButton(Yii::t('admin', 'Download'), '', array('type'=>'POST',
            'data'=>array('ajax'=>'start', 'target'=>"js:$('#update-target').val()",
            'file'=>"js:$('#update-file').val()", Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,),
            'success'=>'update_response'), array('class' => 'btn btn-default'));
        echo '&nbsp;';
        echo CHtml::ajaxSubmitButton(Yii::t('admin', 'Install'), '', array('type'=>'POST',
            'data'=>array('ajax'=>'install', Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,),
            'success'=>'update_response'), array('class' => 'btn btn-primary'));
        ?>
    </div>
</div>
</form>
<br/>

<?php $w = $this->widget('zii.widgets.CListView', array(
    'emptyText'=>Yii::t('admin', 'No daemons found.').'<br/><br/>'.Yii::t('admin', 'Please check that at least one daemon is started and that it uses the same database you configured as the daemon database using the control panel installer.'),
    'dataProvider'=>$daemonList,
    'itemView'=>'_updateBox',
    'loadingCssClass'=>'',
    'beforeAjaxUpdate'=>'function(id){ stopRefreshList(id); }',
    'afterAjaxUpdate'=>'function(id, data){ scheduleRefreshList(id, data); }',
    'itemsTagName' => 'table',
)); ?>

<?php echo CHtml::script('

    function download(daemon)
    {
        '.CHtml::ajax(array(
            'type'=>'POST',
            'data'=>array(
                'ajax'=>'start',
                'daemon'=>'js:daemon',
                'target'=>"js:$('#update-target').val()",
                'file'=>"js:$('#update-file').val()",
                Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,
                ),
            'success'=>'update_response'
            )).'
        return false;
    }
    function install(daemon)
    {
        '.CHtml::ajax(array(
            'type'=>'POST',
            'data'=>array(
                'ajax'=>'install',
                'daemon'=>'js:daemon',
                'file'=>"js:$('#update-file').val()",
                Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,
                ),
            'success'=>'update_response'
            )).'
        return false;
    }

    function update_response(data) {
        if (data && data.length)
            alert(data);
    }

    var refreshTimer = 0;
    function stopRefreshList(id) {
        if (refreshTimer)
            clearTimeout(refreshTimer);
    }
    function scheduleRefreshList(id, data) {
        stopRefreshList(id);
        refreshTimer = setTimeout(function() {jQuery("#"+id).yiiListView.update(id);}, 2000);
    }
    $(document).ready(function() {
        scheduleRefreshList("'.$w->id.'", "");
    });
    ') ?>

<div class="infoBox">
<?php echo Yii::t('admin', 'If you get an error when downloading the JAR file, try downloading and installing the .conf file first in case the JAR download location has changed in the configuration.') ?><br/>
<br/>
<?php echo Yii::t('admin', '<b>.conf file</b>: Contains server binary specific configuration and download links') ?><br/>
<?php echo Yii::t('admin', '<b>JAR File</b>: The server binary itself') ?>
</div>
<br/>