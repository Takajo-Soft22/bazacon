# bazacon
バザーコンテスト投票のデジタル化

## インストール
1. PHPとMySQLが入ったサーバを立てる
2. MySQLで以下のような操作をする
```bash
$ mysql -u root
```
```SQL
-- データベースbazaconを作成
CREATE DATABASE bazacon;
-- ユーザnobodyをパスワード「パスワード」で作成（※パスワードは適当に変えてください）
CREATE USER nobody@localhost IDENTIFIED BY 'パスワード';
-- nobodyにbazaconの全権限を渡す
GRANT ALL ON bazacon.* TO nobody@localhost IDENTIFIED BY 'パスワード';
```
3. init.sqlを読み込む
```bash
$ mysql bazacon -u root < init.sql
```
4. vote.php, vote_confirm.php, vote_decide.phpの中の$DB_PASSを2.で設定したパスワードにする
5. 同ファイル群の中の$keyを適当なキーに設定する

## 背景画像について
public_html/images
- images/rikujo-udon.jpg
- images/basukedanshi-nagetto.jpg
- images/basukejoshi-tapioka.jpg
- images/volleyball-yakisoba.jpg
- images/softtennis-frankfurt.jpg
- images/takkyu-agetako.jpg
- images/kendo-friedpotato.jpg
- images/suiei-cheeseandchocostick.jpg
- images/baseball-yakitori.jpg
- images/bado-crape.jpg
- images/handball-waffuru.jpg
- images/hardtennis-isobe.jpg
- images/acheri-takikomigohan.jpg
- images/bijutu-fakefruitsandhaborium.jpg
- images/suisogaku-korokke.jpg
- images/mekatoro-amusement.jpg
- images/engei-soupanddriflower.jpg
- images/bika-yakiimo.jpg
- images/me2-popcorn.jpg
- images/ie2-frenchtoast.jpg
- images/ca2-donut.jpg
- images/ie3-okonomiyaki.jpg
- images/ca3-watagashi.jpg
- images/me4-churosu.jpg
- images/ie4-agepan.jpg
- images/ca4-pancake.jpg
- images/senkoka-curry.jpg
