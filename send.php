<?php

#выносим в отдельную функцию для простоты

$sale=5000;
$company_id='';
$number=67676776;
$email='blabla@gmail.com';
function api_crm($api,$method,$param,$subdomain)
{
    if($api=='auth')
    {
        $leads=$param;
        $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
       // echo $link;
        //echo '<br>';
        //var_dump($leads);
    }
    else
    {
        $leads[$method] = array($param);
        $link='https://'.$subdomain.'.amocrm.ru/api/v2/'.$api;
        // echo $link;
       //echo '<br>';
       // var_dump($leads);
    }

    #Формируем ссылку для запроса
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете прочитать в мануале. */
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');

   // echo json_encode($leads,JSON_UNESCAPED_UNICODE);
    //echo '<br>';
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную

    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if($code!=200 && $code!=204)
        {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
        }
        else
        {
            return $out;
        }
    }
    catch(Exception $E)
    {
        die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
    }

}
$subdomain='ikutin';
$time=time();
$adress='ул.К.Заслонова';
$city='СПб';
$zip='19191';
$updated_at=1508274000;
$responsible_user_id=215302;
$name_courier='Аркадий';

#параметры для создания сделки
$param_lead=array(
    'name'=>'заказ корма',
    'created_at'=>$time,
    'updated_at'=>$updated_at,
    'status_id'=>100,
    'sale'=>$sale,
    'responsible_user_id'=>$responsible_user_id);

$param_contact=array(
    array(
        array(
            'name' => 'Александр Крылов',
            'responsible_user_id' => 504141,
            'created_by' => 504141,
            'created_at' => "1509051600"
        ),
        'company_id' => $company_id,
        'custom_fields' => array(
            'id'=>366431,
            'name'=>'Телефон',
            'values'=>array(
                'value'=>$number
            ),
            'id'=>366433,
            'name'=>'Email',
            'values'=>array(
                'value'=>$email,
                'enum'=>'571623'
            ),
            'id'=>366429,
            'name'=>'Должность',
            'values'=>array(
                'value'=>'Клиент'
            )

        )
    )


);
$user=array(
    'USER_LOGIN'=>'deus6543@gmail.com', #Ваш логин (электронная почта)
    'USER_HASH'=>'f9c20a2f1bbf6a1c9848de5f590dcca01a4ab4b9' #Хэш для доступа к API (смотрите в профиле пользователя)
);


 api_crm('auth','',$user,$subdomain);

echo api_crm('leads','add',$param_lead,$subdomain);

echo api_crm('contacts','add',$param_contact,$subdomain);






//echo api_crm('contacts','add',$param,$subdomain);




?>