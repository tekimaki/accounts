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
{form}
<input type="hidden" name="page" value="{$page}" />
	{jstabs}

	{* Are there homeable settings? *}
		{jstab title="Accounts Home Settings"}
			{legend legend="Accounts Home Settings"}
				<div class="row">
					{formlabel label="Accounts Home Format"}
					{forminput}
					{html_options name='accounts_home_format' options=$homeFormatOptions id="homeFormatAccounts" selected=$accounts_home_format}
					{/forminput}
				</div>		
				<div class="row">
					{formlabel label="Accounts Home Content Type"}
					{forminput}
						<select name="accounts_home_type" id="homeAccounts">
							{section name=ix loop=$homeTypes}
								<option value="{$homeTypes[ix]|escape}" {if $accounts_home_type == $homeTypes[ix]}selected="selected"{/if} >{$homeTypes[ix]}</option>
							{/section}
						</select>
					{/forminput}
				</div>		
{* Output for each type *}
				<div class="row">
					{formlabel label="Home Account Content Item" for="homeAccount"}
					{forminput}
						<select name="accounts_account_home_id" id="homeAccount">
							{section name=ix loop=$account_data}
								<option value="{$account_data[ix].account_id|escape}" {if $account_data[ix].account_id eq $accounts_account_home_id}selected="selected"{/if}>{$account_data[ix].title|escape|truncate:20:"...":true}</option>
							{sectionelse}
								<option>{tr}No records found{/tr}</option>
							{/section}
						</select>
						{formhelp note="This is the account that will be displayed when viewing the accounts homepage if Accounts Home Format above is set to 'Content Item' and Accounts Home Content Type above is set to account"}
					{/forminput}
				</div>
{* End foreach type *}
			{/legend}
			<div class="row submit">
				<input type="submit" name="accounts_settings" value="{tr}Change preferences{/tr}" />
			</div>
		{/jstab}
{* End homeable section *}

	
		{jstab title="Account Settings"}
	{jstabs}



			{jstab title="Account List Settings"}
				{legend legend="Account List Settings"}
					{foreach from=$formaccountLists key=item item=output}
						<div class="row">
							{formlabel label=`$output.label` for=$item}
							{forminput}
								{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
								{formhelp note=`$output.note` page=`$output.page`}
							{/forminput}
						</div>
					{/foreach}
				{/legend}
				<div class="row submit">
					<input type="submit" name="accounts_settings" value="{tr}Change preferences{/tr}" />
				</div>
			{/jstab}
{* End List Settings *}

		{/jstabs}
	{/jstab}

			{include file="bitpackage:liberty/service_package_admin_inc.tpl" package=$smarty.const.ACCOUNTS_PKG_NAME }
	
	{/jstabs}
{/form}
{/strip}