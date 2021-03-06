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

<div class="listing accounts project">
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

					{if $gBitSystem->isFeatureActive( 'accounts_list_project_id' ) eq 'y'}
						<li>{smartlink ititle="Project Id" isort=project_id offset=$control.offset iorder=desc idefault=1}</li>
					{/if}


					{if $gBitSystem->isFeatureActive('project_list_title') eq 'y'}
						<li>{smartlink ititle="Project Name" isort=title offset=$control.offset}</li>
					{/if}

	 		     	{if $gBitSystem->isFeatureActive('project_list_data' ) eq 'y'}
						<li>{smartlink ititle="Description" isort=data offset=$control.offset}</li>
					{/if}
	 		     	{if $gBitSystem->isFeatureActive('project_list_is_default' ) eq 'y'}
						<li>{smartlink ititle="Is Default" isort=is_default offset=$control.offset}</li>
					{/if}
	 		     	{if $gBitSystem->isFeatureActive('project_list_account_content_id' ) eq 'y'}
						<li>{smartlink ititle="Account Name" isort=account_content_id offset=$control.offset}</li>
					{/if}


					{if $gBitSystem->isFeatureActive( 'project_list_summary' ) eq 'y'}
						<li>{smartlink ititle="Text" isort=data offset=$control.offset}</li>
					{/if}
				</ul>
			</div>

			<ul class="data clear">
				{foreach item=dataItem from=$projectList name=projectlist}
					<li class="{if $smarty.foreach.projectlist.last}last {/if}{cycle values="even,odd"}">
						<div class="floaticon">
							{if $gBitUser->hasPermission( 'p_project_update' )}
								{smartlink ititle="Edit" ifile="edit_project.php" ibiticon="icons/accessories-text-editor" project_id=$dataItem.project_id}
							{/if}
							{if $gBitUser->hasPermission( 'p_project_expunge' )}
								<input type="checkbox" name="checked[]" title="{$dataItem.title|escape}" value="{$dataItem.project_id}" />
							{/if}
						</div>


						{if $gBitSystem->isFeatureActive( 'project_list_title' )}
							<h2><a href="{$smarty.const.ACCOUNTS_PKG_URL}index.php?project_id={$dataItem.project_id|escape:"url"}" title="{$dataItem.project_id}">{$dataItem.title|escape}</a></h2>
						{/if}


						{if $gBitSystem->isFeatureActive( 'project_list_data' )}
							<div class="body">{$dataItem.parsed_data}</div>
						{/if}

<ul>

						{if $gBitSystem->isFeatureActive( 'accounts_list_project_id' )}
							<li><label>Project_id:</label>&nbsp;<a href="{$smarty.const.ACCOUNTS_PKG_URL}index.php?project_id={$dataItem.project_id|escape:"url"}" title="{$dataItem.project_id}">{$dataItem.project_id}</a></li>
						{/if}

	 		     	    {if $gBitSystem->isFeatureActive('project_list_title' ) eq 'y'}
							<li><label>Project Name:</label>&nbsp;{$dataItem.title|escape}</li>
						{/if}
	 		     	    {if $gBitSystem->isFeatureActive('project_list_is_default' ) eq 'y'}
							<li><label>Is Default:</label>&nbsp;{$dataItem.is_default|escape}</li>
						{/if}
	 		     	    {if $gBitSystem->isFeatureActive('project_list_account_content_id' ) eq 'y'}
							<li><label>Account Name:</label>&nbsp;{$dataItem.account_content_id|escape}</li>
						{/if}
</ul>


					</li>
				{foreachelse}
					<li class="norecords">{tr}No records found{/tr}</li>
				{/foreach}
			</ul>

			{if $gBitUser->hasPermission( 'p_project_expunge' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove_project_data">{tr}remove{/tr}</option>
					</select>

					<noscript><div><input class="button" type="submit" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .listing -->
{/strip}
