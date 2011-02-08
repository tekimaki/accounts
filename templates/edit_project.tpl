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

<div class="edit accounts project">
	<div class="header">
		<h1>
			{if $gContent->mInfo.project_id}
				{tr}Edit {$gContent->mInfo.title|escape}{/tr}
			{else}
				{tr}Create New {$gContent->getContentTypeName()}{/tr}
			{/if}
		</h1>
	</div>

	<div class="body">
		{formfeedback success=$success}
		{formfeedback error=$errors.store}
		{form enctype="multipart/form-data" id="editprojectform"}
			{* =-=- CUSTOM BEGIN: input -=-= *}

			{* =-=- CUSTOM END: input -=-= *}
			<input type="hidden" name="content_id" value="{$gContent->mContentId}" />
			<div class="servicetabs">
			{jstabs id="servicetabs"}
				{* =-=- CUSTOM BEGIN: servicetabs -=-= *}

				{* =-=- CUSTOM END: servicetabs -=-= *}
				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl" display_help_tab=1 formid="editprojectform"}
			{/jstabs}
			</div>
			<div class="editcontainer">
			{jstabs}
				{if $preview eq 'y'}
					{jstab title="Preview"}
						{legend legend="Preview"}
						<div class="preview">
							{include file="bitpackage:accounts/display_project.tpl" page=`$gContent->mInfo.project_id`}
						</div>
						{/legend}
					{/jstab}
				{/if}
				{jstab title="Edit"}
				{legend legend=$gContent->getContentTypeName() class="inlineLabels"}
						<input type="hidden" name="project[project_id]" value="{$gContent->mInfo.project_id}" />

						<div class="row" id="row_title">
							{formlabel label="Project Name" for="title" required="y"}
							{forminput}
								{formfeedback error=$errors.title}
								<input class="textInput" type="text" size="50" name="project[title]" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>
						<div class="row" id="row_project_account_content_id" style="">
							
	{formlabel label="Account Name" for="account_content_id" required="y"}
	{forminput}
		{formfeedback error=$errors.account_content_id}

        			{html_options id="account_content_id" options=$account_content_id_options name="project[account_content_id]" selected=$gContent->getField('account_content_id')  }
    
	{formhelp note=""}
	{/forminput}
						</div>
 
{textarea label="Description" name="project[edit]" help="A description of the project" error=$errors.data }{$gContent->mInfo.data}{/textarea}
						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl" formid="editprojectform"}


						{if $gContent->hasUserPermission('p_liberty_attach_attachments') }
							<div class="row">
							{legend legend="Attachments"}
								{include file="bitpackage:liberty/edit_storage.tpl"}
							{/legend}
							</div>
						{/if}

						<div class="buttonHolder row submit">
							<input class="button" type="submit" name="preview" value="{tr}Preview{/tr}" />
							<input class="button" type="submit" name="save_project" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}
			{/jstabs}
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end . -->

{/strip}