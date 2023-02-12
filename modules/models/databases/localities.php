<?php
namespace Models\DataBases {
    class Localities extends \Models\DataBases\DataBase {
        protected const TABLE_NAME = 'localities_id_tc'; // Имя таблицы
        protected const DEFAULT_ORDER = 'name'; // Сортировка по умолчанию
        protected const RELATIONS = []; // Масив связанх таблиц

        /*
            Метод получает список городов и ищет город в БД по ИД транспортной.
            Если запись в БД есть, добавляет её во вложенный массив.
            Возвращает сопоставленый список городов.
        */
        function compareCities($list, $company) {
            foreach ($list as $key => $value) {
                $select_params = [
                    'where' => $company . ' = ' . $value['code']
                ];
                $data_base = $this->select($select_params);
                // foreach($data_base as $record) {
                //     $arr = $record;
                // }
                $arr = $data_base->fetch(\PDO::FETCH_ASSOC);
                $list[$key]['database'] = $arr;
            }
            return $list;
        }
    }
}
?>