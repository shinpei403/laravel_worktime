勤怠管理システム
====
このアプリケーションは、従業員の勤怠管理を行うためのシステムです。
管理者は全従業員の勤怠情報を閲覧、編集、CSV出力が出来ます。
また、従業員の情報の管理機能も使用することが出来ます。
従業員は自身の勤怠情報を登録、閲覧ができます。

## デモ



## インストール方法
<ol>
  <li>このリポジトリをクローンする。</li>
  <li>env.example を .env にコピーし、適宜環境変数を設定する。</li>
  <li>composer installを実行する</li>
  <li>php artisan key:generate を実行する。</li>
  <li>php artisan migrate</li>
  <li>phpmyadminなどでユーザーを追加する</li>
  <li>php artisan serve を実行する。</li>
</ol>

## 使い方

  1.http://localhost/laravel-worktime/public/login/show にアクセスします<br>
  
  2.登録したユーザーの従業員番号とパスワードを入力して、ログインします。<br>
  
  3.ログイン後、各機能を使用することが出来ます。<br>
  
## Licence

This software is released under the MIT License, see LICENSE.

## Author
shinpei403



