<?php
namespace Models\DataBases;
class DataBase  {
    protected const TABLE_NAME = ''; // Имя таблицы
    protected const DEFAULT_ORDER = ''; // Сортировка по умолчанию
    protected const RELATIONS = []; // Масив связанх таблиц

    private $query = NULL; // Переменная для объекта запроса

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
            self::$connect = \Models\DataBases\DataBase::connect_to_db();
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

    // $values_arr = [
    //     "price" => 100, 
    //     "name" => "товар 1",
    //     "date" => "2022-08-16",
    //     "value" => "hbsdvhsdv"
    // ];

    function run($sql_str, $values = NULL) {
        if ($this->query) {
            $this->query->closeCursor();
            // print_r("приложение очистилось \n");
            // $this->query = null;
        }
        $this->query = self::$connect->prepare($sql_str);
        if ($values) {
            foreach ($values as $key => $value) {
                $name_param = (is_integer($key)) ? $key + 1 : $key;
                    switch (gettype($value)) {
                        case 'integer':
                            $type_param = \PDO::PARAM_INT; // 1
                            break;
                        case 'boolean':
                            $type_param = \PDO::PARAM_BOOL; // 5
                            break;
                        case 'NULL':
                            $type_param = \PDO::PARAM_NULL; // 0
                            break;
                        case 'string':
                            $type_param = \PDO::PARAM_STR; // 2
                            break;
                        default:
                            $type_param = \PDO::PARAM_STR;
                    }
                    $this->query->bindValue((":" . $name_param), $value, $type_param); 
                    // print_r(":" . $name_param . ' ' . $value . ' ' . $type_param);
            }
        }
        // var_dump($sql_str);
        // var_dump($values);
        $this->query->execute();
        // $this->query->closeCursor();
        // var_dump($this->query->fetch(\PDO::FETCH_ASSOC));
        return $this->query;
    }

    /*
        Функция SELECT формирует запрос из переданyного массива в качестве параметров содержащий составные части и значения констант (TABLE_NAME, DEFAUL_ORDER, RELATIONS), после чего выполняет его.
        Если список извлекаемых полей не указан, будут извлечены все поля.
        Если не указан порядок сортировки, буден использован порядок указанный в константе класса DEFAULT_ORDER.
    */
    // $sql = [
    //     "fields" => ["id", "name", "date"], // array
    //     "links" => ["users", "products", "comments"], // array
    //     "where" => '', //string
    //     "order" => ["id", "name"], // array
    //     "limit" => [3, 9], // array кол-во записей
    //     "group" => ["users", "product"], 
    //     "having" => '',
    //     "values" => [] // array индексированный или асоциативный массив с 
    // ];
    function select(array $sql) {
        if (!$sql["fields"]) {
            $sql_str = 'SELECT ' . '*' . ' FROM ' . static::TABLE_NAME;
        } else if ($sql["fields"]) {
            $sql_str = 'SELECT ' . implode(",", $sql["fields"]) . ' FROM ' . static::TABLE_NAME;
        }
        
        if ($sql["links"]) {
            foreach ($sql["links"] as $table) {
                $link_table = static::RELATIONS[$table];
                $sql_str .= ' ' . ((key_exists('type', $link_table)) ? $link_table['type'] : 'INNER')
                 . ' JOIN ' .  $table . ' ON '
                 . static::TABLE_NAME . '.' . $link_table['external']
                 . ' = '
                 . $table . '.' . $link_table['primary'];
            }
        }
        if ($sql["where"]) {
            $sql_str .= ' WHERE ' . $sql["where"];
        }
        if ($sql["group"]) {
            $sql_str .= ' GROUP BY ' . implode(",", $sql["group"]);
            if ($sql["having"]) {
                $sql_str .= ' HAVING ' . $sql["having"];
            }
        }
        if ($sq["order"]) {
            $sql_str .= ' ORDER BY ' . implode(",", $sq["lorder"]);
        } else {
            $sql_str .= ' ORDER BY ' . static::DEFAULT_ORDER;
        }
        if ($sql["limit"]) {
            $sql_str .= ' LIMIT ' . $sql["limit"][0] . ', ' . $sql["limit"][1];
        }
        $sql_str .= ';';
        // echo $sql_str;
        $this->run($sql_str, $sql["values"]);
        return $this->run($sql_str, $sql["values"]);
    }

    /*
        Методы интерфейса Iterator.
        В них будет выполняться выборка записей из результата выполнения запроса.
    */
    private $record = FALSE; // Переменная для хранения массива со значениями очередной извлечённой записи.
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

    protected function before_insert(&$values) {}
    function insert(array $values) {
        static::before_insert($values);
        $sql_str = 'INSERT INTO ' . static::TABLE_NAME;
        $filds_str = $values_str = '';
        foreach ($values as $key => $value) {
            if ($filds_str) {
                $filds_str .= ', ';
                $values_str .= ', ';
            }
            $filds_str .= $key;
            $values_str .= ':' . $key;
        }
        $sql_str .= ' (' . $filds_str . ') VALUES (' . $values_str . ');';
        $this->run($sql_str, $values);
        $id = self::$connect->lastInsertId();
        return $id;
    }

    protected function before_update(
        &$values, 
        $value_field, 
        $key_field = 'id'
    ) {}
    function update(
        array $values, 
        string $value_field, 
        string $key_field = 'id'
    ) {
        static::before_update($values, $value_field, $key_field);
        $sql_str = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $filds_str = '';
        foreach ($values as $n => $v) {
            if ($filds_str) {
                $filds_str .= ', ';
            }
            $filds_str .= $n . ' = :' . $n;
        }
        $sql_str .= $filds_str . ' WHERE ' . $key_field . ' = :__key;';
        $values['__key'] = $value_field;
        // print_r($values);
        // print_r($sql_str);
        // print_r("\n \n");
       $res = $this->run($sql_str, $values);
       return $res->rowCount();
    }

    protected function before_delete(
        $value_field, 
        $key_field = 'id'
    ) {}
    function delete(
        string $value_field, 
        string $key_field = 'id'
    ) {
        static::before_delete($value_field, $key_field);
        $sql_str = 'DELETE FROM ' . static::TABLE_NAME;
        $sql_str .= ' WHERE ' . $key_field . ' = ?;';
        $this->run($sql_str, [$value_field]);
    }


    /**
     * метод ищет записи по полю с нужным значением
     * @param string $field имя поля в БД по которому будет идти поиск
     * @param array $value искомое значение
     * @return array возвращает асс.массив записей
     */
    function getRecords($field, $value) 
    {
        $records = $this->select([
            'where' => $field . ' = :' . $field,
            'values' => [
                $field => $value
            ]
        ]);
        $records = $records->fetchAll(\PDO::FETCH_ASSOC);
        return $records;
    }

    /**
     * добавляет новые записи в таблицу
     * 
     * принимает массив со вложеными масивами,
     * каждый вложенный масив представляет запись в БД,
     * ключи вложенного массива должны совпадать с наименованиями полей в таблице,
     * перед тем как добавить запись в БД, проверяет ее существование по указаному полю в таблице и одноименному ключу в массиве
     * 
     * 
     * @param array $list масив со вложенными масивами, представляет записи
     * @param string $field наименование поля в таблице и ключа в БД по которому проверять существование записи в БД
     */
    function recordNewCities($list, $field) 
    {
        foreach ($list as $i => $value) {
            $record = $this->getRecords($field, $value[$field]);
            if ($record == []) {
                $id_new_record = $this->insert($value);
            }
        };
    }

    /**
     * сопоставляет запись из массива с записью в БД
     * 
     * принимает массив со вложеными масивами,
     * каждый вложенный масив представляет запись в БД,
     * ключи вложенного массива должны совпадать с наименованиями полей в таблице,
     * указывается поле по которому будет происходить поиск,
     * если запись найдена в БД, она добавляется добавляется во вложенный массив по ключом "bd"
     * после обработки всех записей, возвращается обновленный массив
     * 
     * @param array $list масив со вложенными масивами, представляет записи
     * @param string $field наименование поля в таблице и ключа в БД по которому проверять существование записи в БД
     * @return array обновленный массив с найдеными записями в БД
     */
    function compareCities(&$list, $field) 
    {
        $arr = [];
        foreach ($list as $i => $value) {
            $record = $this->getRecords($field, $value[$field]);
            $list[$i]['db'] = $record;
        };
        return $list;
    }
}
