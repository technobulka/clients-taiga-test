<div class="row align-items-center">
    <div class="col-9"><h1>Clients</h1></div>
    <div class="col-3">
        <form class="input-group">
            <input type="text" name="q" value="<?php echo $q ?>" placeholder="Search" class="form-control form-control-sm">
            <div class="input-group-append"><button type="submit" class="btn btn-sm btn-outline-secondary">&rarr;</button></div>
            <div class="input-group-append"><a href="/" class="btn btn-sm btn-outline-danger" title="clear">&times;</a></div>
        </form>
    </div>
</div>

<table class="table">
    <?php if ($clients): ?>
        <thead>
            <tr>
                <?php foreach ($cols as $col): ?>
                <th scope="col"><?php echo $col['Field'] ?></th>
                <?php endforeach; ?>

                <th scope="col">phones</th>
                <th scope="col" style="width: 1%;"><a class="btn btn-sm btn-block btn-primary" href="/create">create new</a></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($clients as $client): ?>
            <tr scope="row">
                <?php
                    $id = $client['id'];
                    foreach ($client as $key => $value) {
                        if ($key == 'phones') {
                            echo '<td>'. str_replace(',', '<br>', $value) .'</td>';
                        } else {
                            echo '<td>'. $value .'</td>';
                        }
                    }
                ?>

                <td>
                    <div class="btn-group">
                        <a class="btn btn-sm btn-outline-secondary" href="/view/<?php echo $id ?>">view</a>
                        <a class="btn btn-sm btn-outline-secondary" href="/edit/<?php echo $id ?>">edit</a>
                        <a class="btn btn-sm btn-outline-danger" href="/delete/<?php echo $id ?>" onclick="return confirm('Are you sure?')">delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    <?php else: ?>
        <thead>
            <tr>
                <th>No clients found.</th>
                <th scope="col" style="width: 1%;"><a class="btn btn-sm btn-block btn-primary" href="/create">create new</a></th>
            </tr>
        </thead>
    <?php endif; ?>

</table>