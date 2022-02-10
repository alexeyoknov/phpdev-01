<?php

// ООП - определение, прочитать WIKI - сказать что непонятно в вопросе ДЗ
// для описания алгоритма используются взаимодействующие объекты (экземпляры класса)
// методы для взаимодействия публичные - доступны, методы внутренней реализации - скрытые
// логика алгоритма выглядит проще, если скрывать незначительные детали и выстраивать взаимодействие между объектами на разных уровнях

// https://ru.wikipedia.org/wiki/%D0%9E%D0%B1%D1%8A%D0%B5%D0%BA%D1%82%D0%BD%D0%BE-%D0%BE%D1%80%D0%B8%D0%B5%D0%BD%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%BD%D0%BE%D0%B5_%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5
// https://habr.com/ru/post/87119/
// https://habr.com/ru/post/87205/
// http://www.codenet.ru/progr/cpp/ipn.php
// https://medium.com/@volkov97/%D0%BE%D1%81%D0%BD%D0%BE%D0%B2%D0%BD%D1%8B%D0%B5-%D0%BA%D0%BE%D0%BD%D1%86%D0%B5%D0%BF%D1%86%D0%B8%D0%B8-%D0%BE%D0%BE%D0%BF-9c61d16e693b

// !абстракция
// инкапсуляция
// наследование
// полиморфизм

// OOP PHP https://refactoring.guru/ru/design-patterns/php

// Практики: (DRY) KISS SOLID
// https://habr.com/ru/company/itelma/blog/546372/

//------------------------------------------------------------------------------------------------------------------



// TODO намеренно плохой код! задача - отрефакторить, используя известные вам техники организации кода

// Войска: пехота, конница, лучники.
// Свойства: жизни, броня, урон
$pehota = [
    'health' => 100,
    'armour' => 10,
    'damage' => 10,
];

$luchniki = [
    'health' => 100,
    'armour' => 5,
    'damage' => 20,
];

$konnica = [
    'health' => 300,
    'armour' => 30,
    'damage' => 30,
];

// Создаём две армии (кол-во юнитов)
$army1 = [
    'name' => 'Александр Ярославич',
    'units' => [
        'pehota' => 200,
        'luchniki' => 30,
        'konnica' => 15,
    ]
];
$army2 = [
    'name' => 'Ульф Фасе',
    'units' => [
        'pehota' => 90,
        'luchniki' => 65,
        'konnica' => 25,
    ]
];

// Запускаем битву.
$damage1 = 0;
$health1 = 0;
foreach ($army1['units'] as $unit => $count) {
    $damage1 += ${$unit}['damage'] * $count;
    $health1 += ${$unit}['health'] * $count + ${$unit}['armour'] * $count;
}

$damage2 = 0;
$health2 = 0;
foreach ($army2['units'] as $unit => $count) {
    $damage2 += ${$unit}['damage'] * $count;
    $health2 += ${$unit}['health'] * $count + ${$unit}['armour'] * $count;
}

//function calc_army_damage_health ($army) use ($pehota, $luchniki, $konnica)
//{
//    $damage = 0;
//    $health = 0;
//
//    foreach ($army['units'] as $unit => $count) {
//        $damage += ${$unit}['damage'] * $count;
//        $health += ${$unit}['health'] * $count + ${$unit}['armour'] * $count;
//    }
//
//    return ['damage' => $damage, 'health' => $health];
//};

?>

<table border="1">
    <tr>
        <th></th>
        <th><?=$army1['name']?></th>
        <th><?=$army2['name']?></th>
    </tr>
    <tr>
        <th>Army units:</th>
        <td>unit1 (count), unit2(count), ...</td>
        <td>unit1 (count), unit2(count), ...</td>
    </tr>
<?php
$duration = 0;
while ($health1 >= 0 && $health2 >= 0) {
    $health1 -= $damage2;
    $health2 -= $damage1;
    $duration++;
}
?>
    <tr>
        <th>Health after <?=$duration?> hits:</th>
        <td><?=$health1?></td>
        <td><?=$health2?></td>
    </tr>
    <tr>
        <th>Result</th>
        <td><?=$health1 > $health2 ? 'WINNER' : 'LOOSER'?></td>
        <td><?=$health2 > $health1 ? 'WINNER' : 'LOOSER'?></td>
    </tr>
</table>
<?php

// + ДЗ
// Вывод: результаты битвы. Кто участвовал, кто победил, погибшие, выжившие.
// Переписать на ООП, используя интерфейс Unit от которого будут создаваться юниты.
// Научить объединяться юниты в армию, см. Composite
// реализовать две механики расчета боя (суммарный подсчет, сражение каждой линии до выживания)
// добавить условия поля битвы ??? например, лед - снижает броню конницы до 0, дождь - снижает в два раза атаку лучников

// + 3 задачки на codewars

// Паттерны
// https://refactoring.guru/ru/design-patterns/php
// + изучить паттерны Composite Decorator Strategy (самостоятельно)

// MVC - фреймворк, CMS
// https://ru.wikipedia.org/wiki/Model-View-Controller

//

