# MiniDiary

ミニ日記

## Description

どなたでも、自由にミニ日記を作成することができます。  
写真でその日の出来事が明確に思い出されます。  

***SAMPLE:***

![minidiary](https://user-images.githubusercontent.com/36429862/37025033-f027b0fa-216d-11e8-8f9e-f958c491c018.png)

## Features

・複数ユーザーのダイアリーの表示（認証済みの時は、認証ユーザーのみ表示）  
・ダイアリーの個別表示（画像をクリックで表示)  
・ダイアリーの日付移動（矢印キーで移動。但し、登録されていない日付はスキップ）  
・ダイアリーの検索（ユーザー及び、日付の選択）  
・ダイアリーの選択（カレンダーの日付の背景が緑色の時）  

## Requirement

・CentOS 7.4  
・PHP 7.1  
・mysql 5.7  
・twig 2.4  
・slick 1.8  
・bootstrap 4.0  

## Usage

1.ダイアリーの処理  
　・ダイアリーの登録（メニューバーのユーザー名のプルダウンメニューより）  
　公開日を指定できる （認証済みの時）  
　・ダイアリーの編集（画像をクリックで表示 -> 編集）（認証済みの時）  
　・ダイアリーの削除（画像をクリックで表示 -> 編集 -> 削除）（認証済みの時）  
2.アカウント  
　・ログイン（メニューバーより）  
　・ログアウト（メニューバーのユーザー名のプルダウンメニューより）（認証済みの時）  
　・ユーザーの新規登録（メニューバーより）  
　・ユーザーの編集、退会(削除)  
　（メニューバーのユーザー名のプルダウンメニューのアカウントより）（認証済みの時）  
3.その他  
　・about（メニューバーより）  
　・お問い合わせ（メニューバーより）  

## Settings

（注）  
　プログラムの中で "unlink、rmdir "を使用しています。  
　テストは行いました。念のためにお伝えします。  

    ・unlink  
      /app/src/diary/edit.php 122行  
      /app/src/diary/delete.php 37行  
      /app/src/auth/account_delete.php 41行  
    ・rmdir  
      /app/src/auth/account_delete.php 47行  

　1.データベースの設定  
 
    ・データベース接続  
        dbname、user、passwordの設定をしてください。
        /src/functions/function.php 41行
       function connectDb() {
           try {
               $dsn = "mysql:host=localhost;dbname=dbname";
               $user = "user";
               $password = "password";

               $dbh = new PDO($dsn, $user, $password);
               return $dbh;
           } catch(PDOException $e) {
               echo "DB accsess error ! : " . $e->getMessage();
               exit();
           }
       }

　2.テーブルの作成  
 
    ・ユーザーテーブル（users）
        CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `sex` char(1) DEFAULT '1',
          `birthday` varchar(255) DEFAULT NULL,
          `zip` varchar(255) DEFAULT NULL,
          `address` varchar(255) DEFAULT NULL,
          `tel` varchar(255) DEFAULT NULL,
          `email` varchar(255) DEFAULT NULL,
          `password` varchar(255) DEFAULT NULL,
          `profile` text,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`));

    ・ダイアリーテーブル（diaries）
        CREATE TABLE `diaries` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) DEFAULT NULL,
          `title` varchar(255) DEFAULT NULL,
          `path` varchar(255) DEFAULT NULL,
          `image` varchar(255) DEFAULT NULL,
          `comment` varchar(255) DEFAULT NULL,
          `release_date` date DEFAULT NULL,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          CONSTRAINT `diaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`));

　3.メールアドレスの設定  

    "admin@example.jp"を変更してください。  
    /app/config/define.php 4行  
    $support_email = "admin@example.jp";   

　4.ドキュメントルート  
　・ドキュメントルートは、**'diary'** に設定してください。  

　5.ディレクトリのオーナの設定  
　・画像保存のディレクトリ img のオーナをwebサーバーの実行ユーザーに設定してください。 
    
    /diary/assets/img

　6.エントリーポイント  
 
    /index.php


## Author

@mikanlun

## License

[MIT](https://github.com/mikanlun/MiniDiary/blob/master/LICENSE)
