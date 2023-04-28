{if $users}
    {script src="js/tygh/exceptions.js"}

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}

    {if !$show_empty}
        {split data=$users size=$columns|default:"2" assign="splitted_users"}
    {else}
        {split data=$users size=$columns|default:"2" assign="splitted_users" skip_complete=true}
    {/if}

    {math equation="100 / x" x=$columns|default:"2" assign="cell_width"}

    {* FIXME: Don't move this file *}
    {script src="js/tygh/product_image_gallery.js"}

    <div class="grid-list">
        {strip}
			<h2>{__("employees")}:</h2>
            {foreach from=$splitted_users item="susers"}
                {foreach from=$susers item="user"}
                    <div class="ty-column{$columns}">
                      {if $user}
                        {assign var="obj_id" value=$user.user_id}
                        {assign var="obj_id_prefix" value="`$obj_prefix``$user.user_id`"}
                        <div class="ty-grid-list__item ty-grid-promotions__item">
	                        {include 
						        file="common/image.tpl" 
						        no_ids=true
						        images=$users.main_pair 
	                            image_width=$settings.Thumbnails.product_lists_thumbnail_width
	                            image_height=$settings.Thumbnails.product_lists_thumbnail_height 
                            }
                            <div class="ty-grid-promotions__content">
                          	    <h2 class="ty-grid-promotions__header">
                                    &ZeroWidthSpace;{$user.lastname} {$user.firstname}
                                </h2>
	                            <div class="ty-wysiwyg-content ty-grid-promotions__description">
		                            <p>
										{__("email")}: {$user.email}
		                            </p>
		                            <p>
										{__("phone")}: {$user.phone}
		                            </p>
									<p>
										{__("company")}: {$user.company_name}
									</p>
	                            </div>
        					</div>
                        </div>
                        {/if}
                    </div>
                {/foreach}
            {/foreach}
        {/strip}
    </div>

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}
{else}
    <p class="ty-no-items">{__("text_no_users")}</p>
{/if}

{capture name="mainbox_title"}{$title}{/capture}