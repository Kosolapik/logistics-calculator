<?php
namespace Models\DataBases {
    class KitCities extends \Models\DataBases\DataBase {
        protected const TABLE_NAME = 'kit_cities'; // Имя таблицы
        protected const DEFAULT_ORDER = 'name'; // Сортировка по умолчанию
        protected const RELATIONS = []; // Масив связанх таблиц
    }
}
?>