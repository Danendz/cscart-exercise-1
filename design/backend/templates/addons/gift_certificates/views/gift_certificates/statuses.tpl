{include file="views/statuses/update.tpl" privilege="manage_gift_certificates" item=__("statuses") title=__("gift_certificate_statuses") status_type=$smarty.const.STATUSES_GIFT_CERTIFICATE no_inventory="Y" extra_fields="<input type=\"hidden\" name=\"redirect_mode\" value=\"`$smarty.request.mode`\">" }