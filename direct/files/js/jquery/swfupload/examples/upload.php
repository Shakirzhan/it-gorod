<?php

// we don't actually save the file, since this is just a demo - we just say that we did
file_put_contents(dirname(__FILE__).'/log.txt', 'test');
echo '1';

?>