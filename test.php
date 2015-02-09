<?php

include('db.php');

$db = new db;
$db->connect('db/test');

if (!$db) {
    echo "<h2>Ne morem odpreti baze!</h2>";
    exit;
}

//$db->query('create table up (ime varchar(255), priimek varchar(255))');
 
if (isset($_POST['dodaj'])) {
    if ($_POST['ime'] != '' && $_POST['priimek'] != '') {
        $db->query('insert into up (ime, priimek) values ("' . $_POST['ime'] . '", "' . $_POST['priimek'] . '")');
    } else {
        $error = '<div style="float: right; width: 225px; padding: 10px; margin-bottom: 10px; background: #FFF7F7; border: solid 1px #FF9090; font-weight: bold; text-align: center; -moz-border-radius: 6px;">Manjkajo podatki!</div>';
    }
}

header('content-type: text/html; charset=utf-8');

?>

<style>
body { font: normal 13px arial; }
#container { width: 500px; margin: 10px auto; padding: 10px; background: #eee; border: solid 1px #ddd; -moz-border-radius: 6px; }
#form { background: #fafafa; padding: 10px; border: solid 1px #dadada; font: normal 13px arial; -moz-border-radius: 6px; }
#form input[type=text] { font: normal 12px arial; }
td { padding: 1px 0; margin: 0; }
</style>

<div id="container">
    <h1 style="margin: 10px 0;">SQLite test</h1>
    
    <?php if ($error) echo $error; ?>
    
    <form method="post">
        <table id="form">
            <tr><td>Ime:</td><td><input type="text" name="ime" /></td></tr>
            <tr><td width="80">Priimek:</td><td><input type="text" name="priimek" /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Dodaj" name="dodaj" /></td></tr>
        </table>
    </form>
    
    <h2>Dodani ljudje (<?php echo $db->num_rows($db->query('select null from up')); ?>)</h2>
    
    <div style="margin-bottom: 10px;">
        Uredi po priimku:
        <?php if (empty($_SERVER['QUERY_STRING'])) { echo '<strong>narašèujoèe</strong>'; } else { echo '<a href="./">narašèujoèe</a>'; } ?> |
        <?php if ($_GET['order'] == 'desc') { echo '<strong>padajoèe</strong>'; } else { echo '<a href="./?order=desc">padajoèe</a>'; } ?>
    </div>
    
    <?php
    $order = 'asc';
    if ($_GET['order'] == 'desc') $order = 'desc';
    $q = $db->query('select * from up order by priimek ' . $order);
    while ($f = $db->fetch_assoc($q)) {
        echo '<div ' . ($f['priimek'] == $_POST['priimek'] && $f['ime'] == $_POST['ime'] ? 'style="color: green;"' : '') . '><strong>' . mb_strtoupper($f['priimek'], 'utf-8') . '</strong> ' . $f['ime'] . '</div>';
    }
    ?>
</div>