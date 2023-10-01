<?php
/* ====================
[BEGIN_COT_EXT]
Code=catlist
Name=[SEDBY] CatList
Category=navigation-structure
Description=Generates custom category lists available via {PHP|cot_catlist} callback
Version=2.00b
Date=2023-09-06
Author=Dmitri Beliavski
Copyright=&copy; 2021 Seditio.By
Notes=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=
Requires_plugins=cotlib
Recommends_modules=
Recommends_plugins=
[END_COT_EXT]
[BEGIN_COT_EXT_CONFIG]

useajax=00:separator:::
ajax=01:radio::0:Use AJAX
encrypt_ajax_urls=02:radio::0:Encrypt ajax URLs
encrypt_key=03:string::1234567890123456:Secret Key
encrypt_iv=04:string::1234567890123456:Initialization Vector

[END_COT_EXT_CONFIG]
==================== */

/**
* Catlist Plugin / Setup
*
* @package catlist
* @author Dmitri Beliavski
* @copyright (c) 2021-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');
