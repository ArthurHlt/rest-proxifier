<div class="addEditProxy" style="display: block;" id="editProxy">
    <form method="post" action="<?php echo $this->route('edit'); ?>">
        <fieldset>
            <legend>Edit proxy "<?php echo $proxy['route']; ?>"</legend>
            <div class="form-group">
                <label for="routeApi">Api *</label>
                <input type="text" name="api" class="form-control" id="routeApi"
                       placeholder="Enter api, e.g: http://exemple.com" value="<?php echo $proxy['api']; ?>">
            </div>
            <div class="form-group">
                <label for="routeResponseContent">Response content [Optional]</label>
                <textarea name="responseContent" class="form-control"
                          id="routeResponseContent"><?php echo $proxy['response-content']; ?></textarea>
            </div>
            <input type="hidden" name="route" class="route" value="<?php echo $proxy['route']; ?>"/>
            <input type="hidden" name="sent" value="1"/>
            <button type="submit" class="btn btn-default">Edit</button>
        </fieldset>
    </form>
</div>