<div class="row align-items-center justify-content-between">
    <div class="col-auto">
        <h1>Client</h1>
    </div>
    <div class="col-auto btn-group">
        <a class="btn btn-sm btn-outline-secondary" href="/edit/<?php echo $id ?>">edit</a>
        <a class="btn btn-sm btn-outline-danger" href="/delete/<?php echo $id ?>" onclick="return confirm('Are you sure?')">delete</a>
    </div>
</div>

<table class="table">
    <tbody>
        <?php foreach ($cols as $col): ?>
        <tr>
            <th scope="row"><?php echo $col['Field'] ?></th>
            <td><?php echo $client[$col['Field']] ?></td>
        </tr>
        <?php endforeach; ?>

        <tr>
            <th scope="row">phones</th>
            <td><?php echo implode('<br>', $client['phones']) ?></td>
        </tr>
    </tbody>
</table>

<a href="/" class="btn btn-secondary">Back</a>