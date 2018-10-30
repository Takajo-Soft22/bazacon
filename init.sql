CREATE TABLE bazzar (
	name VARCHAR(100) PRIMARY KEY,
	seller VARCHAR(100),
	booth VARCHAR(100)
) charset=utf8;
INSERT INTO bazzar (name, seller, booth) VALUES
	('rikujo-udon', '陸上競技部', 'うどん'),
	('basukedanshi-nagetto', '男子バスケットボール部', 'ナゲット'),
	('basukejoshi-tapioka', '女子バスケットボール部', 'タピオカ'),
	('volleyball-yakisoba', 'バレーボール部', '焼きそば'),
	('softtennis-frankfurt', 'ソフトテニス部', 'フランクフルト'),
	('takkyu-agetako', '卓球部', '揚げたこ'),
	('kendo-friedpotato', '剣道部', 'フライドポテト'),
	('suiei-cheeseandchocostick', '水泳部', 'チーズ＆チョコスティック'),
	('baseball-yakitori', '野球部', '焼き鳥'),
	('bado-crape', 'バドミントン部', 'クレープ'),
	('handball-waffuru', 'ハンドボール', 'ワッフル'),
	('hardtennis-isobe', 'テニス部', 'ちくわの磯部揚げ'),
	('acheri-takikomigohan', 'アーチェリー部', '炊き込みご飯'),
	('bijutu-fakefruitsandhaborium', '美術部', 'フェイクフルーツ＆ハーバリウム'),
	('suisogaku-korokke', '吹奏楽部', 'コロッケ'),
	('mekatoro-amusement', 'メカトロシステム部', 'アミューズメント'),
	('engei-soupanddriflower', '園芸同好会', 'スープ＆ドライフラワー'),
	('bika-yakiimo', '美化係', '焼き芋'),
	('me2-popcorn', 'ME2', 'ポップコーン'),
	('ie2-frenchtoast', 'IE2', 'フレンチトースト'),
	('ca2-donut', 'CA2', 'ドーナツ'),
	('ie3-okonomiyaki', 'IE3', 'お好み焼き'),
	('ca3-watagashi', 'CA3', 'わたがし'),
	('me4-churosu', 'ME4', 'チュロス'),
	('ie4-agepan', 'IE4', '揚げパン'),
	('ca4-pancake', 'CA4', 'パンケーキ'),
	('senkoka-curry', '専攻科', 'カレー');

CREATE TABLE vote (
	id CHAR(64) PRIMARY KEY,
	quality VARCHAR(100),
	ambience VARCHAR(100),
	pr VARCHAR(100)
) charset=utf8;