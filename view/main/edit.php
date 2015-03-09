<?php $this->layout('layout.php', array('title' => 'Admin Ui')) ?>
<ul class="controls">
    <li>
        <a class="btn btn-default addProxy" aria-label="Right Align"
           href="<?php echo $this->route('index'); ?>#addProxy">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Proxy
        </a>
    </li>
</ul>
<?php $this->insert('include/table.php', ['proxies' => $proxies]) ?>
<?php $this->insert('include/editProxy.php', ['proxy' => $proxyEdit]); ?>

