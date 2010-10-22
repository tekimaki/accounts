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

<div class="listing accounts subproject">
	<div class="header">
		<h1>{tr}{$gContent->getContentTypeName(TRUE)}{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<div class="navbar">
				<ul>
					<li><strong>{tr}Sort list by:{/tr} </strong></li>

					{if $gBitSystem->isFeatureActive( 'accounts_list_subproject_id' ) eq 'y'}
						<li>{smartlink ititle="Subproject Id" isort=subproject_id offset=$control.offset iorder=desc idefault=1}</li>
					{/if}


					{if $gBitSystem->isFeatureActive('subproject_list_title') eq 'y'}
						<li>{smartlink ititle="Sub-Project Name" isort=title offset=$control.offset}</li>
					{/if}

	 		     	{if $gBitSystem->isFeatureActive('subproject_list_data' ) eq 'y'}
						<li>{smartlink ititle="Description" isort=data offset=$control.offset}</li>
					{/if}
	 		     	{if $gBitSystem->isFeatureActive('subproject_list_account_id' ) eq 'y'}
						<li>{smartlink ititle="Account Name" isort=account_id offset=$control.offset}</li>
					{/if}
	 		     	{if $gBitSystem->isFeatureActive('subproject_list_project_id' ) eq 'y'}
						<li>{smartlink ititle="Project Name" isort=project_id offset=$control.offset}</li>
					{/if}


					{if $gBitSystem->isFeatureActive( 'subproject_list_summary' ) eq 'y'}
						<li>{smartlink ititle="Text" isort=data offset=$control.offset}</li>
					{/if}
				</ul>
			</div>

			<ul class="data clear">
				{foreach item=dataItem from=$subprojectList name=subprojectlist}
					<li class="{if $smarty.foreach.subprojectlist.last}last {/if}{cycle values="even,odd"}">
						<div class="floaticon">
							{if $gBitUser->hasPermission( 'p_accounts_subproject_update' )}
								{smartlink ititle="Edit" ifile="edit_subproject.php" ibiticon="icons/accessories-text-editor" subproject_id=$dataItem.subproject_id}
							{/if}
							{if $gBitUser->hasPermission( 'p_accounts_subproject_expunge' )}
								<input type="checkbox" name="checked[]" title="{$dataItem.title|escape}" value="{$dataItem.subproject_id}" />
							{/if}
						</div>


						{if $gBitSystem->isFeatureActive( 'subproject_list_title' )}
							<h2><a href="{$smarty.const.ACCOUNTS_PKG_URL}index.php?subproject_id={$dataItem.subproject_id|escape:"url"}" title="{$dataItem.subproject_id}">{$dataItem.title|escape}</a></h2>
						{/if}


						{if $gBitSystem->isFeatureActive( 'subproject_list_data' )}
							<div class="body">{$dataItem.parsed_data}</div>
						{/if}

<ul>

						{if $gBitSystem->isFeatureActive( 'accounts_list_subproject_id' )}
							<li><label>Subproject_id:</label>&nbsp;<a href="{$smarty.const.ACCOUNTS_PKG_URL}index.php?subproject_id={$dataItem.subproject_id|escape:"url"}" title="{$dataItem.subproject_id}">{$dataItem.subproject_id}</a></li>
						{/if}

	 		     	    {if $gBitSystem->isFeatureActive('subproject_list_title' ) eq 'y'}
							<li><label>Sub-Project Name:</label>&nbsp;{$dataItem.title|escape}</li>
						{/if}
	 		     	    {if $gBitSystem->isFeatureActive('subproject_list_account_id' ) eq 'y'}
							<li><label>Account Name:</label>&nbsp;{$dataItem.account_id|escape}</li>
						{/if}
	 		     	    {if $gBitSystem->isFeatureActive('subproject_list_project_id' ) eq 'y'}
							<li><label>Project Name:</label>&nbsp;{$dataItem.project_id|escape}</li>
						{/if}
</ul>


					</li>
				{foreachelse}
					<li class="norecords">{tr}No records found{/tr}</li>
				{/foreach}
			</ul>

			{if $gBitUser->hasPermission( 'p_accounts_subproject_expunge' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.BitBase.switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove_subproject_data">{tr}remove{/tr}</option>
					</select>

					<noscript><div><input class="button" type="submit" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .listing -->
{/strip}
