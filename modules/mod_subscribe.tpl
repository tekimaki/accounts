<div id="side">
	{* volunteer subscribe box *}
	<div class="box"><div class="cos-form">
		{include file="`$smarty.const.CONFIG_PKG_PATH`accounts/plugins/subscribe/templates/subscribe_form_inc.tpl" formType="vol" success=$vol_success errors=$vol_errors request=$vol_request}
	</div></div>
	{* organization subscribe box *}
	<div class="box"><div class="cos-form">
		{include file="`$smarty.const.CONFIG_PKG_PATH`accounts/plugins/subscribe/templates/subscribe_form_inc.tpl" formType="org" success=$org_success errors=$org_errors request=$org_request}
	</div></div>
</div>
