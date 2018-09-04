<h1>Create new client</h1>

<form action="/create" method="post">
    <?php foreach ($cols as $col) if (!in_array($col['Field'], ['id', 'created', 'edited'])) { ?>
    <div class="form-group row">
        <label class="col-2 col-form-label"><?php echo $col['Field'] ?></label>

        <div class="col-10">
        <?php if (substr($col['Type'], 0, 4) == 'enum') {
            $enum = explode(',', substr($col['Type'], 5, -1));

            echo '<select class="form-control" name="'. $col['Field'] .'">';
            foreach ($enum as $el) {
                $el = trim($el, "'");
                echo '<option value="'. $el .'">'. $el .'</option>';
            }
            echo '</select>';
        ?>
        <?php } else { ?>
            <input type="text" class="form-control" name="<?php echo $col['Field'] ?>">
        <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="form-group row phones">
        <label class="col-2 col-form-label">phones</label>

        <div class="col-10 input-group proto mt-3 offset-2 d-none">
            <input type="tel" class="form-control" name="phones[]">
            <div class="input-group-append"><button class="btn btn-outline-secondary del" type="button" tabindex="-1">del</button></div>
        </div>

        <div class="col-10 input-group">
            <input type="tel" class="form-control" name="phones[]">
            <div class="input-group-append"><button class="btn btn-outline-secondary add" type="button" tabindex="-1">add</button></div>
        </div>
    </div>

    <a href="/" class="btn btn-secondary">Back</a> <button type="submit" class="btn btn-primary">Submit</button>
</form>