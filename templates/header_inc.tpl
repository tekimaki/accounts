{strip}
{* Themes plugin stuff *}
{if $gAccount && $gAccount->isValid()}
<style type="text/css">
{if !empty($gAccount->mInfo.primary_color)}
#header, #footer {ldelim}
	background-color: #{$gAccount->mInfo.primary_color};
{rdelim}
{/if}
{if !empty($gAccount->mInfo.headline_color)}
h1, h2, h3, h4, h5 {ldelim}
	color: #{$gAccount->mInfo.headline_color};
{rdelim}
{/if}
{if !empty($gAccount->mInfo.link_color)}
a:link, a:active, a:visited {ldelim}
	color: #{$gAccount->mInfo.link_color};
{rdelim}
{/if}
{if !empty($gAccount->mInfo.text_color)}
p {ldelim}
	color: #{$gAccount->mInfo.text_color};
{rdelim}
{/if}
</style>
{/if}
{/strip}