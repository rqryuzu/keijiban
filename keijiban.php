<?php

// DB接続設定
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); //エラーの際表示

//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date DATETIME,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
	
    //新規投稿
        if(!empty($_POST["submit"])){
            
            //データベースに接続
            $mysqli = new mysqli('ホスト名', 'ユーザー名', 'パスワード', 'データベース名');
            
			//定義	
            $name = $_POST["name"];    
            $comment = $_POST["comment"]; 
            $date = date("Y-m-d H:i:s");
            $pass=$_POST["pass"];
			
			//データを登録
			$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, pass)VALUES (:name, :comment, :date, :pass)");
	        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

	        $sql -> execute();
			
		}

    //削除機能  
        if(!empty($_POST["delete"])){
            
            if(!empty($_POST["deleteNo"]) && ($_POST["delpass"])){

            //データベースに接続
            $mysqli = new mysqli('ホスト名', 'ユーザー名', 'パスワード', 'データベース名');

            //テーブルのデータを取得
            $sql = 'SELECT * FROM tbtest';
	        $stmt = $pdo->query($sql);
	        
	        $delete_rets = $stmt->fetchAll();
	            foreach ($delete_rets as $line){
	                
	                if($line['pass']==$_POST["delpass"]){

                        //データを削除
                        $deleteid = $_POST["deleteNo"];
	                    $sql = 'delete from tbtest WHERE id=:id';
	                    $stmt = $pdo->prepare($sql);
	                    $stmt->bindParam(':id', $deleteid, PDO::PARAM_INT);
	                    $stmt->execute();
	                }
	            }
            }
        }        


    //編集機能
        if(!empty($_POST["edit"])){  //編集番号が送信されたとき
        
            //データベースに接続
            $mysqli = new mysqli('ホスト名', 'ユーザー名', 'パスワード', 'データベース名');
        
            //その番号の名前とテキストを表示する
            $sql = 'SELECT * FROM tbtest';
	        $stmt = $pdo->query($sql);
	        
	        $edit_rets = $stmt->fetchAll();
	            foreach ($edit_rets as $line){
	                
	                if($line['id']==$_POST["editnum"] && $line['pass']==$_POST["editpass"]){

	                $editsarerunum = $line['id'];	                
	                $editsareruname = $line['name'];
	                $editsarerucomment = $line['comment'];
	                $editsarerupass = $line['pass'];
	                
	                }
	            }
        }
        
        //編集ボタンを押す    
            
        elseif(!empty($_POST["edit_ex"])){

            if(!empty($_POST["edit_post"])){
    
        	    $editid = $_POST["edit_post"]; //変更する投稿番号
	            $editname = $_POST["editname"];
	            $editcomment = $_POST["editcomment"];
	            $editdate = date("Y-m-d H:i:s");
	            $editnewpass = $_POST["editnewpass"];
	            $sql = 'UPDATE tbtest SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':name', $editname, PDO::PARAM_STR);
	            $stmt->bindParam(':comment', $editcomment, PDO::PARAM_STR);
	            $stmt->bindParam(':date', $editdate, PDO::PARAM_STR);
	            $stmt->bindParam(':pass', $editnewpass, PDO::PARAM_STR);
	            $stmt->bindParam(':id', $editid, PDO::PARAM_INT);
	            $stmt->execute();
            }
        }

?>

<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title> Mission_5-1 </title>
</head>
<body>
    <form action = ""method = "post">
        
        <h1>ひとこと掲示板</h1>
        <div>
            <h2>投稿フォーム</h2>
            <label for = "name">名前</label>
            <input type = "text" name = "name">
            <br>
            <label for = "comment">投稿</label>
            <input type = "text" name = "comment">
            <br>
            <label for="pass">パスワード</label>
            <input type="text" name="pass">
            <br>
            <input type="submit" name="submit">
        </div>
        <div>
            <h2>削除フォーム</h2>
            削除対象番号
            <input type="text" name="deleteNo" placeholder="投稿番号">
            <br>
            削除パスワード
            <input type="text" name="delpass">
            <br>
            <input type="submit" name="delete" value="削除">
        </div>
        <div>
            <h2>編集フォーム</h2>
            編集対象番号
            <input type="text" name="editnum" placeholder="投稿番号">
            <br>
            編集パスワード
            <input type="text" name="editpass">
            <br>            
            <input type="submit" name="edit" value="送信">
            <br>
            <br>
            <input type="hidden" name="edit_post" value="<?php if(!empty($editsarerunum)){echo $editsarerunum;} ?>">
            編集名前
            <input type="text" name="editname" value="<?php if(!empty($editsareruname)){echo $editsareruname;} ?>">
            <br>
            編集コメント
            <input type="text" name="editcomment" value="<?php if(!empty($editsarerucomment)){echo $editsarerucomment;} ?>">
            <br>
            新しいパスワード
            <input type="text" name="editnewpass">
            <br>      
            <input type="submit" name="edit_ex" value="編集">
        </div>
    <hr>
        <h2>投稿表示</h2>
    </form>

<?php
            //表示
	        $sql = 'SELECT * FROM tbtest';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	            foreach ($results as $row){
		            //$rowの中にはテーブルのカラム名が入る
		            echo $row['id'].' ';
		            echo $row['name'].' ';
		            echo $row['comment'].' ';
		            echo $row['date'].'<br>';
	                echo "<hr>";
                }   

?>

</body>
</html>