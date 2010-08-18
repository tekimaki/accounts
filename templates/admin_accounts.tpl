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
	{jstabs}
{* Are there homeable settings? *}


	{jstab title="Account Settings"}
		{jstabs}



			{jstab title="Account List Settings"}
				{legend legend="Account List Settings"}
					<input type="hidden" name="page" value="{$page}" />
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

	{include file="bitpackage:liberty/admin_services_inc.tpl" ipackage=$smarty.const.ACCOUNT_PKG_NAME}
	{/jstabs}
{/form}
{/strip}
