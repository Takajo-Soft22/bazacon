<?php
function debug($info) {
	file_put_contents('php://stderr', $info . "\n");
}

# validation
if(!isset($_POST['bazzar'])) {
	die('No bazzar specified');
}
$bazzar = $_POST['bazzar'];
if(!preg_match('/^[-0-9a-z]+$/', $bazzar)) {
	die('Malformed bazzar name');
}

$DB_HOST = 'localhost';
$DB_NAME = 'bazacon';
$DB_USER = 'nobody';
$DB_PASS = /* hidden */;
$DB = "mysql:dbname=$DB_NAME;host=$DB_HOST;charset=utf8";

$ip = $_SERVER['REMOTE_ADDR'];
$key = /* hidden */;
$_id = hash_hmac('sha256', $ip, $key);

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

$bz = getRow('bazzar', 'name', $bazzar) or die("Bazzar $bazzar not found");
$booth = $bz['booth'];
$seller = $bz['seller'];

$vote = null;
if(isset($_COOKIE['id'])) {
	$id = $_COOKIE['id'];
	if(!ctype_xdigit($id)) {
		id_reset();
	} elseif($id !== $_id) {
		if($vote = getRow('vote', 'id', $id)) {
			# update id
			$stmt = prepareSQL('UPDATE vote SET id=? WHERE id=?');
			$stmt->execute(array($_id, $id));
		}
		id_reset();
	} else {
		$vote = getRow('vote', 'id', $_id);
	}
} else {
	$vote = getRow('vote', 'id', $_id);
	id_reset();
}

$quality_text = $ambience_text = $pr_text = null;
if(!empty($vote)) {
	if(!empty($_vote['quality'])) {
		$row = getRow('bazzar', 'name', $vote['quality']);
		$quality_text = "現在、この部門は${row['booth']}（${row['seller']}）に投票しています。";
	}
	if(!empty($_vote['ambience'])) {
		$row = getRow('bazzar', 'name', $vote['ambience']);
		$ambience_text = "現在、この部門は${row['booth']}（${row['seller']}）に投票しています。";
	}
	if(!empty($_vote['pr'])) {
		$row = getRow('bazzar', 'name', $vote['pr']);
		$pr_text = "現在、この部門は${row['booth']}（${row['seller']}）に投票しています。";
	}
}
if(empty($quality_text))
	$quality_text = '現在、この部門には投票していません。';
if(empty($ambience_text))
	$ambience_text = '現在、この部門には投票していません。';
if(empty($pr_text))
	$pr_text = '現在、この部門には投票していません。';

$token1 = hash_hmac('sha256', time(), $key);
$token2 = hash_hmac('sha256', $token1, $key);
?>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title><?= "確認 - ${seller}の${booth}に投票する | バザコン2018"; ?></title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<h2><?= "${booth}（${seller}）に投票する";?></h2>
			<p>このバザーをどの部門に投票しますか？</p>
			<form class="col-md-4" action="vote_decide.php" method="POST">
				<input type="hidden" name="token1" value="<?= $token1; ?>">
				<input type="hidden" name="token2" value="<?= $token2; ?>">
				<input type="hidden" name="booth" value="<?= $bazzar; ?>">
				<div class="form-group">
					<input type="checkbox" name="quality" value="yes" id="quality">
					<label for="quality" class="panel text-center">
						<span class="panel-title">quality（味など）</span>
						<div class="panel-body">
							<?= $quality_text; ?>

						</div>
					</label>
				</div>
				<div class="form-group">
					<input type="checkbox" name="ambience" value="yes" id="ambience">
					<label for="ambience" class="panel text-center">
						<span class="panel-title">ambience（雰囲気）</span>
						<div class="panel-body">
							<?= $ambience_text; ?>

						</div>
					</label>
				</div>
				<div class="form-group">
					<input type="checkbox" name="pr" value="yes" id="pr">
					<label for="pr" class="panel text-center">
						<span class="panel-title">PR（広告）</span>
						<div class="panel-body">
							<?= $pr_text; ?>

						</div>
					</label>
				</div>
				<button type="submit" class="btn btn-primary">投票する</button>
				<small id="submitHelp" class="form-text text-muted">2度目以降の投票は投票先の変更になります</small>
			</form>
		</div>
	</div>
	<!-- jQuery -->
	<script src="//code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<!-- Bootstrap -->
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>