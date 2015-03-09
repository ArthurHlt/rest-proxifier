<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>Route</th>
        <th>Api</th>
        <th>Created By</th>
        <?php if ($asDb): ?>
            <th>&nbsp;</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php $i = 1;
    foreach ($proxies as $proxy): ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $proxy['route']; ?></td>
            <td><?php echo $proxy['api']; ?></td>
            <td><?php echo ucfirst($proxy['from']); ?></td>
            <?php if ($asDb): ?>
                <?php if ($proxy['from'] == 'Database'): ?>
                    <td>
                        <a href="<?php echo $this->route('showEdit') . $proxy['route'] . '#editProxy'; ?>"><span
                                class="glyphicon glyphicon-pencil"
                                aria-hidden="true"></span></a>
                        <a href="<?php echo $this->route('deleteEdit') . $proxy['route']; ?>"><span
                                class="glyphicon glyphicon-remove"
                                aria-hidden="true"></span></a>
                    </td>
                <?php else: ?>
                    <td>&nbsp;</td>
                <?php endif; ?>
            <?php endif; ?>

        </tr>
        <?php $i++; endforeach; ?>
    </tbody>
</table>