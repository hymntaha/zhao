<?php

require_once(dirname(__FILE__) . '/../config.php');

$u = user::i(user::findOne(array('email'=>'aegrumet@alum.mit.edu')));

hook::run('user_new', $u);
