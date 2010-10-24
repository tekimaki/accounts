{strip}
{if $gContent->hasService($smarty.const.LIBERTY_SERVICE_SUBPROJECT_CONTENT)} 
	{if $gContent->isValid() && $gBitUser->hasPermission('p_subproject_content_update') ||
		$gBitUser->hasPermission('p_subproject_content_view')}
	{legend legend=$serviceName.label|default:$serviceName}
		<div class="row" id="row_subproject_content_subproject_id" style="">
	{formfeedback warning=$errors.subproject_id}
	{formlabel label="Sub Projects" for="subproject_id" required="y"}
	{forminput}

	        <select name="subproject_content_data[][subproject_id]" id="subproject_id" onchange="SubProjectContent.onChangeSubprojectId(this);"  multiple="multiple" >
         {foreach from=$subproject_id_options key=itemKey item=itemValue}
             {assign var=selected value=false}
             {foreach from=$gContent->mInfo.subproject_content item=fieldValues key=keyName name=fields}
		{if $itemKey == $fieldValues.subproject_id}{assign var=selected  value=true}{/if}
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
{/if}
{/strip}