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

$title = "${seller}の${booth}に投票する | バザコン2018";
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
		<div class="row">
			<h2><?= "${booth}（${seller}）に投票する";?></h1>
			<form class="col-xs-md-4" action="vote_confirm.php" method="POST">
				<div class="form-group">
					<label for="booth">店名</label>
					<input type="hidden" name="bazzar" value="<?= $bazzar; ?>">
					<input type="text" value="<?= "${booth}（${seller}）"; ?>" disabled class="form-control" id="booth">
				</div>
				<button type="submit" class="btn btn-primary">投票する</button>
				<small id="submitHelp" class="form-text text-muted">2度目以降の投票は投票先の変更になります</small>
			</form>
		</div>
	</div>
	<script src="//code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
else:
?>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>バザコン2018</title>

	<!-- bootstrap -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body>
	<div class="container block-center">
		<h2 class="col-xs-8">現在投票されるように設定されている各部門</h2>
		<div class="row">
			<h3>
		</div>
	</div>
	<script src="//code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
endif;