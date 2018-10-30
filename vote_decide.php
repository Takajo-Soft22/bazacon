<?php
$DB_HOST = 'localhost';
$DB_NAME = 'bazacon';
$DB_USER = 'nobody';
$DB_PASS = /* hidden */;
$DB = "mysql:dbname=$DB_NAME;host=$DB_HOST;charset=utf8";

$ip = $_SERVER['REMOTE_ADDR'];
$key = /* hidden */;
$_id = hash_hmac('sha256', $ip, $key);


# validation
if(!isset($_POST['bazzar'])) {
	die('No bazzar specified');
}
$bazzar = $_POST['bazzar'];
if(!preg_match('/^[-0-9a-z]+$/', $bazzar)) {
	die('Malformed bazzar name');
}
if(!isset($_POST['token1']) || !isset($_POST['token2']) || hash_hmac('sha256', $_POST['token1'], $key) != $_POST['token2']) {
	die('Wrong tokens');
}

function id_reset() {
	global $_id;
	setcookie('id', $_id, 1541343600);
}

$pdo = null;
try {
	$pdo = new PDO($DB, $DB_USER, $DB_PASS);
} catch(PDOException $e) {
	debug($e->getMessage());
	die('DB connection failed');
}
if(!$pdo) die('error');

function prepareSQL($sql) {
	global $pdo;
	try {
		return $pdo->prepare($sql);
	} catch(PDOException $e) {
		file_put_contents("php://stderr", $e->getMessage());
		die('SQL preparation failed');
	}
}

function getRow($table, $key_name, $key) {
	$sql = "SELECT * FROM $table WHERE $key_name=?";
	$stmt = prepareSQL($sql);
	$res = $stmt->execute(array($key));
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

$vote_quality = isset($_POST['quality']);
$vote_ambience = isset($_POST['ambience']);
$vote_pr = isset($_POST['pr']);

$keys = array();
if($vote_quality) array_push($keys, 'quality');
if($vote_ambience) array_push($keys, 'ambience');
if($vote_pr) array_push($keys, 'pr');
$values = array_fill(0, count($keys), $bazzar);

if(count($keys) == 0) {
	die('Nothing to be done');
}

$bz = getRow('bazzar', 'name', $bazzar) or die("Bazzar $bazzar not found");
$booth = $bz['booth'];
$seller = $bz['seller'];

function update_row($id) {
	global $bazzar, $_id, $keys, $values;
	array_push($values, $_id, $id);
	array_push($keys, 'id=?');
	$query = implode('=?, ', $keys);
	$stmt = prepareSQL("UPDATE vote SET $query WHERE id=?");
	$stmt->execute($values);
}

function insert_row() {
	global $bazzar, $_id, $keys, $values;
	array_push($keys, 'id');
	array_push($values, $_id);
	$query1 = implode(', ', $keys);
	$query2 = implode(', ', array_fill(0, count($keys), '?'));
	$stmt = prepareSQL("INSERT INTO vote($query1) VALUES($query2)");
	$stmt->execute($values);
}

$vote = null;
if(isset($_COOKIE['id'])) {
	$id = $_COOKIE['id'];
	if(!ctype_xdigit($id)) {
		id_reset();
	} elseif($id !== $_id) {
		if($vote = getRow('vote', 'id', $id)) {
			# 登録済み(ID変更あり)→update
			update_row($id);
		} else {
			# 未登録→insert
			insert_row();
		}
		id_reset();
	} else {
		if($vote = getRow('vote', 'id', $id)) {
			# 登録済み→update
			update_row($id);
		} else {
			# 未登録→insert
			insert_row();
		}
	}
} else {
	# 未登録→insert
	insert_row();
	id_reset();
}

$voted = array();
if($vote_quality) $voted []= 'quality';
if($vote_ambience) $voted []= 'ambience';
if($vote_pr) $voted []= 'PR';

echo $booth, '（', $seller, '）を', implode(', ', $voted), 'に投票しました。';
?>