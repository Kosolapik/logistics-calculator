<?php
namespace DataBases;
class DataBase implements \Iterator {
    protected const TABLE_NAME = ''; // Имя таблицы
    protected const DEFAULT_ORDER = ''; // Сортировка по умолчанию
    protected const RELATIONS = []; // Масив связанх таблиц

    private $query = NULL; // Переменная для объекта запроса
    private $record = FALSE; // Переменная для хранения массива со значениями очередной извлечённой записи.

    /*
        Фун-ия создания объекта соединения с БД
    */
    static function connect_to_db(){
        $conn_str = 'mysql:host=' . \Settings\DB_HOST . ';dbname=' . \Settings\DB_NAME . ';charset=utf8';
        return new \PDO($conn_str, \Settings\DB_USERNAME, \Settings\DB_PASSWORD);
    }

    static private $connect = NULL; // Переменая для объекта соединения с БД
    static private $connect_count = 0; // Счётчик установленых соединений с БД
    /*
        Фун-ия конструктор 
    */
    function __construct() {
        if (!self::$connect) {
            self::$connect = \DataBases\DataBase::connect_to_db();
        }
        self::$connect_count++;
    }

    function __destruct() {
        self::$connect_count--;
        if (self::$connect_count == 0) {
            self::$connect = NULL;
        }
    }
    /*
        Функция RUN выполняет заданный запрос с указанными параметрами.
        Запрос передаётся ввиде строки с его SQL кодом.
        Если запрос не параметризированный, массив с параметрами указывать не нужно.
    */
    function run($sql, $params = NULL) {
        if ($this->query) {
            $this->query->closeCursor();
        }
        $this->query = self::$connect->prepare($sql);
        if ($params) {
            foreach ($params as $key => $value) {
                $name_param = (is_integer($key)) ? $key + 1 : $key;
                switch (gettype($value)) {
                    case 'integer':
                        $type_param = \PDO::PARAM_INT;
                        break;
                    case 'boolean':
                        $type_param = \PDO::PARAM_BOOL;
                        break;
                    case 'NULL':
                        $type_param = \PDO::PARAM_NULL;
                        break;
                    default:
                        $type_param = \PDO::PARAM_STR;
                }
                $this->query->bindValue($name_param, $value, $type_param);
            }
        }
        $this->query->execute();
    }

    /*
        Функция SELECT формирует запросиз переданых в качестве параметров составных частей и значений констант (TABLE_NAME, DEFAUL_ORDER, RELATIONS), после чего выполняет его.
        Если список извлекаемых полей не указан, будут извлечены все поля.
        Если не указан порядок сортировки, буден использован порядок указанный в константе класса DEFAULT_ORDER.
    */
    function prepareRequest(array $arr_params) {
        $arr_params = [
            'type' => 'SELECT',
            'filds' => '*',
            'values' => '',
        ]
        $str_sql = "";

        if (key_exists('type', $arr_params)) {
            if ($arr_params['type'] == "SELECT") {
                $str_sql = "{$arr_params['type']} {$fields} FROM {static::TABLE_NAME}";


            } else if ($arr_params['type'] == "INSERT") {
                if (key_exists('values', $arr_params)) {
                    $str_sql = "{$arr_params['type']} INTO {static::TABLE_NAME} ({$fields}) VALUES ({$values)";
                }


            } else if ($arr_params['type' == "UPDATE"]) {



            } else if ($arr_params['type'] == "DELETE") {



            }
        } else {
            return "Укажите пит запроса";
        }
    }

    function select(
        $fields = '*', // string
        $links = NULL, // array
        $where = '', //string
        $params = NULL, // array
        $order = '', // string
        $offset = NULL, // string
        $limit = NULL, // string
        $group = '', 
        $having = ''
    ) {
        $str_sql = 'SELECT ' . $fields . ' FROM ' . static::TABLE_NAME;
        if ($links) {
            foreach ($links as $ext_table) {
                $rel = static::RELATIONS[$ext_table];
                $str_sql .= ' ' . ((key_exists('type', $rel)) ? $rel['type'] : 'INNER') . ' JOIN ' .  $ext_table . ' ON ' . static::TABLE_NAME . '.' . $rel['external'] . ' = ' . $ext_table . '.' . $rel['primary'];
            }
        }
        if ($where) {
            $str_sql .= ' WHERE ' . $where;
        }
        if ($group) {
            $str_sql .= ' GROUP BY ' . $group;
            if ($having) {
                $str_sql .= ' HAVING ' . $having;
            }
        }
        if ($order) {
            $str_sql .= ' ORDER BY ' . $order;
        } else {
            $str_sql .= ' ORDER BY ' . static::DEFAULT_ORDER;
        }
        if ($limit && $offset !== NULL) {
            $str_sql .= ' LIMIT ' . $offset . ', ' . $limit;
        }
        $str_sql .= ';';
        $this->run($s, $params);
    }

    /*
        Методы интерфейса Iterator.
        В них будет выполняться выборка записей из результата выполнения запроса.
    */
    function current() {
        return $this->record;
    }

    function key() {
        return 0;
    }

    function next() {
        $this->record = $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    function rewind() {
        $this->record = $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    function valid() {
        return $this->record !== FALSE;
    }

    /*
        Функция get_record выбирает согласно заданным условиям фильтрации единственную запись и возвращает ее в качестве результата.
    */
    function get_record($fields = '*', $links = NULL, $where = '', $params = NULL) {
        $this->record = FALSE;
        $this->select($fields, $links, $where, $params);
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    /*
        Функция get выбирает согласно заданному значению указанного поля конкретную запись и возвращает ее в качестве результата.
        Если поле не указано, поиск значения будет выполняться в поле id.
    */
    function get($value, $key_field = 'id', $fields = '*', $links = NULL) {
        return $this->get_record($fields, $links, $key_field . ' = ?', [$value]);
    }

    function get_or_404($value, $key_field = 'id', $fields = '*', $links = NULL) {
        $rec = $this->get($value, $key_field, $fields, $links);
        if ($rec) {
            return $rec;
        } else {
            throw new \Page404Exception();
        }
    }

    protected function before_insert(&$fields) {}

    function insert($fields) {
        static::before_insert($fields);
        $s = 'INSERT INTO ' . static::TABLE_NAME;
        $s2 = $s1 = '';
        foreach ($fields as $n => $v) {
            if ($s1) {
                $s1 .= ', ';
                $s2 .= ', ';
            }
            $s1 .= $n;
            $s2 .= ':' . $n;
        }
        $s .= ' (' . $s1 . ') VALUES (' . $s2 . ');';
        $this->run($s, $fields);
        $id = self::$connect -> lastInsertId();
        return $id;
    }

    protected function before_update(&$fields, $value, $key_field = 'id') {}
    
    function update($fields, $value, $key_field = 'id') {
        static::before_update($fields, $value, $key_field);
        $s = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $s1 = '';
        foreach ($fields as $n => $v) {
            if ($s1) {
                $s1 .= ', ';
            }
            $s1 .= $n . ' = :' . $n;
        }
        $s .= $s1 . ' WHERE ' . $key_field . ' = :__key;';
        $fields['__key'] = $value;
        $this->run($s, $fields);
    }

    protected function before_delete($value, $key_field = 'id') {}

    function delete($value, $key_field = 'id') {
        static::before_delete($value, $key_field);
        $str_sql = 'DELETE FROM ' . static::TABLE_NAME;
        $str_sql .= ' WHERE ' . $key_field . ' = ?;';
        $this->run($str_sql, [$value]);
    }
}
