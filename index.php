<?php
/**
 * @var $data
 */
require 'model.php';
require 'query.php';

define('HOST', TRUE);
define('NEWACC', FALSE);

// Информация об аккаунте
if(HOST) {
    $domain = 'sbogdanov.amocrm.sass';

    $user = [
        'USER_LOGIN' => 'twist.tracer@gmail.com',
        'USER_HASH' => 'b2ef0353a07a3772d3a23c6ae0bb5668'
    ];

    $subdomain = 'sbogdanov';
} else {
    $domain = 'amocrm.ru';

    $user = [
        'USER_LOGIN' => 'twist.tracer@gmail.com',
        'USER_HASH' => 'e368ec5528196d5a81a5664db1f9050b'
    ];

    if(NEWACC) {
        $subdomain = 'bogdanovnew';
    } else {
        $subdomain = 'bogdanov';
    }
}

/* Методы API */
#/private/api/v2/json/accounts/current - информация об аккаунте

#/api/unsorted/list/?api_key=".$user["USER_HASH"]."&login=".$user["USER_LOGIN"]." - список неразобранного
#/private/api/v2/json/leads/list - список сделок
#/private/api/v2/json/contacts/list - список контактов
#/private/api/v2/json/company/list - список компаний
#/private/api/v2/json/webhooks/list - список WebHooks

#/api/unsorted/add/?api_key=".$user["USER_HASH"]."&login=".$user["USER_LOGIN"]." - добавить в неразобранное
#/private/api/v2/json/leads/set - добавить/обновить сделку
#/private/api/v2/json/contacts/set - добавить/обновить сделку
#/private/api/v2/json/company/set - добавить/обновить сделку
#/private/api/v2/json/webhooks/subscribe - добавить WebHook

#/private/api/v2/json/webhooks/unsubscribe - удалить WebHook

#/private/api/v2/json/contacts/links - Связи между сделками и контактами

// Настройки запроса
$link = "https://".$subdomain.".".$domain."/private/api/v2/json/accounts/current"; #ссылка на метод API
$type = FALSE; #Тип запроса: FALSE(GET запрос, параметры нужно указать в url), CURLOPT_POST(Стандартный пост запрос), CURLOPT_CUSTOMREQUEST(Пост с телом запроса, например JSON)

$log = FALSE;

$request_str = ($type == 'CURLOPT_CUSTOMREQUEST')? file_get_contents('query.php', NULL, NULL, 20): $link;
$status = "default";

if(isset($_POST["query"]) && $_POST["query"] == "get_request_contents") {
    echo $request_str;
    exit;
}

if(isset($_POST["query"]) && $_POST["query"] == "send_request") {
    if(!auth($user, $subdomain, $domain)) {
        echo json_encode(["status" => "danger", "response_str" => "Авторизация не удалась!"]);
        exit;
    } else {
        $response = send_request($link, $data, $type, $log);
        $status = ((int)$response["code"] != 200 && (int)$response["code"] != 201 && (int)$response["code"] != 204)? "danger" : "success";
        echo json_encode(["status" => $status, "response_str" => $response["response_str"]]);
        exit;
    }
}

if(isset($_GET["page"]) && $_GET["page"] == "test_ajax") {
    $page = Template("test_ajax_page.php");
} else {
    $page = Template("template.php", array(
        "request_str" => $request_str,
        "status" => $status
    ));
}

echo $page;