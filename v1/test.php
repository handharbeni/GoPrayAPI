<?php

$x = array(
                'method' => 'POST',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $_SERVER['REMOTE_ADDR'] 
            );

$x['method'] = 'test';

echo $x['method'];