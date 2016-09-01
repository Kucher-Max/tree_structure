<?php

//Устанавливаем кодировку и вывод всех ошибок
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);

//Создаем подключение к БД
$mysqli = new mysqli('localhost', 'root', '', 'cars');

//Устанавливаем кодировку utf8
$mysqli->query("SET NAMES 'utf8'");

//В случае неудачного подключения к БД выводим ошибку и завершаем работу скрипта
if ($mysqli->connect_error) {
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

//Получаем массив названий категорий из БД
function getCategory($mysqli){
    $sql = 'SELECT * FROM `categories`';
    $res = $mysqli->query($sql);

//Создаем масив где ключ массива является ID меню
$cat = array();
while($row = $res -> fetch_assoc()){
    $cat[$row['id']] = $row;
}
    return $cat;
}

//Функция построения дерева из массива от Tommy Lacroix
function getTree($dataset) {
    $tree = array();

    foreach ($dataset as $id => &$node) {
        //Если нет вложений
        if (!$node['parent_id']){
            $tree[$id] = &$node;
        }else{
            //Если есть потомки то перебераем массив
            $dataset[$node['parent_id']]['childs'][$id] = &$node;
        }
    }
    return $tree;
}

//Получаем подготовленный массив с данными
$cat  = getCategory($mysqli);

//Создаем древовидное меню
$tree = getTree($cat);

//Шаблон для вывода меню в виде дерева
function tplMenu($category){
    $menu = '<li>
		<a href="#" title="'. $category['name'] .'">'.
        $category['name'].'</a>';

    if(isset($category['childs'])){
        $menu .= '<ul>'. showCategory($category['childs']) .'</ul>';
    }
    $menu .= '</li>';

    return $menu;
}

/**
 * Рекурсивно считываем наш шаблон
 **/
function showCategory($data){
    $string = '';
    foreach($data as $item){
        $string .= tplMenu($item);
    }
    return $string;
}

//Получаем HTML разметку
$cat_menu = showCategory($tree);

//Выводим на экран
echo '<ul>'. $cat_menu .'</ul>';

?>