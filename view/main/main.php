<?php $this->layout('layout.php', array('title' => 'Admin Ui')) ?>
<ul class="controls">
    <li>
        <button type="button" class="btn btn-default addProxy" aria-label="Right Align">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Proxy
        </button>
    </li>
</ul>
<div style="clear:both"></div>
<?php if (!empty($error)): ?>
    <br/>
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        <?php foreach ($error as $err): ?>
            <?php echo $err; ?>
            <br/>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php $this->insert('include/table.php', ['proxies' => $proxies]) ?>
<?php $this->insert('include/addProxy.php') ?>
