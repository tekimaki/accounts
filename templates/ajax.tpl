{strip}
{if $req == 'get_available_projects'}
	{if !empty($error)}
		<div class="error" id="project_id">{$error}</div>
	{else}
		{html_options id="project_id" options=$project_id_options name="subproject[project_id]" }
	{/if}
{/if}
{/strip}