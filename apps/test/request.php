<?php

    $file = DAT . 'request-' . microtime ( TRUE ) . '.json';

    $data = [
        'headers' => getallheaders (),
        'get'     => $_GET,
        'post'    => $_POST,
        'data'    => file_get_contents ('php://input'),
        'files '  => $_FILES,
        'cookies' => $_COOKIE,
        'server'  => $_SERVER,
        'host'    => $_ENV ] ;

    header ( 'Content-Type: application/json' );

    echo json_encode ( $data );

    flush();

    $data ['headers-out'] = headers_list ();

    file_put_contents ( $file, json_encode ( $data ) ) ;

?>