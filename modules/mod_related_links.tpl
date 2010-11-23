<div class="section-col3">
	{if $gContent->mInfo.relatedlinks_related_links}
		<legend>Related Links</legend>
		<div>
		<ul>
		{foreach from=$gContent->mInfo.relatedlinks_related_links key=link_content_id item=link}
			<li><a href="{$link.relatedlinks_related_link_url}">{$link.relatedlinks_related_link_title}</a></li>
		{/foreach}
		</ul>
		</div>
	{/if}
</div>
