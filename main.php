<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use VK\Client\VKApiClient;

function logOut($msg)
{
	echo "[" . date('H:i:s d/m/Y') . "] $msg" . PHP_EOL;
}

logOut("========================"); // Для сохранения читабельности лога
logOut("Начинаем работу скрипта");
// Проверяем настройки
if (empty($access_token) || empty($dialogs)) {
	logOut("[ERROR] Перед запуском необходимо настроить параметры скрипта в файле config.php!");
	exit;
}
// Инициируем SDK
$vk = new VKApiClient('5.90');
// Получаем наш ID пользователя
$my_id = $vk->users()->get($access_token); // users.get без параметров возвращает данные о юзере, которому принадлежит access token
$my_id = $my_id[0]['id'];
logOut("Наш идентификатор: $my_id");
// Запускаем цикл обхода диалогов
$to_delete = array();
foreach ($dialogs as $dialog) {
	logOut("Приступаем к обработке диалога №$dialog");
	// Выполняем запрос на получение истории переписки
	$history = $vk->messages()->getHistory($access_token, array(
		'count' => $history_count,
		'user_id' => $dialog,
		'extended' => '1'
	));
	// Запоминаем идентификаторы последних прочитанных сообщений
	$in_read = $history['conversations'][0]['in_read']; // нами
	$out_read = $history['conversations'][0]['out_read']; // собеседником
	// Проходим по каждому сообщению в поисках отправленных нами
	foreach ($history['items'] as $message) {
		if ($message['from_id'] == $my_id) { // если сообщение принадлежит нам
			$time_diff = time() - $message['date']; // секунд с момента отправки
			if ($time_diff > $interval && $time_diff < (60 * 60 * 24)) { // и если сообщение бьет по интервалу (больше $interval но меньше 24 часов (после пропадает возможность удалить сообщение у собеседника))
				if ($message['id'] <= $out_read) { // не забываем убедиться в том, что собеседник уже прочел сообщение, иначе смысл его удалять?
					// Добавляем идентификатор сообщения в список на удаление
					$to_delete[] = $message['id'];
				} else {
					logOut("Сообщение №" . $message['id'] . " (" . $message['text'] . ") подлежит удалению, но собеседник до сих пор не прочёл его.");
				}
			}
		}
	}
	logOut("К удалению отмечено " . count($to_delete) . " сообщений.");
}
// Удаляем отмеченные сообщения
if (!empty($to_delete)) {
	$result = $vk->messages()->delete($access_token, array(
		'message_ids' => $to_delete,
		'delete_for_all' => '1'
	));
	$count = array_count_values($result)['1'];
	logOut("Количество успешно удаленныых сообщений: $count");
}
logOut("Завершаем работу скрипта");
logOut("========================"); // Для сохранения читабельности лога
?>
