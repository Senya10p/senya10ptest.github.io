<?php

//Админ-панель
require __DIR__ . '/autoload.php';

//Авторизация
$admin = new \App\Models\Authorization();

if ( null === $admin->getUsername() ) {

    header('Location: /admin/login.php');
    exit;
}

//Добавление фото
$gal = new \App\Models\Uploader('upl');

$gal->isUploaded();

if (  null !== $admin->getUsername() ) {

    $gal->upload(__DIR__ . '/gallery/images/', ['image/jpg', 'image/png', 'image/jpeg']); //в качестве аргументов передаём путь до файла и тип загружаемого файла
}

$list = scandir(__DIR__ . '/gallery/images');
$list = array_diff($list, ['.', '..']);

//Обо мне текст
$text = new \App\Models\AboutMe(__DIR__ . '/aboutme.txt');

if ( isset( $_POST['text'] ) ) {
    $text->append($_POST['text']);
    $text->save();
}

//Гостевая книга
$news = new \App\Models\GBMessages();

//var_dump($news->getAll());

if ( isset( $_POST['del'] ) ) {
    $news->delArticle( $_POST['del'] );
}

$news->getAll();
$data = $news->getAll();

//Отображение
$view = new \App\Models\View();

//Для гостевой книги
$view->assign( 'data', $data );

$view->assign('records', $text->getData() );//текстовка обо мне

$view->assign( 'list', $list );//фотогаллерея

$view->display(__DIR__ . '/templates/admin.php');
