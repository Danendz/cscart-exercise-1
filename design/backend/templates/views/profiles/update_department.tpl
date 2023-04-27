{if $department_data}
    {assign var="id" value=$department_data.department_id}
{else}
    {assign var="id" value=0}
{/if}

{capture name="mainbox"}

    <form action="{""|fn_url}" method="post" class="form-horizontal form-edit" name="departments_form"
        enctype="multipart/form-data">
        <input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
        <input type="hidden" class="cm-no-hide-input" name="department_id" value="{$id}" />

        <div id="content_general">
            <div class="control-group">
                <label for="elm_department_name" class="control-label cm-required">{__("name")}</label>
                <div class="controls">
                    <input type="text" name="department_data[department]" id="elm_department_name"
                        value="{$department_data.department}" size="25" class="input-large" />
                </div>
            </div>

            <div class="control-group">
            <label class="control-label">{__("image")}</label>
            <div class="controls">
                {include file="common/attach_images.tpl"
                    image_name="department"
                    image_object_type="promo"
                    image_pair=$department_data.main_pair
                    image_object_id=$id
                    no_detailed=true
                    hide_titles=true
                }
            </div>
        </div>

        <div class="control-group" id="department_text">
            <label class="control-label" for="elm_department_description">{__("description")}:</label>
            <div class="controls">
                <textarea id="elm_department_description" name="department_data[description]" cols="35" rows="8"
                    class="cm-wysiwyg input-large">{$department_data.description}</textarea>
            </div>
        </div>

        {if $department_data}
        <div class="control-group">
            <label class="control-label">{__("creation_date")}:</label>
            <div class="controls">
                <p>
                    {$department.timestamp|date_format:"`$settings.Appearance.date_format`"}
                </p>
            </div>
        </div>
        {/if}
		<div class="control-group">
	        <label for="elm_department_supervisor" class="control-label cm-required">{__("supervisor")}</label>
	        <div class="controls">
	            {include 
                file="pickers/users/picker.tpl" 
                but_text=__("add_department_supervisor") 
                data_id="elm_department_supervisor" 
                but_meta="btn" 
                input_name="department_data[supervisor_id]" 
                item_ids=$department_data.supervisor_id
                placement="right"
                display="radio"
                view_mode="single_button"
                user_info=$supervisor_info
                }

	        </div>
		</div>
		<div class="control-group">
	        <label for="elm_department_employee" class="control-label">{__("employees")}</label>
	        <div class="controls">
	            {include 
                file="pickers/users/picker.tpl" 
                but_text=__("add_department_employee") 
                data_id="elm_department_employee"
                but_meta="btn" 
                input_name="department_data[employee_ids]" 
                item_ids=$department_data.employee_ids
                placement="right"
                }

	        </div>
		</div>
        
        {include file="common/select_status.tpl" input_name="department_data[status]" id="elm_department_status" obj_id=$id obj=$department_data hidden=false}
        <!--content_general-->
    </div>

    {if $id} 
    {/if}

    {capture name="buttons"}
	    {if !$id}
		    {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="departments_form" but_name="dispatch[profiles.update_department]"}
	    {else}
		    {capture name="tools_list"}
			    <li>
			        {btn type="list" text=__("delete") class="cm-confirm" href="profiles.delete_department?department_id=`$id`" method="POST"}
			    </li>
		    {/capture}
	        {dropdown content=$smarty.capture.tools_list}
		    {include file="buttons/save_cancel.tpl" but_name="dispatch[profiles.update_department]" but_role="submit-link" but_target_form="departments_form" hide_first_button=$hide_first_button hide_second_button=$hide_second_button save=$id}
	    {/if}
    {/capture}

</form>

{/capture}

{include file="common/mainbox.tpl"
    title=($id) ? "Изменить: {$department_data.department}" : __("new_department") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}

{** department section **}