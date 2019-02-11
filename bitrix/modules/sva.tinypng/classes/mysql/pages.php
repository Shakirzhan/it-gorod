<?php
IncludeModuleLangFile(__FILE__);

class CSVATinyPNGPages extends CSVATinyPNGPagesAll {

	const mysqlError = 'MySQL error in class CSVATinyPNGPages on line ';

	private static function GetAvailableFields($table) {
		$arFields = array(
			'b_asd_seo_pages' => array('HASH', 'SITE_ID', 'URL', 'STATUS', 'IN_SITEMAP', 'OBJ_TITLE', 'REAL_TITLE',
									'DESCRIPTION', 'KEYWORDS', 'OG_TAGS', 'OG_TAGS_DETAIL', 'H1', 'ELEMENT_ID', 'SHARE_TW',
									'SHARE_GP', 'SHARE_FB', 'SHARE_VK', 'SHARE_MA', 'SHARE_OD', 'NEW_TITLE',
									'NEW_DESCRIPTION', 'NEW_KEYWORDS', 'REDIRECT', 'REDIRECT_STATUS', 'ERRORS', 'TIMESTAMP',
									'G_CNT', 'Y_CNT', 'WAIT_TIME', 'TMP'),
			'b_asd_seo_referers' => array('HASH', 'REF_HASH', 'REFERER', 'FROM', 'START', 'TIMESTAMP', 'COUNT'),
		);
		return $arFields[$table];
	}

	public static function Add($arFields) {
		global $DB;
		$table = 'b_asd_seo_pages';
		if (!isset($arFields['HASH'])) {
			return false;
		}
		if (!$DB->Query('SELECT * FROM `'.$table.'` WHERE `HASH`="'.$DB->ForSQL($arFields['HASH']).'";', true, self::mysqlError.__LINE__)->Fetch()) {
			foreach ($arFields as $k => $v) {
				if (($k=='ERRORS' || $k=='OG_TAGS_DETAIL') && is_array($v)) {
					$v = implode(',', $v);
				}
				if (in_array($k, self::GetAvailableFields($table))) {
					$arFields[$k] = '"'.$DB->ForSQL($v).'"';
				} else {
					unset($arFields[$k]);
				}
			}
			return $DB->Insert($table, $arFields, self::mysqlError.__LINE__);
		} else {
			return false;
		}
	}

	public static function Update($hash, $arFields) {
		global $DB;
		$table = 'b_asd_seo_pages';
		$hash = $DB->ForSQL(trim($hash));
		foreach ($arFields as $k => $v) {
			if (($k=='ERRORS' || $k=='OG_TAGS_DETAIL') && is_array($v)) {
				$v = implode(',', $v);
			}
			if (in_array($k, self::GetAvailableFields($table))) {
				$arFields[$k] = '"'.$DB->ForSQL($v).'"';
			} else {
				unset($arFields[$k]);
			}
		}
		if (!empty($arFields)) {
			$arFields['TIMESTAMP'] = 'NOW()';
			return $DB->Update($table, $arFields, 'WHERE `HASH`="'.$hash.'"', self::mysqlError.__LINE__);
		} else {
			return false;
		}
	}

	public static function GetList($arSort, $arFilter=array()) {
		global $DB;
		$table = 'b_asd_seo_pages';
		$by = key($arSort);
		$order = current($arSort);
		if (!in_array($by, self::GetAvailableFields($table))) {
			$by = 'HASH';
		}
		if (strtoupper($order)!='ASC') {
			$order = 'DESC';
		}
		$strWhere = '';
		if (!empty($arFilter)) {
			foreach ($arFilter as $k => $v) {
				$modif = substr($k, 0, 1);
				if (in_array($modif, array('?'))) {
					$k = substr($k, 1);
				}
				$v = $DB->ForSQL(trim($v));
				if (in_array($k, self::GetAvailableFields($table))) {
					$strWhere .= ' AND `'.$k.'`';
					if ($modif == '?') {
						$strWhere .= ' LIKE ';
						$strWhere .= '"%'.$v.'%"';
					} else {
						$strWhere .= ' = ';
						$strWhere .= '"'.$v.'"';
					}
				} elseif ($k == 'ALL_META') {
					$strWhere .= ' AND (`OBJ_TITLE` LIKE "%'.$v.'%" OR `REAL_TITLE` LIKE "%'.$v.'%" OR
										`DESCRIPTION` LIKE "%'.$v.'%" OR `KEYWORDS` LIKE "%'.$v.'%" OR `H1` LIKE "%'.$v.'%")';
				} elseif ($k == 'PHRASE') {
					$strWhere .= ' AND `HASH` IN (SELECT `HASH` FROM `b_asd_seo_phrases` WHERE `PHRASE` LIKE "%'.$v.'%")';
				}
			}
		}
		if (strlen($strWhere)) {
			$strWhere = ' WHERE 1=1 '.$strWhere;
		}
		return $GLOBALS['DB']->Query('SELECT
											*, '.$GLOBALS['DB']->DateToCharFunction('TIMESTAMP').' AS `TIMESTAMP_FORMATED`
										FROM
											`'.$table.'`
											'.$strWhere.'
										ORDER BY '.$by.' '.$order.';', true, self::mysqlError.__LINE__);
	}

	public static function GetByHash($hash) {
		$hash = $GLOBALS['DB']->ForSQL(trim($hash));
		return $GLOBALS['DB']->Query('SELECT
										*, '.$GLOBALS['DB']->DateToCharFunction('TIMESTAMP').' AS `TIMESTAMP_FORMATED`
									FROM
										`b_asd_seo_pages`
									WHERE
										`HASH`="'.$hash.'";', true, self::mysqlError.__LINE__);
	}

	public static function Delete($hash) {
		$hash = $GLOBALS['DB']->ForSQL(trim($hash));
		$GLOBALS['DB']->Query('DELETE FROM `b_asd_seo_referers` WHERE `HASH`="'.$hash.'";', true, self::mysqlError.__LINE__);
		$GLOBALS['DB']->Query('DELETE FROM `b_asd_seo_bot_hits` WHERE `HASH`="'.$hash.'";', true, self::mysqlError.__LINE__);
		$GLOBALS['DB']->Query('DELETE FROM `b_asd_seo_phrases` WHERE `HASH`="'.$hash.'";', true, self::mysqlError.__LINE__);
		$GLOBALS['DB']->Query('DELETE FROM `b_asd_seo_errors` WHERE `HASH`="'.$hash.'";', true, self::mysqlError.__LINE__);
		return $GLOBALS['DB']->Query('DELETE FROM `b_asd_seo_pages` WHERE `HASH`="'.$hash.'";', true, self::mysqlError.__LINE__);
	}

	public static function AddReferer($arFields) {
		global $DB;
		$arUpdate = array();
		$table = 'b_asd_seo_referers';
		foreach ($arFields as $k => $v) {
			if (in_array($k, self::GetAvailableFields($table))) {
				$arUpdate['`'.$k.'`'] = "'".$DB->ForSQL($v)."'";
			}
		}
		$DB->Query("INSERT INTO `".$table."` (".implode(', ', array_keys($arUpdate)).", `START`)
								VALUES (".implode(', ', array_values($arUpdate)).", NOW())
								ON DUPLICATE KEY UPDATE `COUNT`=`COUNT`+1;");
	}

	public static function GetReferers($arSort, $arFilter=array(), $arGroupBy=false, $arNavStartParams=false) {
		global $DB;
		$tableR = 'b_asd_seo_referers';
		$tableP = 'b_asd_seo_pages';
		$limit = '';
		$by = key($arSort);
		$order = current($arSort);
		if (!in_array($by, self::GetAvailableFields($tableR))) {
			$by = 'HASH';
		}
		if (strtoupper($order)!='ASC') {
			$order = 'DESC';
		}
		$strWhere = '';
		if (!empty($arFilter)) {
			foreach ($arFilter as $k => $v) {
				$modif = substr($k, 0, 1);
				if (in_array($modif, array('?'))) {
					$k = substr($k, 1);
				}
				$vOrig = trim($v);
				$v = $DB->ForSQL($vOrig);
				$inR = in_array($k, self::GetAvailableFields($tableR));
				$inP = in_array($k, self::GetAvailableFields($tableP));
				if ($inR || $inP) {
					if ($inR) {
						$strWhere .= ' AND R.`'.$k.'`';
					} else {
						$strWhere .= ' AND P.`'.$k.'`';
					}
					if ($k == 'URL') {
						$urlPart = parse_url($vOrig);
						if (isset($urlPart['path'])) {
							$v = $urlPart['path'];
						} else {
							$v = '/';
						}
						if (isset($urlPart['query'])) {
							$v .= '?'.$urlPart['query'];
						}
						$v = $DB->ForSQL($v);
					}
					if ($modif == '?') {
						$strWhere .= ' LIKE ';
						$strWhere .= '"%'.$v.'%"';
					} else {
						$strWhere .= ' = ';
						$strWhere .= '"'.$v.'"';
					}
				} elseif ($k == 'START1') {
					$strWhere .= ' AND `START`';
					$strWhere .= ' >= ';
					$strWhere .= $DB->CharToDateFunction($vOrig);
				} elseif ($k == 'START2') {
					$strWhere .= ' AND `START`';
					$strWhere .= ' <= ';
					$strWhere .= $DB->CharToDateFunction($vOrig);
				} elseif ($k == 'START_DAYS') {
					$strWhere .= ' AND DATE_SUB(NOW(), INTERVAL '.intval($vOrig).' DAY) < `START`';
				}
			}
		}
		if (strlen($strWhere)) {
			$strWhere = ' WHERE 1=1 '.$strWhere;
		}
		if ($arNavStartParams != false) {
			if (isset($arNavStartParams['nTopCount'])) {
				$limit = ' LIMIT '.intval($arNavStartParams['nTopCount']);
			}
		}
		return $DB->Query('SELECT
								R.*, '.$DB->DateToCharFunction('R.START').' AS `START_FORMATED`
								, '.$DB->DateToCharFunction('R.TIMESTAMP').' AS `TIMESTAMP_FORMATED`
								, P.`URL`, P.`SITE_ID`, P.`REAL_TITLE`
							FROM
								`'.$tableR.'` R
							LEFT JOIN
								`'.$tableP.'` P ON (R.HASH = P.HASH)
								'.$strWhere.'
							ORDER BY '.$by.' '.$order.
							$limit.';', false, self::mysqlError.__LINE__);
	}

	public static function DeleteReferer($ID) {
		return $GLOBALS['DB']->Query('DELETE FROM `b_asd_seo_referers` WHERE `ID`="'.intval($ID).'";', true, self::mysqlError.__LINE__);
	}

	public static function refererCount() {
		$ar = $GLOBALS['DB']->Query('SELECT COUNT("x") AS CNT FROM `b_asd_seo_referers`;', true, self::mysqlError.__LINE__)->Fetch();
		return (int)$ar['CNT'];
	}

	public static function clearReferers() {
		global $DB;
		$count = (int)COption::GetOptionString('asd.seo', 'referers_day', 180);
		if (!$count) {
			$count = 180;
		}
		$DB->query('DELETE FROM `b_asd_seo_referers` WHERE DATE_SUB(CURDATE(), INTERVAL '.$count.' DAY) > `TIMESTAMP` ORDER BY `TIMESTAMP` DESC;');
		return 'CSVATinyPNGPages::clearReferers();';
	}
}