<?php

require_once 'db.php';

$db = new db;
$db->connect('localhost', 'user', 'password', 'urls');

$q = $db->fetch_assoc($db->query('select url from urls where hash = "' . $_GET['hash'] . '"'));
$db->query('update urls set clicks = clicks+1 where hash = "' . $_GET['hash'] . '" limit 1');

header('location: ' . $q['url']);

?>