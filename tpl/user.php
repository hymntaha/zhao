<div> body for user section </div>


<table cellpadding="0" cellspacing="0">
  <tr>
    <th>id</th>
    <th>name</th>
    <th>age</th>
    <th>created</th>
  </tr>
  <?php foreach ($users as $id=>$user):?>
  <tr>
    <td><?=$id?></td>
    <td><?=$user['name']?></td>
    <td><?=$user['age']?></td>
    <td><?=$user['created_readable']?></td>
  </tr>
  <?php endforeach?>
</table>

