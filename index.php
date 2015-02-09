<?php

require_once 'db.php';
require_once 'user.php';
require_once 'session.php';

$db = new db;
$db->connect('localhost', 'user', 'password', 'urls');

$session = new session;
$user = new user('zurka');
$d = $user->getdata();

$domain = 'http://waka.us/~nasal/url/bounce.php?hash=';

function url_exists ($url) {
	global $db;
	$hash = substr(sha1($url), 0, 5);
	if ($db->num_rows($db->query('select null from urls where hash = "' . $hash . '"'))) return true;
	else return false;
}

function create_url ($url) {
	global $db, $user, $d;
	if (!url_exists($url)) {
		$hash = substr(sha1($url), 0, 5);
		$db->query('insert into urls (owner, url, hash) values ("' . ($user->valid() ? $d['id'] : '0') . '", "' . $url . '", "' . $hash . '")');
		return array('url' => $url, 'hash' => $hash);
	} else {
		$hash = $db->fetch_assoc($db->query('select hash from urls where url = "' . $_POST['url'] . '" limit 1'));
		return array('url' => $_POST['url'], 'hash' => $hash['hash']);
	}
}

if (isset($_POST['register'])) {
	$db->query('insert into users (uname, passwd) values ("' . $_POST['uname'] . '", "' . md5($_POST['passwd']) . '")');
	$user->login($_POST['uname'], md5($_POST['passwd']));
	header('location: ./');
}

if (isset($_POST['login'])) {
	if (isset($_POST['uname']) && isset($_POST['passwd'])) {
  	if ($db->num_rows($db->query('select null from users where uname = "' . $_POST['uname'] . '" and passwd = "' . md5($_POST['passwd']) . '"'))) {
    	$user->login($_POST['uname'], md5($_POST['passwd']));
    	header('location: ./');
		}
	}
}

if (isset($_GET['logout'])) {
	$user->logout();
	header('location: ./');
}

header('content-type: text/html; charset=windows-1250');

?>

<html>
  <head>
    <title>Url èompa</title>
    <style>
    	body { font: normal 12px arial; margin: 0; padding: 0; background: #eee; }
    	#login { width: 600px; margin: 15px auto 0; padding: 5px 10px; text-align: right; font-size: 11px; }
    	#login input { font-size: 11px; }
    	#container { width: 600px; padding: 10px; margin: 5px auto; background: white; box-shadow: 0 0 10px #ccc; }
    	h1, h2 { margin: 0 0 10px; }
    	.ok { background: #C4F5C4; border: solid 2px #8DEB8D; padding: 10px; text-align: center; margin: 10px 0; }
    	.tinyurl { margin: 5px 0; font-size: 20px; font-weight: bold; }
    	.urlfield { width: 100%; padding: 5px; margin-bottom: 10px; }
    	#urlist { background: #C4D8F5; border: solid 2px #8DB4EB; padding: 10px; }
    	#noga { width: 600px; margin: 0 auto; padding: 5px 10px; color: #bbb; font-size: 11px; text-align: right; }
    </style>
  </head>
  <body>
    
    <div id="login">
    	<?php if (!$user->valid()): ?>
      	<form method="post" style="margin: 0;">
      		<input type="text" name="uname" placeholder="Uporabniško ime" /> <input type="password" name="passwd" placeholder="Geslo" /> <input type="submit" value="Prijava" name="login" /> <a href="./?register">Registracija</a>
      	</form>
      <?php else: ?>
      	Prijavljen kot: <strong><?= $d['uname']; ?></strong> (<a href="./?logout">odjavi se</a>)
    	<?php endif; ?>
    </div>
        
    <div id="container">
        
      <div id="content">
        <?php
        
        switch(key($_REQUEST)) {
        
        	case 'register':
        		echo '<h2>Registracija</h2>' . 
        		'<form method="post">' .
        		'User: <input type="text" name="uname" /><br/>' .
        		'Pass: <input type="password" name="passwd" /><br/>' .
        		'<input type="submit" value="Registriraj se" name="register" />' .
        		'</form>';
        	break;
        
          default:
          echo '<h2>Èompni</h2>';
          echo '<form method="post"><input type="text" name="url" class="urlfield" placeholder="Vpiši dolg url" /><br/><input type="submit" value="Sèompaj" name="chomp" /></form>';
          if (isset($_POST['chomp'])) {
						$tiny = create_url($_POST['url']);
						$len = ((strlen($tiny['hash']) - strlen($tiny['url']))*-1);
						echo '<div class="ok">' . 
						'Tvoj sèompan url je:' . 
						'<div class="tinyurl">' . $domain . $tiny['hash'] . '</div>' . 
						'in je <strong>' . $len . '</strong> znakov ' . ($len > 0 ? 'krajši' : 'daljši') . ' od originalnega.</div>';
					}
          echo '<h2>Nazadnje ' . ($user->valid() ? 'si me nahranil' : 'ste me nahranili') . ' sz</h2>';
          echo '<div id="urlist">';
					$q = $db->query('select * from urls where owner = "0" order by id desc limit 10');
					if ($user->valid()) $q = $db->query('select * from urls where owner = "' . $d['id'] . '" order by id desc');
					if ($db->num_rows($q)) {
						while ($f = $db->fetch_assoc($q)) {
							echo '<div><span style="width: 70px; display: inline-block;"><a href="' . $domain . $f['hash'] . '" target="_blank">' . $f['hash'] . '</a></span> &raquo; ' . $f['url'] . ' (' . $f['clicks'] . ' klikov)</div>';
						}
					} else {
						echo 'nièemer :( laèen sem!';
					}
					echo '</div>';
          
        }
        
        ?>
      </div>
      
    </div>
    
    <div id="noga">
    	URL Èompa v0.2
    </div>
    
  </body>
</html>