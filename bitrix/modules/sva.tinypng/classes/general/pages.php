<?php

class CSVATinyPNGPagesAll {

	public static function DetectReferer($hash) {
		if (strlen($_SERVER['HTTP_REFERER'])) {
			$partURL = parse_url($_SERVER['HTTP_REFERER']);
			$host = $_SERVER['HTTP_HOST'];
			if (strpos($host, ':') !== false) {
				$host = parse_url($host);
				$host = $host['host'];
			}
			if (isset($partURL['host']) && $partURL['host'] != $host) {
				$referExcl = trim(COption::GetOptionString('asd.seo', 'exclude_referrers'));
				if (strlen($referExcl)) {
					$referExcl = explode("\n", $referExcl);
					TrimArr($referExcl, true);
					if (preg_match('/([^\.]+\.[^\.]+)$/is', $partURL['host'], $matches)) {
						if (in_array($matches[1], $referExcl)) {
							return false;
						}
					} else {
						return false;
					}
				}
				$refer = trim($_SERVER['HTTP_REFERER']);
				$md5refer = md5($refer);
				if (defined('ASD_SEO_DEBUG') && ASD_SEO_DEBUG===true && isset($_SESSION['ASD_SEO_REFERERS'])) {
					unset($_SESSION['ASD_SEO_REFERERS']);
				}
				if (!isset($_SESSION['ASD_SEO_REFERERS'])) {
					$_SESSION['ASD_SEO_REFERERS'] = array();
				}
				if (!in_array($md5refer, $_SESSION['ASD_SEO_REFERERS'])) {
					$_SESSION['ASD_SEO_REFERERS'][] = $md5refer;
				} else {
					return true;
				}
				CSVATinyPNGPages::AddReferer(array(
					'HASH' => $hash,
					'REF_HASH' => $md5refer,
					'REFERER' => $refer,
				));
			}
		}
	}

}