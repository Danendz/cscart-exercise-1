{capture name="mainbox"}
<script>
    (function($) {
        $(document).ready(function(){
            // Double scroll
            var elm_orig = $("#scrolled_div");
            var elm_scroller = $("#scrolled_div_top");

            var dummy = $("<div></div>");
            dummy.width(elm_orig.get(0).scrollWidth);
            dummy.height(24);
            elm_scroller.append(dummy);

            elm_scroller.scroll(function(){
                elm_orig.scrollLeft(elm_scroller.scrollLeft());
            });
            elm_orig.scroll(function(){
                elm_scroller.scrollLeft(elm_orig.scrollLeft());
            });
        });
    }(Tygh.$));
</script>

<form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="departments_m_update_form">
<input type="hidden" name="fake" value="1" />

<div class="table-wrapper">
    <table width="100%">
    <tr>
        <td width="100%">
            <div id="scrolled_div_top" class="scroll-x scroll-top"></div>
            <div id="scrolled_div" class="scroll-x scroll-border">
            <table width="100%" class="table-fixed table--relative">
            <tr>
                {foreach from=$field_names item="field_name" key=field_key}
                    <th>
                        {if $field_name|is_array}
                            {__($field_key)}
                        {else}
                            {$field_name}
                        {/if}
                    </th>
                {/foreach}
            </tr>
            {foreach from=$department_data item="department"}
            <tr>
                {foreach $field_names as $field => $v}
                <td valign="top" class="pad">
                    {if $field === "supervisor_id"}
                        {include 
                            "pickers/users/picker.tpl" 
                            but_text=__("sd_departments_add_supervisor") 
                            data_id="elm_department_supervisor" 
                            but_meta="btn" 
                            input_name="department_data[{$department.department_id}][{$field}]" 
                            item_ids=$department.supervisor_id
                            placement="right"
                            display="radio"
                            view_mode="single_button"
                            user_info=$department.supervisor_info
                        }
                    {elseif $field === 'employee_ids'}
                        {include 
                            "pickers/users/picker.tpl" 
                            but_text=__("sd_departments_add_employee") 
                            data_id="elm_department_employee"
                            but_meta="btn" 
                            input_name="department_data[{$department.department_id}][{$field}]" 
                            item_ids=$department.employee_ids
                            placement="right"
                        }
                    {elseif $field === 'department'}
                        <input
                            type="text" 
                            value="{$department.$field}" 
                            class="input-text" 
                            name="department_data[{$department.department_id}][{$field}]" 
                        />
                    {/if}
                </td>
                {/foreach}
            </tr>
            {/foreach}
            </table>
            </div>
        </td>
    </tr>
    </table>
</div>
{capture name="buttons"}
    {include 
        "buttons/save.tpl" 
        but_name="dispatch[profiles.m_update_departments]" 
        but_target_form="departments_m_update_form" 
        but_role="submit-link"
    }
{/capture}
</form>
{/capture}

    
{include 
    "common/mainbox.tpl" 
    title=__("sd_departments_update_departments") 
    content=$smarty.capture.mainbox 
    select_languages=true 
    buttons=$smarty.capture.buttons
}
