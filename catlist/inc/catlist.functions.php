<?php
/**
* Catlist Plugin / Functions
*
* @package catlist
* @author Dmitri Beliavski
* @copyright (c) 2021-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

// define globals
define('SEDBY_CATLIST_REALM', '[SEDBY] Catlist');

require_once cot_incfile('page', 'module');

/**
* Generates CatList widget
* @param  string  $tpl					01. Template code
* @param  integer $items				02. Number of items to show. 0 - show all items
* @param  string  $order				03. Sorting order (SQL)
* @param  string  $extra				04. Custom selection filter (SQL)
* @param  integer $offset			05. Exclude specified number of records starting from the beginning
* @param  integer $cache_name	06. Cache name
* @param  integer $cache_ttl		07. Cache TTL
* @return string              Parsed HTML
*/
function cot_catlist($tpl = 'catlist', $items = 0, $order = '', $extra = '', $offset = 0, $pagination = '', $ajax_block = '', $cache_name = '', $cache_ttl = '') {

	$enableAjax = $enableCache = $enablePagination = false;

  // Condition shortcut
  if (Cot::$cache && !empty($cache_name) && ((int)$cache_ttl > 0)) {
    $enableCache = true;
    $cache_name = str_replace(' ', '_', $cache_name);
  }

	if ($enableCache && Cot::$cache->db->exists($cache_name, SEDBY_CATLIST_REALM))
		$output = Cot::$cache->db->get($cache_name, SEDBY_CATLIST_REALM);
	else {

		/* === Hook === */
		foreach (cot_getextplugins('catlist.first') as $pl) {
			include $pl;
		}
		/* ===== */

		// Condition shortcuts
		if ((Cot::$cfg['turnajax']) && (Cot::$cfg['plugin']['catlist']['ajax']) && !empty($ajax_block))
			$enableAjax = true;

		if (!empty($pagination) && ((int)$items > 0))
			$enablePagination = true;

		if ($enableAjax && Cot::$cfg['plugin']['catlist']['encrypt_ajax_urls']) {
			$h = $tpl.','.$items.','.$order.','.$extra.','.$offset.','.$pagination.','.$ajax_block.','.$cache_name.','.$cache_ttl;
			$h = cot_encrypt_decrypt('encrypt', $h, Cot::$cfg['plugin']['catlist']['encrypt_key'], Cot::$cfg['plugin']['catlist']['encrypt_iv']);
			$h = str_replace('=', '', $h);
		}

		$db_structure = Cot::$db->structure;

		// Display the items
		(!isset($tpl) || empty($tpl)) && $tpl = 'catlist';
		$t = new XTemplate(cot_tplfile($tpl, 'plug'));

		// Get pagination if necessary
		if ($enablePagination)
			list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
		else
			$d = 0;

		// Compile items number
    ((int)$offset <= 0) && $offset = 0;
    $d = $d + (int)$offset;
		$sql_limit = ($items > 0) ? "LIMIT $d, $items" : '';

		$sql_order = empty($order) ? "" : " ORDER BY $order";

		// Compile all conditions
		$sql_cond		= empty($extra) ? "" : " WHERE $extra";

		/* === Hook === */
		foreach (array_merge(cot_getextplugins('pagelist.query')) as $pl) {
			include $pl;
		}
		/* ===== */

		$query = "SELECT s.* FROM $db_structure AS s $sql_cond $sql_order $sql_limit";
		$res = Cot::$db->query($query);
		$jj = 1;

		while ($row = $res->fetch()) {
			$t->assign(array(
				'PAGE_ROW_NUM'			=> $jj,
				'PAGE_ROW_ODDEVEN'	=> cot_build_oddeven($jj),
				'PAGE_ROW_RAW'			=> $row,
				// Is there a function for this?
				'PAGE_ROW_ID'				=> $row['structure_id'],
				'PAGE_ROW_AREA'			=> $row['structure_area'],
				'PAGE_ROW_CODE'			=> $row['structure_code'],
				'PAGE_ROW_PATH'			=> $row['structure_path'],
				'PAGE_ROW_TPL'			=> $row['structure_tpl'],
				'PAGE_ROW_TITLE'		=> $row['structure_title'],
				'PAGE_ROW_DESC'			=> $row['structure_desc'],
				'PAGE_ROW_ICON'			=> $row['structure_icon'],
				'PAGE_ROW_COUNT'		=> $row['structure_count']
			));
			// Build extrafields
			if (isset(Cot::$extrafields[Cot::$db->structure])) {
				foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
					$uname = strtoupper($exfld['field_name']);
					$exfld_title = cot_extrafield_title($exfld, 'structure_');
					$cat = &$structure[$row['structure_area']][$row['structure_code']];
					$t->assign(array(
						'PAGE_ROW_'.$uname.'_TITLE' => $exfld_title,
						'PAGE_ROW_'.$uname => cot_build_extrafields_data('structure', $exfld, $cat[$exfld['field_name']]),
						'PAGE_ROW_'.$uname.'_VALUE' => $cat[$exfld['field_name']],
					));
				}
			}

			/* === Hook === */
			foreach (cot_getextplugins('catlist.loop') as $pl) {
				include $pl;
			}
			/* ===== */

			$t->parse("MAIN.PAGE_ROW");
			$jj++;
		}

		// Render pagination if needed
		if ($enablePagination) {
			$totalitems = Cot::$db->query("SELECT s.* FROM $db_structure AS s $sql_cond")->rowCount();

      $url_area = defined('COT_PLUG') ? 'plug' : Cot::$env['ext'];
			if (defined('COT_LIST')) {
				global $list_url_path;
				$url_params = $list_url_path;
			}
			elseif (defined('COT_PAGES')) {
				global $al, $id, $pag;
				$url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
			}
			elseif(defined('COT_USERS')) {
				global $m;
				$url_params = empty($m) ? array() :  array('m' => $m);
			}
			elseif (defined('COT_ADMIN')) {
				$url_area = 'admin';
				global $m, $p, $a;
				$url_params = array('m' => $m, 'p' => $p, 'a' => $a);
			}
			else
				$url_params = array();
			$url_params[$pagination] = $durl;

			if ($enableAjax) {
				$ajax_mode = true;
				$ajax_plug = 'plug';
				if (Cot::$cfg['plugin']['catlist']['encrypt_ajax_urls'])
					$ajax_plug_params = "r=catlist&h=$h";
				else
					$ajax_plug_params = "r=catlist&tpl=$tpl&items=$items&order=$order&extra=$extra&offset=$offset&pagination=$pagination&ajax_block=$ajax_block&cache_name=$cache_name&cache_ttl=$cache_ttl";
			}
			else {
				$ajax_mode = false;
				$ajax_plug = $ajax_plug_params = '';
			}

			$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination, '', $ajax_mode, $ajax_block, $ajax_plug, $ajax_plug_params);

		  // Assign pagination tags
		  $t->assign(array(
		    'PAGE_TOP_PAGINATION'  => $pagenav['main'],
		    'PAGE_TOP_PAGEPREV'    => $pagenav['prev'],
		    'PAGE_TOP_PAGENEXT'    => $pagenav['next'],
		    'PAGE_TOP_FIRST'       => $pagenav['first'],
		    'PAGE_TOP_LAST'        => $pagenav['last'],
		    'PAGE_TOP_CURRENTPAGE' => $pagenav['current'],
		    'PAGE_TOP_TOTALLINES'  => $totalitems,
		    'PAGE_TOP_MAXPERPAGE'  => $items,
		    'PAGE_TOP_TOTALPAGES'  => $pagenav['total']
		  ));
		}

		// Assign service tags
    if ((!$enableCache) && (Cot::$usr['maingrp'] == 5)) {
      $t->assign(array(
        'PAGE_TOP_QUERY' => $query,
        'PAGE_TOP_RES' => $res,
      ));
    }

		($jj==1) && $t->parse("MAIN.NONE");

		/* === Hook === */
		foreach (cot_getextplugins('catlist.tags') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse();
		$output = $t->text();

		if (Cot::$cache && !empty($cache_name) && !empty($cache_ttl) && ($cache_ttl > 0))
		Cot::$cache->db->store($cache_name, $output, SEDBY_CATLIST_REALM, $cache_ttl);
	}

	return $output;
}

/**
* Counts structure categories & caches result
*/
function cot_catcount($condition = '', $lang = '', $cache_name = '', $cache_ttl = '') {

	$cache_name = (!empty($cache_name)) ? str_replace(' ', '_', $cache_name) : '';

	if (Cot::$cache && !empty($cache_name) && Cot::$cache->db->exists($cache_name, SEDBY_CATLIST_REALM))
		$output = Cot::$cache->db->get($cache_name, SEDBY_CATLIST_REALM);
	else {

		global $Ls;
		$db_structure = Cot::$db->structure;

		$sql_cond	= empty($extra) ? "" : "WHERE $extra";
		$query = "SELECT COUNT(*) FROM $db_structure $where_condition";
		$output = Cot::$db->query($query)->fetchColumn();

		$output = (empty($lang)) ? $output : cot_declension($output, $Ls[$lang]);

		if (Cot::$cache && !empty($cache_name) && !empty($cache_ttl) && ($cache_ttl > 0))
		Cot::$cache->db->store($cache_name, $output, SEDBY_CATLIST_REALM, $cache_ttl);
	}
	return ($output);
}