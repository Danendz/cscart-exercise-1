<div id="product_features_{$block.block_id}">
<div class="ty-feature">
    <div class="ty-feature__image">
        {include 
            file="common/image.tpl" 
			no_ids=true
		    images=$department_data.main_pair 
	        image_width=$settings.Thumbnails.product_lists_thumbnail_width
	        image_height=$settings.Thumbnails.product_lists_thumbnail_height 
          }
    </div>
    <div class="ty-feature__description ty-wysiwyg-content">
		{if !$department_data.description}
			<p>{__("no_description")}</p>
		{else}
        {$department_data.description nofilter}
		{/if}
    </div>
</div>

{if $users}
	{include file="views/departments/components/department_employees.tpl" columns=3}
{else}
    <p class="ty-no-items">{__("no_users")}</p>
{/if}
<!--product_features_{$block.block_id}--></div>
{$supervisor_info = $department_data.supervisor_id|fn_get_user_short_info}
{capture name="mainbox_title"}
	{(__("department_name"))}: {$department_data.department nofilter}
	<br />
	{(__("supervisor"))}: {$supervisor_info.firstname} {$supervisor_info.lastname}
{/capture}