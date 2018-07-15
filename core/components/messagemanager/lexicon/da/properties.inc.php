<?php
/**
 * da properties topic lexicon file for MessageManager extra
 * Danish translation by Anton Tarasov (Himurovich) - 07-14-2018
 *
 * Copyright 2014-2018 Bob Ray <https://bobsguides.com>
 * Created on 01-26-2015
 *
 * MessageManager is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MessageManager is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MessageManager; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package messagemanager
 */

/**
 * Description
 * -----------
 * da properties topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package messagemanager
 **/


/* Used in properties.mmajax.snippet.php */

$_lang['mm_exclude_groups_desc'] = 'Kommasepareret liste over brugergruppe-id\'er eller navne, der ikke kan sendes bulkbeskeder; hvis tom alle brugergrupper kan sendes meddelelser; standard: tomt';

$_lang['mm_user_group_desc'] = 'Brugergruppe-id eller navn, der har valgt at modtage meddelelser; Hvis det er tomt, kan alle brugere sendes en besked; standard: tomt.';

/* Used in properties.messagemanager.snippet.php */
$_lang['cssFile_desc'] = 'CSS-fil til brug for Message Manager; standard: messagemanager.css';
$_lang['jsChunk_desc'] = 'Chunk indeholder Message Manager JS; standard: mmAjaxJS';
$_lang['language_desc'] = 'Sprog til brug i Message Manager; standard: en';
$_lang['outerTpl_desc'] = 'Ydre Tpl chunk til Message Manager;standard: MessageOuterTpl';
$_lang['tpl_desc'] = 'Tpl chunk for individuelle meddelelser; standard: MessageTpl';
$_lang['redirect_to_desc'] = 'ID for ressource for at omdirigere til, om brugeren ikke er logget ind (fx loginsiden); Hvis dette ikke er angivet, vil MessageManager omdirigere til login-siden, hvis dens pagetitle er logget ind, eller siden site_start hvis ikke.';
$_lang['allowed_groups_desc'] = 'Kommasepareret liste over brugergruppens navne eller id\'er, der har adgang til MessageManager; hvis tom er alle grupper tilladt.';
$_lang['mm_recipient_options_desc'] = 'Valg til afsendelse af meddelelser; Disse valgmuligheder vises i rullelisten, når du sender en ny besked; du skal inkludere mindst en mulighed teksterne til de tre muligheder er leksikonstrenger i standard.inc.php filen; standard: user,usergroup,all.';