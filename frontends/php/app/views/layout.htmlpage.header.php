<?php
/*
** Zabbix
** Copyright (C) 2001-2019 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

global $DB, $ZBX_SERVER, $ZBX_SERVER_NAME, $ZBX_SERVER_PORT;

$page_title = $data['page']['title'];
if (isset($ZBX_SERVER_NAME) && $ZBX_SERVER_NAME !== '') {
	$page_title = $ZBX_SERVER_NAME.NAME_DELIMITER.$page_title;
}

$pageHeader = new CPageHeader($page_title);

$scripts = $data['javascript']['files'];

$theme = ZBX_DEFAULT_THEME;
if (!empty($DB['DB'])) {
	$config = select_config();
	$theme = getUserTheme($data['user']);

	$pageHeader->addStyle(getTriggerSeverityCss($config));
	$pageHeader->addStyle(getTriggerStatusCss($config));

	// perform Zabbix server check only for standard pages
	if ($config['server_check_interval'] && !empty($ZBX_SERVER) && !empty($ZBX_SERVER_PORT)) {
		$scripts[] = 'servercheck.js';
	}
}

// Show GUI messages in pages with menus and in fullscreen and kiosk mode.
$show_gui_messaging = (!defined('ZBX_PAGE_NO_MENU')
	|| in_array($data['web_layout_mode'], [ZBX_LAYOUT_FULLSCREEN, ZBX_LAYOUT_KIOSKMODE]))
		? intval(!CWebUser::isGuest())
		: null;

$pageHeader
	->addCssFile('assets/styles/'.CHtml::encode($theme).'.css')
	->addJsBeforeScripts(
		'var PHP_TZ_OFFSET = '.date('Z').','.
			'PHP_ZBX_FULL_DATE_TIME = "'.ZBX_FULL_DATE_TIME.'";'
	)
	->addJsFile((new CUrl('js/browsers.js'))->getUrl())
	->addJsFile((new CUrl('jsLoader.php'))
		->setArgument('lang', $data['user']['lang'])
		->setArgument('ver', ZABBIX_VERSION)
		->setArgument('showGuiMessaging', $show_gui_messaging)
		->getUrl()
	);

if ($scripts) {
	$pageHeader->addJsFile((new CUrl('jsLoader.php'))
		->setArgument('ver', ZABBIX_VERSION)
		->setArgument('lang', $data['user']['lang'])
		->setArgument('files', $scripts)
		->getUrl()
	);
}
$pageHeader->display();

echo '<body lang="'.CWebUser::getLang().'">';
echo '<output class="'.ZBX_STYLE_MSG_BAD_GLOBAL.'" id="msg-bad-global"></output>';
