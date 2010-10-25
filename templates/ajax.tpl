{strip}
{if $req == 'get_available_projects'}
	{if !empty($error)}
		<div class="error" id="project_content_id">{$error}</div>
	{else}
		{html_options id="project_content_id" options=$project_id_options name="subproject[project_content_id]" }
	{/if}
{/if}
{/strip}