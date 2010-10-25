BitSubProject = {
	onChangeAccountContentId:function( inputElm ){
		/* =-=- CUSTOM BEGIN: onChangeAccountContentId -=-= */
                account_content_id = inputElm.value;
                BitSubProject.updateProjectOptions(account_content_id);
		/* =-=- CUSTOM END: onChangeAccountContentId -=-= */
	}

	/* =-=- CUSTOM BEGIN: functions -=-= */
	, //continue list, comma not included by generator on last func

        updateProjectOptions:function(account_content_id){
                var row = BitBase.getElement('row_subproject_project_content_id');
                if( !isNaN(parseInt(account_content_id)) ){
                        $.ajax({
                                url:BitSystem.urls.accounts+'ajax.php',
                                type:'POST',
                                context:document.body,
                                data:{req:'get_available_projects',account_content_id:account_content_id},
                                success:function(dom){
                                        $('#project_content_id').replaceWith(dom);
                                        row.style.display = 'block';
                                }
                        });
                }else{
                        row.style.display = 'none';
                }
        },
	/* =-=- CUSTOM END: functions -=-= */
}