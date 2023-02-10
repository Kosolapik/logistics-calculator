<?php
namespace Models\DataBases {
    class Localities extends \Models\DataBases\DataBase {
        protected const TABLE_NAME = 'localities_id_tc'; // Имя таблицы
        protected const DEFAULT_ORDER = 'name'; // Сортировка по умолчанию
        protected const RELATIONS = []; // Масив связанх таблиц
    }
}
?>