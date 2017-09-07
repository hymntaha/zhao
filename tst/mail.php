<?php

require_once dirname(__FILE__) . '/../config.php';


//mail('andrew@bravoyourcity.com', 'the subject', 'the message', null,   '-finfo@bravoyourcity.com');
email::mail('aegrumet+test@gmail.com', 'testing filter with link and no sender', 'the message http://www.bravoyourcity.com/');

print "Sent\n";