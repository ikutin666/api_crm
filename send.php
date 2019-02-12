<?php

#выносим в отдельную функцию для простоты
function api_crm($api, $method, $param, $subdomain)
{

    if ($api == 'auth') {
        $link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';
        $lead = $param;
    } else {
        $lead[$method] = $param;
        $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/' . $api;
       // echo $link;
    }

    #Формируем ссылку для запроса
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете прочитать в мануале. */
    $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
echo '<br>';
    echo json_encode($lead);
    echo '<br>';
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($lead));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int)$code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable'
    );
    try {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        } else
        {

            return json_decode($out);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }

}


# для удобства get запросов
function api_crm_get($api,$name,$subdomain)
{
    $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
    $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/' . $api.$name;
   // echo $link;
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int)$code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable'
    );
    try {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        } else
        {

            return json_decode($out);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
}
#нахождение дубля
function dubl($fio,$adress,$number,$email,$subdomain)
{
    $lea=api_crm_get('leads','?query=заказ корма',$subdomain)->_embedded->items;#получаем сделки
    echo var_dump($leal);
    if($lea=="")
    {
        return false;
    }
    //echo var_dump($lea);

//echo $number;

        foreach ($lea as $elem) {

            // echo $elem->custom_fields[0]->values[0]->value;
            // echo '<br>';
            if ($elem->custom_fields[0]->values[0]->value == $adress)#если адреса достаки совпали
            {

                $contact = api_crm_get('contacts', '?id=' . $elem->contacts->id[0], $subdomain); #получаем привязанного пользователя
                // echo '<br>';
                //var_dump($contact->_embedded->items[0]->custom_fields[1]->values[0]->value);
                //    echo $number .' y '. $contact->_embedded->items[1]->values[0]->value;
                //  echo'<br>';
                // echo 'em '.$email;
                //echo $contact->_embedded->items[1]->values[0]->value;
                #проверяем поля фмио, номер, емейл
                if ($contact->_embedded->items[0]->name == $fio && $contact->_embedded->items[0]->custom_fields[0]->values[0]->value == (string)$number && $contact->_embedded->items[0]->custom_fields[1]->values[0]->value == $email) {
                    #возвращвет id дублируемой сделки
                    echo 'rrr '. $elem->id;
                    return $elem->id;
                }
            }
        }
    return false;

}
//tp=var_dump(api_crm_get('leads'));
//$tp=explode('',$tp);
//var_dump($tp[0]);
if( isset($_POST['Fio'])&&isset($_POST['Mobi'])&&isset($_POST['Email'])&&isset($_POST['Adress'])) {



    $subdomain = 'ikutin';
    $time = time();
    $responsible_user_id = 215302;
   $fio=$_POST['Fio'];
//$fio='Кутин Илья';
    $sale = 1000;
    $number = $_POST['Mobi'];
    //$number ='12121';
    $adress = $_POST['Adress'];
//$adress="213434";
   $email = $_POST['Email'];
 //$email='12321';




#для создания контакта
    $param_contact = array(
        array(
            'name' => $fio,


            'created_at' => $time,
            'tags' => "важный,доставка",
            'custom_fields' => array(

                array(
                    'id' => 366431,   #мобильный номер
                    'values' => array(
                        array(
                            'value' => $number,
                            'enum' => '571615'
                        )
                    )

                ),
                array(
                    'id' => 366433,   #email
                    'values' => array(
                        array(
                            'value' => $email,
                            'enum' => '571623'
                        )
                    )

                ),
                array(
                    'id' => 366429,   #должность
                    'values' => array(
                        array(
                            'value' => 'Клиент',

                        )
                    )

                )
            )
        )
        );
#лоя авторизации
    $user = array(
        'USER_LOGIN' => 'deus6543@gmail.com', #Ваш логин (электронная почта)
        'USER_HASH' => 'f9c20a2f1bbf6a1c9848de5f590dcca01a4ab4b9' #Хэш для доступа к API (смотрите в профиле пользователя)
    );
#авторизируемся
    api_crm('auth', '', $user, $subdomain);
#создаем контакт и получаем его id




   //api_crm('leads', 'add', $param_lead, $subdomain);
    #проверыяем наличие дубля
   $id=dubl($fio,$adress,$number,$email,$subdomain);

   echo "id". $id;
   if($id!='')
   {
       #для создаение задачи
       $param=array(
            array(
           'element_id'=>$id,
           'element_type'=> 2,
           'task_type'=>1,
           'text'=>'Позвонить срочно',
               'complete_till_at'=> $time+40000,
         'responsible_user_id'=> 3238366,
         )
       );
       var_dump(api_crm('tasks', 'add', $param, $subdomain));
       echo 'обнаружен дубль';

   }
   else
   {
       echo 'jhgj';
       $id_contact = api_crm('contacts', 'add', $param_contact, $subdomain)->_embedded->items[0]->id;
        #параметры для создания сделки
       $param_lead = array(
           array(
           'name' => 'заказ корма',
           'created_at' => $time,
           'updated_at' => $time,
           'status_id' => 24413818,
           'sale' => $sale,
           'responsible_user_id' => $responsible_user_id,
           'contacts_id' => array(
               $id_contact
           ), #добовляем id контакта
           'custom_fields'=>array(
               array(
                   'id'=>377487,
                   'values'=>array(
                       array(
                           'value'=>$adress
                       )
                   )
               )

           )
           )

       );
       api_crm('leads', 'add', $param_lead, $subdomain);
   }
}



?>