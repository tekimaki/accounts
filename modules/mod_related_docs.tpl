<div class="section-col3">
	{if $gContent->mInfo.relateddocs_related_docs}
		<legend>Related Documents</legend>
		<div>
		<ul>
		{foreach from=$gContent->mInfo.relateddocs_related_docs key=doc_content_id item=doc}
			<li><a href="{$smarty.const.LIBERTY_PKG_URL}download/file/{$doc.relateddocs_related_doc_id}">{$doc.relateddocs_related_title}</a></li>
		{/foreach}
		</ul>
		</div>
	{/if}
</div>
