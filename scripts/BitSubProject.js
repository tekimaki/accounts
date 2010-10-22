BitSubProject = {
	onChangeAccountId:function( inputElm ){
		/* =-=- CUSTOM BEGIN: onChangeAccountId -=-= */
		account_id = inputElm.value;
		BitSubProject.updateProjectOptions(account_id)
		/* =-=- CUSTOM END: onChangeAccountId -=-= */
	}

	/* =-=- CUSTOM BEGIN: functions -=-= */
	, //continue list, comma not included by generator on last func

        updateProjectOptions:function(account_id){
                var row = BitBase.getElement('row_subproject_project_id');
                if( !isNaN(parseInt(account_id)) ){
                        $.ajax({
                                url:BitSystem.urls.accounts+'ajax.php',
                                type:'POST',
                                context:document.body,
                                data:{req:'get_available_projects',account_id:account_id},
                                success:function(dom){
                                        $('#project_id').replaceWith(dom);
                                        row.style.display = 'block';
                                }
                        });
                }else{
                        row.style.display = 'none';
                }
        },
	/* =-=- CUSTOM END: functions -=-= */
}