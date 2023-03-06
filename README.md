勤怠管理システム
====
このアプリケーションは、従業員の勤怠管理を行うためのシステムです。
管理者は全従業員の勤怠情報を閲覧、編集、CSV出力が出来ます。
また、従業員の情報の管理機能も使用することが出来ます。
従業員は自身の勤怠情報を登録、閲覧ができます。

## 機能一覧
　<ul>
      <li>ログイン認証機能</li>
      <li>勤怠登録機能</li>
      <li>勤怠情報一覧の表示・検索</li>
      <li>勤怠情報の編集(管理者)</li>
      <li>CSVで勤怠情報の出力(明細、集計)(管理者)</li>
      <li>従業員の一覧の表示(管理者)</li>
      <li>新規従業員の登録(管理者)</li>
      <li>従業員の詳細の表示(管理者)</li>
      <li>従業員情報の編集(管理者)</li>
      <li>従業員の削除(管理者)</li>
  </ul>

## デモ

![cab4cde9aedd3f4ea80534787c52fb86](https://user-images.githubusercontent.com/104670465/221192306-1f9fce9e-3934-4164-9e72-ee1cf16a3527.gif)


![682756181828cbbc7a6e106d48202ff5](https://user-images.githubusercontent.com/104670465/221198749-0c6c7e49-6d24-46dd-92cd-30cbbf8fc76b.gif)

## インストール方法
<ol>
  <li>このリポジトリをクローンする。</li>
  <li>env.example を .env にコピーし、適宜環境変数を設定する。</li>
  <li>composer installを実行する</li>
  <li>php artisan key:generate を実行する。</li>
  <li>php artisan migrate</li>
  <li>php artisan db:seed --class=UserSeeder 実行する。</li>
  <li>php artisan serve を実行する。</li>
</ol>

## 使い方

  1.http://localhost/laravel-worktime/public/login/show にアクセスします<br>
  
  2.従業員番号とパスワードを入力してログインします。(初期値 従業員番号：d00001 パスワード：password)<br>
  
  3.ログイン後、各機能を使用することが出来ます。<br>
  
## 注意事項

　本アプリケーションはLaravel 8.xで開発されています。Laravelのバージョンを確認し、必要に応じてアップグレードしてからご利用ください。
  
## Licence

This software is released under the MIT License, see LICENSE.

## Author
shinpei403



