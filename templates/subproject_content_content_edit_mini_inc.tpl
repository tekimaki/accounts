{strip}
{if $gContent->hasService($smarty.const.LIBERTY_SERVICE_SUBPROJECT_CONTENT)} 
	{if $connect_subproject_content_id}
		<input type="hidden" name="connection_subproject_content_id" value="{$connect_subproject_content_id}" />
	{/if}

	{*
	{if $gContent->isValid() && $gBitUser->hasPermission('p_subproject_content_update') ||
		$gBitUser->hasPermission('p_subproject_content_view')}
	{legend legend=$serviceName.label|default:$serviceName}
		<div class="row" id="row_subproject_content_subproject_content_id" style="">
	{formfeedback warning=$errors.subproject_content_id}
	{formlabel label="Sub Projects" for="subproject_content_id" required="y"}
	{forminput}

            <select name="subproject_content_data[][subproject_content_id]" id="subproject_content_id" onchange="SubProjectContent.onChangeSubprojectContentId(this);"  multiple="multiple" >
         {foreach from=$subproject_content_id_options key=itemKey item=itemValue}
             {assign var=selected value=false}
             {foreach from=$gContent->mInfo.subproject_content item=fieldValues key=keyName name=fields}
		{if $itemKey == $fieldValues.subproject_content_id}{assign var=selected  value=true}{/if}
             {/foreach}
             <option value="{$itemKey}" {if $selected}selected='selected'{/if}>
               {$itemValue|escape:html}
             </option>
         {/foreach}
         </select>

            
	{formhelp note="Select the sub-projects this content belongs to"}
	{/forminput}
		</div>
        {/legend}
        {/if}
	*}
{/if}
{/strip}
