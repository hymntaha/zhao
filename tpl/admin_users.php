
<?php  require_once 'tpl/header.php'; ?>

<div class="outer-container">
  <div class="container">
    <div class="admintable">

<table border="0" cellspacing="0" cellpadding="0">

  <tr>
    <th>username</th>
    <th>email</th>
    <th>created</th>
  </tr>

  <?php $i = 0;?>
  <?php foreach ($users as $user):?>
  <?php $i++;?>
  <tr <?=($i%2) ? 'class="odd"' : ''?>>
    <td><a href="/#authors=<?=$user->slug?>"><?=$user->username?></a></td>
    <td><?=$user->email?></td>
    <td><?=$user->created_readable?> (<?=$user->created_diff?>)</td>
  </tr>
  <?php endforeach?>

</table>

      </div>
    </div>
  </div>

<?php  require_once 'footer.php'; ?>
