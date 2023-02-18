<?php
namespace Models
{

/**
 * Главный класс SDK
 */
class PecomKabinet
{
	/**
	 * Версия серверного API (не PHP SDK!)
	 * @const string
	 */
	const VERSION = '1.0';

	/**
	 * Базовый URL по умолчанию
	 * @const string
	 */
	const API_BASE_URL = 'https://kabinet.pecom.ru/api/v1/';

	/**
	 * Имя пользователя
	 * @var string
	 */
	private $_api_login = '';

	/**
	 * Ключ доступа к API
	 * @var string
	 */
	private $_api_key = '';

	/**
	 * Базовый URL
	 * @var string
	 */
	private $_api_url = '';

	/**
	 * Переопределение настроек curl
	 * @var array
	 */
	private $_curl_options = array();

	/**
	 * Экземпляр curl'а
	 * @var resource
	 */
	private $_ch = null;

	/**
	 * Конструктор класса
	 * @param string $api_login Имя пользователя
	 * @param string $api_key Ключ доступа к API
	 * @param array $curl_options Дополнительные параметры для curl (возможно переопределение)
	 * @param string $api_url Базовый URL API (необязательный параметр), если не указан используется адрес по умолчанию
	 * @throws PecomKabinetException
	 */
	public function __construct($api_login, $api_key, $curl_options = array(), $api_url = '')
	{
		$this->_api_login = $api_login;
		$this->_api_key = $api_key;
		$this->_api_url = ($api_url === '') ? self::API_BASE_URL : $api_url;
		$this->_curl_options = $curl_options;
	}

	/**
	 * Осуществляет вызов метода API
	 * @param string $controller Название группы
	 * @param string $action Название метода
	 * @param mixed $data Входящие данные запроса
	 * @param bool $assoc Формат возвращаемого результата, если установлено в true результат будет объектом, false -- массивом
	 * @return mixed Результат выполнения запроса
	 * @throws PecomKabinetException В случае ошибок при осуществлении запроса
	 */
	public function call($controller, $action, $data, $assoc = false)
	{
		if (is_null($this->_ch))
		{
			$this->_init_curl();
		}

		$json_data = json_encode($data);

		curl_setopt_array($this->_ch, array(
			CURLOPT_URL => $this->_construct_api_url($controller, $action),
			CURLOPT_POSTFIELDS => $json_data,
		));

		$result = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
		{
			throw new PecomKabinetException(curl_error($this->_ch));
		}
		else
		{
			$http_code = intval(curl_getinfo($this->_ch, CURLINFO_HTTP_CODE));

			if ($http_code != 200)
			{
				throw new PecomKabinetException(sprintf('HTTP error code: %d', $http_code));
			}

			$decoded_result = json_decode($result, $assoc);
		}

		return $decoded_result;
	}

	/**
	 * Закрывает curl-соединение
	 * @return void
	 */
	public function close()
	{
		if (!is_null($this->_ch))
		{
			curl_close($this->_ch);
		}
	}

	/**
	 * Возвращает полный URL для запроса к методу API
	 * @param string $controller Название группы
	 * @param string $action Название метода
	 * @return string Полный URL
	 */
	private function _construct_api_url($controller, $action)
	{
		return sprintf('%s%s/%s/', $this->_api_url, $controller, $action);
	}

	/**
	 * Инициализирует curl
	 * @return resource Экземпляр curl'а для запросов
	 */
	private function _init_curl()
	{
		$this->_ch = curl_init();
		$options = $this->_curl_options + array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POST => TRUE,
			CURLOPT_SSL_VERIFYPEER => TRUE,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_CAINFO => dirname(__FILE__) . '/cacert-kabinet_pecom_ru.pem',
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_USERPWD => sprintf('%s:%s', $this->_api_login, $this->_api_key),
			CURLOPT_ENCODING => 'gzip',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json; charset=utf-8',
			));
		curl_setopt_array($this->_ch, $options);
	}
}
}
?>