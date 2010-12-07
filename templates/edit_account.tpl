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

<div class="edit accounts account">
	<div class="header">
		<h1>
			{if $gContent->mInfo.account_id}
				{tr}Edit {$gContent->mInfo.title|escape}{/tr}
			{else}
				{tr}Create New {$gContent->getContentTypeName()}{/tr}
			{/if}
		</h1>
	</div>
	{formfeedback success=$success}
	<div class="body">
		{formfeedback warning=$errors}
		{form enctype="multipart/form-data" id="editaccountform"}
			{* =-=- CUSTOM BEGIN: input -=-= *}

			{* =-=- CUSTOM END: input -=-= *}
			<input type="hidden" name="content_id" value="{$gContent->mContentId}" />
			<div class="servicetabs">
			{jstabs id="servicetabs"}
				{* =-=- CUSTOM BEGIN: servicetabs -=-= *}

				{* =-=- CUSTOM END: servicetabs -=-= *}
				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl" display_help_tab=1 formid="editaccountform"}
			{/jstabs}
			</div>
			<div class="editcontainer">
			{jstabs}
				{if $preview eq 'y'}
					{jstab title="Preview"}
						{legend legend="Preview"}
						<div class="preview">
							{include file="bitpackage:accounts/display_account.tpl" page=`$gContent->mInfo.account_id`}
						</div>
						{/legend}
					{/jstab}
				{/if}
				{jstab title="Edit"}
				{legend legend=$gContent->getContentTypeName() class="inlineLabels"}
						<input type="hidden" name="account[account_id]" value="{$gContent->mInfo.account_id}" />
						{formfeedback warning=$errors.store}

						<div class="row" id="row_title">
							{formfeedback warning=$errors.title}
							{formlabel label="Account Name" for="title"}
							{forminput}
								<input class="textInput" type="text" size="50" name="account[title]" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>
 
						{textarea label="About" name="account[edit]" help="A statement about the account."}{$gContent->mInfo.data}{/textarea}
						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl" formid="editaccountform"}


						{if $gContent->hasUserPermission('p_liberty_attach_attachments') }
							<div class="row">
							{legend legend="Attachments"}
								{include file="bitpackage:liberty/edit_storage.tpl"}
							{/legend}
							</div>
						{/if}

						<div class="buttonHolder row submit">
							<input class="button" type="submit" name="preview" value="{tr}Preview{/tr}" />
							<input class="button" type="submit" name="save_account" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}
			{/jstabs}
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end . -->

{/strip}