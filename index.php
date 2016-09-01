<?php

//Задаем кодировку UTF-8 и вывод всех ошибок
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);

//Создаем подключение к БД
$mysqli = new mysqli('localhost', 'root', '', 'cars');

//В случае неудачного подключения к БД выводим ошибку и завершаем работу скрипта
if ($mysqli->connect_error) {
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

//Получаем массив названий категорий из БД
function getCategory($mysqli){
    $sql = 'SELECT * FROM `categories`';
    $res = $mysqli->query($sql);

//Создаем масив где ключ массива является ID меню
$category = array();
while($row = $res -> fetch_assoc()){
    $category[$row['id']] = $row;
}
    return $category;
}

//Функция построения дерева из массива
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

//Записываем подготовленный массив с данными
$cat  = getCategory($mysqli);

//Создаем древовидное меню
$tree = getTree($cat);

//Шаблон для вывода меню в виде дерева
function treeMenu($category){
    $menu = '<li>
		<a href="#" title="'. $category['name'] .'">'.
        $category['name'].'</a>';

    if(isset($category['childs'])){
        $menu .= '<ul>'. showCategory($category['childs']) .'</ul>';
    }
    $menu .= '</li>';

    return $menu;
}

//Рекурсивно считываем шаблон
function showCategory($data){
    $string = '';
    foreach($data as $item){
        $string .= treeMenu($item);
    }
    return $string;
}

//Получаем HTML разметку
$category_menu = showCategory($tree);

//Выводим в браузер
echo '<ul>'. $category_menu .'</ul>';

