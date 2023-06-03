<input type="hidden" name="selected_fields[object]" value="department" />

<div class="table-wrapper">
    <table width="100%">
    <tr valign="top">
        <td>
            <label class="checkbox" for="elm_name">
                <input type="hidden" value="department" name="selected_fields[data][]" />
                <input 
                    type="checkbox" 
                    value="department" 
                    name="selected_fields[data][]" 
                    id="elm_department" 
                    checked="checked" 
                    disabled="disabled" 
                    class="cm-item-s" 
                />
                {__("name")}
            </label>
            <label class="checkbox" for="elm_supervisor">
                <input
                    type="checkbox" 
                    value="supervisor_id" 
                    name="selected_fields[data][]" 
                    id="elm_supervisor" 
                    checked="checked" 
                    class="cm-item-s" 
                />
                {__("sd_departments_supervisor")}
            </label>
            <label class="checkbox" for="elm_employees">
                <input
                    type="checkbox" 
                    value="employee_ids" 
                    name="selected_fields[data][]" 
                    id="elm_employees" 
                    checked="checked" 
                    class="cm-item-s" 
                />
                {__("sd_departments_employees")}
            </label>
        </td>
    </tr>
    </table>
</div>
<p>
{include "common/check_items.tpl" check_target="s" style="links"}
</p>