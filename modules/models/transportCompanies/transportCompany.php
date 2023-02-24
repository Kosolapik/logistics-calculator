<?php
namespace Models\TransportCompanies {
    class TransportCompany {

        /** 
         * подготавливает данные из формы калькулятора для расчёта доставки
         * 
         * в зависимости от указанной ТК в параметрах функции находит нужный ИД города в БД
         * из общего объёма груза и количества мест, высчитывает Д*Ш*В для одного места, таким образом можем приблизительно выявлять негабаритные грузы 
         * @param array $data данные формы калькулятора
         * @param stirng $forCompany транспортная компания
         */
        protected function prepareData ($data, $forCompany) {
            if ($forCompany == 'pec') {
                $db = new \Models\DataBases\PecCities();
            } else if ($forCompany == 'kit') {
                $db = new \Models\DataBases\KitCities();
            }

            $arr['fromLocalityId'] = $db->getRecords('id_kladr', $data['fromLocality']['id']) ? $db->getRecords('id_kladr', $data['fromLocality']['id']) : 000;
            $arr['whereLocalityId'] = $db->getRecords('id_kladr', $data['whereLocality']['id']) ? $db->getRecords('id_kladr', $data['whereLocality']['id']) : 000;
            $arr['length'] = round((pow(($data['volume']/$data['quantity']), 1/3) * 2.25), 2);
            $arr['width'] = round((pow(($data['volume']/$data['quantity']/(pow(($data['volume']/$data['quantity']), 1/3) * 2.25)), 1/2)), 2);
            $arr['height'] = round((pow(($data['volume']/$data['quantity']/(pow(($data['volume']/$data['quantity']), 1/3) * 2.25)), 1/2)), 2);
            $arr['quantity'] = $data['quantity'];
            $arr['volume'] = $data['volume'];
            $arr['maxSize'] = round((pow(($data['volume']/$data['quantity']), 1/3) * 2.25), 2);
            $arr['weight'] = $data['weight'];
            $arr['price'] = $data['price'];
            $arr['typeCargo'] = $data['type-cargo'];

            return $arr;
        }
    }
}
?>