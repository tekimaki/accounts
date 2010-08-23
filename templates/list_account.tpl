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
<div class="floaticon">{bithelp}</div>

<div class="listing accounts account">
	<div class="header">
		<h1>{tr}{$gContent->getContentTypeName(TRUE)}{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="data">
				<tr>
					{if $gBitSystem->isFeatureActive( 'accounts_list_account_id' ) eq 'y'}
						<th>{smartlink ititle="Account Id" isort=account_id offset=$control.offset iorder=desc idefault=1}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'account_list_title' ) eq 'y'}
						<th>{smartlink ititle="Title" isort=title offset=$control.offset}</th>
					{/if}


	 		     		{if $gBitSystem->isFeatureActive('account_list_title' ) eq 'y'}
						<th>{smartlink ititle="Account Name" isort=title offset=$control.offset}</th>
					{/if}
	 		     		{if $gBitSystem->isFeatureActive('account_list_data' ) eq 'y'}
						<th>{smartlink ititle="About" isort=data offset=$control.offset}</th>
					{/if}


					{if $gBitSystem->isFeatureActive( 'account_list_summary' ) eq 'y'}
						<th>{smartlink ititle="Text" isort=data offset=$control.offset}</th>
					{/if}

					<th>{tr}Actions{/tr}</th>
				</tr>

				{foreach item=dataItem from=$accountList}
					<tr class="{cycle values="even,odd"}">
						{if $gBitSystem->isFeatureActive( 'list_account_id' )}
							<td><a href="{$smarty.const.ACCOUNTS_PKG_URL}index.php?account_id={$dataItem.account_id|escape:"url"}" title="{$dataItem.account_id}">{$dataItem.account_id}</a></td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'account_list_title' )}
							<td><a href="{$smarty.const.ACCOUNTS_PKG_URL}index.php?account_id={$dataItem.account_id|escape:"url"}" title="{$dataItem.account_id}">{$dataItem.title|escape}</a></td>
						{/if}


	 		     	     		{if $gBitSystem->isFeatureActive('account_list_title' ) eq 'y'}
								<td>{$dataItem.title|escape}</td>
						{/if}
	 		     	     		{if $gBitSystem->isFeatureActive('account_list_data' ) eq 'y'}
								<td>{$dataItem.data|escape}</td>
						{/if}


						{if $gBitSystem->isFeatureActive( 'account_list_summary' )}
							<td>{$dataItem.summary|escape}</td>
						{/if}


						<td class="actionicon">
						{if $gBitUser->hasPermission( 'p_accounts_account_update' )}
							{smartlink ititle="Edit" ifile="edit_account.php" ibiticon="icons/accessories-text-editor" account_id=$dataItem.account_id}
						{/if}
						{if $gBitUser->hasPermission( 'p_accounts_account_expunge' )}
							<input type="checkbox" name="checked[]" title="{$dataItem.title|escape}" value="{$dataItem.account_id}" />
						{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">
						{tr}No records found{/tr}
					</td></tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission( 'p_accounts_account_expunge' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.BitBase.switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove_account_data">{tr}remove{/tr}</option>
					</select>

					<noscript><div><input type="submit" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .listing -->
{/strip}
