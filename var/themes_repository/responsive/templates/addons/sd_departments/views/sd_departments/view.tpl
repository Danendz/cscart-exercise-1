{if $departments}

{script src="js/tygh/exceptions.js"}

{if !$no_pagination}
    {include "common/pagination.tpl"}
{/if}

{if !$show_empty}
    {split
        data=$departments 
        size=$columns|default:"2" 
        assign="splitted_departments"
    }
{else}
    {split 
        data=$departments 
        size=$columns|default:"2" 
        assign="splitted_departments" 
        skip_complete=true
    }
{/if}

{math equation="100 / x" x=$columns|default:"2" assign="cell_width"}

{* FIXME: Don't move this file *}
{script src="js/tygh/product_image_gallery.js"}

<div class="grid-list">
    {strip}
        {foreach from=$splitted_departments item="sdepartments"}
            {foreach from=$sdepartments item="department"}
            <div class="ty-column{$columns}">
                {if $department}
                {$supervisor_info = $department.supervisor_info}
				{$department_url = "sd_departments.department?department_id={$department.department_id}"|fn_url}
                {$obj_id = $department.department_id}
                {$obj_id_prefix = "`$obj_prefix``$department.department_id`"}

                <div class="ty-grid-list__item ty-quick-view-button__wrapper">
                    <div class="ty-grid-list__image">
                        <a href="{$department_url}">
                            {include
                                "common/image.tpl" 
                                no_ids=true 
                                images=$department.main_pair
                                image_width=250
                                image_height=250
                            }
                        </a>
                    </div>
	                <div class="ty-grid-list__item-name">
	                    <bdi>
	                        <a 
	                            href="{$department_url}"
	                            class="product-title"
	                            title="{$department.department}"
                            >
	                            {$department.department}
	                        </a>    
	                    </bdi>
	                </div>
	                <div class="ty-grid-list__item-name">
	                  <p>
                          {$supervisor_info.firstname} {$supervisor_info.lastname}
	                  </p>    
	                </div>
                </div>
                {/if}
            </div>
            {/foreach}
        {/foreach}
    {/strip}
</div>

{if !$no_pagination}
    {include "common/pagination.tpl"}
{/if}

{else}
    <p class="ty-no-items">{__("sd_departments_text_no_departments")}</p>
{/if}

{capture name="mainbox_title"}{$title}{/capture}