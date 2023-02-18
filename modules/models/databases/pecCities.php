<?php
namespace Models\DataBases {
    class PecCities extends \Models\DataBases\DataBase {
        protected const TABLE_NAME = 'pec_cities'; // Имя таблицы
        protected const DEFAULT_ORDER = 'name'; // Сортировка по умолчанию
        protected const RELATIONS = []; // Масив связанх таблиц
    }
}
?>