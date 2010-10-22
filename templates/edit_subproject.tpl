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

<div class="edit accounts subproject">
	<div class="header">
		<h1>
			{if $gContent->mInfo.subproject_id}
				{tr}Edit {$gContent->mInfo.title|escape}{/tr}
			{else}
				{tr}Create New {$gContent->getContentTypeName()}{/tr}
			{/if}
		</h1>
	</div>

	<div class="body">
		{formfeedback warning=$errors}
		{form enctype="multipart/form-data" id="editsubprojectform"}
			{* =-=- CUSTOM BEGIN: input -=-= *}

			{* =-=- CUSTOM END: input -=-= *}
			<input type="hidden" name="content_id" value="{$gContent->mContentId}" />
			<div class="servicetabs">
			{jstabs id="servicetabs"}
				{* =-=- CUSTOM BEGIN: servicetabs -=-= *}

				{* =-=- CUSTOM END: servicetabs -=-= *}
				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl" display_help_tab=1}
			{/jstabs}
			</div>
			<div class="editcontainer">
			{jstabs}
				{if $preview eq 'y'}
					{jstab title="Preview"}
						{legend legend="Preview"}
						<div class="preview">
							{include file="bitpackage:accounts/display_subproject.tpl" page=`$gContent->mInfo.subproject_id`}
						</div>
						{/legend}
					{/jstab}
				{/if}
				{jstab title="Edit"}
				{legend legend=$gContent->getContentTypeName() class="inlineLabels"}
						<input type="hidden" name="subproject[subproject_id]" value="{$gContent->mInfo.subproject_id}" />
						{formfeedback warning=$errors.store}

						<div class="row" id="row_title">
							{formfeedback warning=$errors.title}
							{formlabel label="Sub-Project Name" for="title"}
							{forminput}
								<input class="textInput" type="text" size="50" name="subproject[title]" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>
						<div class="row" id="row_subproject_account_id" style="">
								{formfeedback warning=$errors.account_id}
	{formlabel label="Account Name" for="account_id" required="y"}
	{forminput}

	    	{html_options id="account_id" options=$account_id_options name="subproject[account_id]" selected=$gContent->getField('account_id') onchange="BitSubProject.onChangeAccountId(this);"  }
    
	{formhelp note=""}
	{/forminput}
						</div>
						<div class="row" id="row_subproject_project_id" style="">
								{formfeedback warning=$errors.project_id}
	{formlabel label="Project Name" for="project_id" required="y"}
	{forminput}

	    	{html_options id="project_id" options=$project_id_options name="subproject[project_id]" selected=$gContent->getField('project_id')  }
    
	{formhelp note=""}
	{/forminput}
						</div>
 
						{textarea label="Description" name="subproject[edit]" help="A description of the sub-project"}{$gContent->mInfo.data}{/textarea}
						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}


						{if $gBitUser->hasPermission('p_liberty_attach_attachments') }
							<div class="row">
							{legend legend="Attachments"}
								{include file="bitpackage:liberty/edit_storage.tpl"}
							{/legend}
							</div>
						{/if}


						<div class="buttonHolder row submit">
							<input class="button" type="submit" name="preview" value="{tr}Preview{/tr}" />
							<input class="button" type="submit" name="save_subproject" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}
			{/jstabs}
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end . -->

{/strip}