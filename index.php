<?php
require_once('db.php');

function Generate($length = 5) {
    $symbols = "abcdefghijklmnopqrstuvwxyz0123456789";

    if($length <= 0) $length = 5;
    else if($length > strlen($symbols)) $length = strlen($symbols);

    $key = str_split($symbols);
    $result = "";

    do {
        $el = rand(0, count($key));
        $result .= $key[$el];
    }
    while(strlen($result) !== $length);

    return $result;
}

$link = htmlspecialchars($_GET['url']);
if(!empty($_GET['url'])) {
    $select = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `short` WHERE `url` = '{$link}'"));

    if($select) {
        $result = [
            'url'  => $select['url'],
            'key'  => $select['key'],
            'link' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $select['key'],
        ];
    }
    else {
        $key = Generate();

        mysqli_query($db, "INSERT INTO `short` (`url`, `key`) VALUES ('{$link}', '{$key}') ");
        $select = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `short` WHERE `url` = '{$link}'"));
        $result = [
            'url'  => $select['url'],
            'key'  => $select['key'],
            'link' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $select['key'],
        ];
    }
    echo json_encode($result);
    exit();
}
?>

<!doctype html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Генератор коротких ссылок</title>
		<link rel="stylesheet" href="style.css">
    </head>
    <body>
	<div class="parent">
		<div class="block">
			<h1>Генератор коротких ссылок</h1>
			<form method="POST" id="form" onsubmit="return FormSubmit();">
				<input type="text" id="url" required>
				<input type="submit" value="Генерировать">
			</form>
			<p id="link"></p>
		</div>
	</div>
        

		<script>
			let url = document.getElementById('url');
			let link = document.getElementById('link');
			
			function GetData(ajaxurl) {
				let result = false;
				let request = new XMLHttpRequest();
				request.open('GET', ajaxurl, false);
				request.send(null);
				if(request.status === 200) {
					result = request.responseText;
				}
				return result;
			}
			
			function FormSubmit() {
				let query = "index.php?url=" + url.value;
				let result = GetData(query);
			
				if(result !== false) {
					result = JSON.parse(result);
					link.innerHTML = "<a href='" + result.link + "' target='_blank'>" + result.link + "</a>";
				}
				else {
					link.innerText = "При выполнении запроса произошла ошибка";
				}
			
				return false;
			}
		</script>
    </body>
</html>