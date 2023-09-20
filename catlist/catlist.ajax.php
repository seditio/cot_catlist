<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
* Catlist Plugin / AJAX
*
* @package catlist
* @author Dmitri Beliavski
* @copyright (c) 2021-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

/* === Hook === */
foreach (array_merge(cot_getextplugins('catlist.ajax.first')) as $pl) {
  include $pl;
}
/* ===== */

if (Cot::$cfg['plugin']['catlist']['encrypt_ajax_urls'] == 1) {
  $params = cot_import('h', 'G', 'TXT');
  $params = sedby_encrypt_decrypt('decrypt', $params, Cot::$cfg['plugin']['catlist']['encrypt_key'], Cot::$cfg['plugin']['catlist']['encrypt_iv']);
  $params = explode(',', $params);

  $tpl = $params[0];
  $items = $params[1];
  $order = $params[2];
  $extra = $params[3];
  $offset = $params[4];
  $pagination = $params[5];
  $ajax_block = $params[6];
  $cache_name = $params[7];
  $cache_ttl = $params[8];
}
else {
  $tpl = cot_import('tpl','G','TXT');
  $items = cot_import('items','G','INT');
  $order = cot_import('order','G','TXT');
  $extra = cot_import('extra','G','TXT');
  $offset = cot_import('offset','G','INT');
  $pagination = cot_import('pagination','G','TXT');
  $ajax_block = cot_import('ajax_block','G','TXT');
  $cache_name = cot_import('cache_name','G','TXT');
  $cache_ttl = cot_import('cache_ttl','G','INT');
}

ob_clean();
echo sedby_catlist($tpl, $items, $order, $extra, $offset, $pagination, $ajax_block, $cache_name, $cache_ttl);
ob_flush();
exit;
