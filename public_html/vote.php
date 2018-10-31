<?php
if(isset($_GET['bazzar'])):

# validation
$bazzar = $_GET['bazzar'];
if(!preg_match('/^[-0-9a-z]+$/', $bazzar)) {
	die('Malformed bazzar name');
}

include '../config.php';

try {
	$pdo = new PDO("mysql:dbname=$DB_NAME;host=$DB_HOST;charset=utf8", $DB_USER, $DB_PASS);

	$sql = 'SELECT * FROM bazzar WHERE name=?';
	$stmt = $pdo->prepare($sql);
	$res = $stmt->execute(array($_GET['bazzar']));
	if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$seller = $row['seller'];
		$booth = $row['booth'];
	} else {
		die("Bazzar $bazzar not found");
	}
} catch(PDOException $e) {
	file_put_contents("php://stderr", $e->getMessage());
	die('DB connection failed');
}
if(!$pdo) die('error');

$title = "${seller} ${booth}に投票する | バザコン2018";
?>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title><?= $title; ?></title>

	<!-- bootstrap -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<h1 class="row text-center"><?= "${seller} ${booth}"; ?></h1>
		<div class="jumbotron" style="background-image: url(<?= "images/${bazzar}.jpg"; ?>)"></div>
		<h2 class="row text-center"><?= "${seller} ${booth}に投票する";?></h2>
		<form action="vote_confirm.php" method="POST">
			<div class="form-group">
				<div class="row text-center">
					<label for="booth">店名</label>
				</div>
				<input type="hidden" name="bazzar" value="<?= $bazzar; ?>">
				<div class="row">
					<div class="col-xs-4 col-xs-offset-4">
						<input type="text" value="<?= "${seller} ${booth}"; ?>" disabled class="form-control" id="booth">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-4 col-xs-offset-4">
					<button type="submit" class="btn btn-primary form-control">投票する</button>
				</div>
			</div>
			<div class="row text-center">
				<small id="submitHelp" class="form-text text-muted">2度目以降の投票は投票先の変更になります</small>
			</div>
		</form>
	</div>
	<script src="//code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
else:
include '../config.php';

$DB = "mysql:dbname=$DB_NAME;host=$DB_HOST;charset=utf8";

$ip = $_SERVER['REMOTE_ADDR'];
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
	if(!empty($vote['quality'])) {
		$row = getRow('bazzar', 'name', $vote['quality']);
		$quality_text = "<strong>${row['seller']} ${row['booth']}</strong>";
	}
	if(!empty($vote['ambience'])) {
		$row = getRow('bazzar', 'name', $vote['ambience']);
		$ambience_text = "<strong>${row['seller']} ${row['booth']}</strong>";
	}
	if(!empty($vote['pr'])) {
		$row = getRow('bazzar', 'name', $vote['pr']);
		$pr_text = "<strong>${row['seller']} ${row['booth']}</strong>";
	}
}
if(empty($quality_text))
	$quality_text = '設定されていません';
if(empty($ambience_text))
	$ambience_text = '設定されていません';
if(empty($pr_text))
	$pr_text = '設定されていません';

?>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>バザコン2018</title>

	<!-- bootstrap -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<h2 class="col-xs-12 text-center">現在投票されるように設定されている各部門</h2>
		<div class="row col-xs-12">
			<h3 class="text-center">Quality部門（味、完成度など）</h3>
			<div class="row col-xs-12 text-center h4"><?= $quality_text;?></div>
		</div>
		<div class="row col-xs-12">
			<h3 class="text-center">Ambience部門（雰囲気）</h3>
			<div class="row col-xs-12 text-center h4"><?= $ambience_text;?></div>
		</div>
		<div class="row col-xs-12">
			<h3 class="text-center">PR部門（広告）</h3>
			<div class="row col-xs-12 text-center h4"><?= $pr_text;?></div>
		</div>
	</div>
	<script src="//code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
endif;