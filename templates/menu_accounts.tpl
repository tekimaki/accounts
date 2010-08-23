{strip}
{*
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 * Copyright (c) 2010 Will James will@tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details *
 * $Id: $
 * @package accounts
 * @subpackage templates
 *}
	<ul>
		{if $gBitUser->hasPermission( 'p_accounts_view')}
			<li><a class="item" href="{$smarty.const.ACCOUNTS_PKG_URL}index.php">{tr}Accounts Home{/tr}</a></li>


			{if $gBitUser->hasPermission( 'p_account_view')}
				<li><a class="item" href="{$smarty.const.ACCOUNTS_PKG_URL}list_account.php">{tr}List Accounts{/tr}</a></li>
			{/if}


		{/if}


		{if $gBitUser->hasPermission( 'p_account_create')}
		<li><a class="item" href="{$smarty.const.ACCOUNTS_PKG_URL}edit_account.php">{tr}Create Account{/tr}</a></li>
		{/if}


	</ul>
{/strip}