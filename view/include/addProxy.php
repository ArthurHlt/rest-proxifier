<div class="addEditProxy" id="addProxy">
    <form method="post" action="<?php echo $this->route('add'); ?>">
        <fieldset>
            <legend>Add a proxy</legend>

            <div class="form-group">
                <label for="routeProxy">Route *</label>
                <input type="text" name="route" class="form-control" id="routeProxy"
                       placeholder="Enter route, e.g: /exemple">
            </div>
            <div class="form-group">
                <label for="routeApi">Api *</label>
                <input type="text" name="api" class="form-control" id="routeApi"
                       placeholder="Enter api, e.g: http://exemple.com">
            </div>
            <div class="form-group">
                <label for="routeResponseContent">Response content [Optional]</label>
                <textarea name="responseContent" class="form-control" id="routeResponseContent"></textarea>
            </div>
            <input type="hidden" name="sent" value="1"/>
            <button type="submit" class="btn btn-default">Add</button>
        </fieldset>
    </form>
</div>