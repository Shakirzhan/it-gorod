<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Ошибка: 404 - Страница не найдена");
?>
<div class="page-404">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<h1>Ошибка 404</h1>
				<h3>Страница не найдена</h3>
				<p>Неправильно набран адрес или такой страницы больше не существует</p>
				<a class="mt20 btn btn-lg btn-default" href="<?=SITE_DIR?>">На главную</a>
			</div>
			<div class="col-xs-12 col-md-6">
				<div class="icon-404">404</div>
			</div>
		</div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>