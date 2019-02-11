<?if(!check_bitrix_sessid()) return;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
echo CAdminMessage::ShowNote(Loc::getMessage("SVA_TINY_PNG_MODULE_IS_UNINSTALED"));
