<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">

                <button type="submit" form="form-paysonCheckout2" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>

            <div class="panel-body">   

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-paysonCheckout2" class="form-horizontal">
                    <!--disabled-->                  
                    <!--disabled-->                  
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-modul-version"><?php echo $text_modul_name.' V- '.$paysonCheckout2_modul_version; ?></label>
                        <div class="col-sm-10" hidden>
                            <input type="text" name="paysonCheckout2_modul_version" hidden value="<?php echo $text_modul_version; ?>" placeholder="<?php echo $text_modul_version; ?>" id="input-modul-version" class="form-control" />
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_sort_order" value="<?php echo $paysonCheckout2_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_status" id="input-status" class="form-control">
                                <?php if ($paysonCheckout2_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-method-mode"><span data-toggle="tooltip" title="<?php echo $help_method_mode; ?>"><?php echo $entry_method_mode; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_mode" id="input-method-mode" class="form-control">
                                <option value="1" <?php echo ($paysonCheckout2_mode?'selected':''); ?> ><?php echo $text_method_mode_live; ?></option>
                                <option value="0" <?php echo ($paysonCheckout2_mode?'':'selected'); ?> ><?php echo $text_method_mode_sandbox; ?></option>
                            </select>

                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-merchant-id"><span data-toggle="tooltip" title="<?php echo $help_merchant_id; ?>"><?php echo $merchant_id; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_merchant_id" value="<?php echo $paysonCheckout2_merchant_id; ?>" placeholder="<?php echo $merchant_id; ?>" id="input-merchant-id" class="form-control" />
                            <?php if ($error_merchant_id) { ?>
                            <div class="text-danger"><?php echo $error_merchant_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-api-key"><span data-toggle="tooltip" title="<?php echo $help_api_key; ?>"><?php echo $api_key; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_api_key" value="<?php echo $paysonCheckout2_api_key; ?>" placeholder="<?php echo $api_key; ?>" id="input-api-key" class="form-control" />
                            <?php if ($error_api_key) { ?>
                            <div class="text-danger"><?php echo $error_api_key; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secure-word"><span data-toggle="tooltip" title="<?php echo $help_secure_word; ?>"><?php echo $secure_word; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_secure_word" value="<?php echo $paysonCheckout2_secure_word; ?>" placeholder="<?php echo $secure_word; ?>" id="input-secure-word" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-logg"><span data-toggle="tooltip" title="<?php echo $help_logg; ?>"><?php echo $entry_logg; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_logg" id="input-logg" class="form-control">
                                <option value="1" <?php echo ($paysonCheckout2_logg == 1 ? 'selected="selected"' : '""') . '>'  . $text_enabled?></option>
                                <option value="0" <?php echo ($paysonCheckout2_logg == 0 ? 'selected="selected"' : '""') . '>' . $text_disabled?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_total" value="<?php echo $paysonCheckout2_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $paysonCheckout2_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>   

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $paysonCheckout2_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>      

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-verification"><span data-toggle="tooltip" title="<?php echo $help_verification; ?>"><?php echo $entry_verification; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_gui_verification" id="input-verification" class="form-control">
                                <?php if (!$paysonCheckout2_gui_verification) { ?>
                                <option value="none" selected="selected"><?php echo $text_verification_none; ?></option>
                                <option><?php echo $text_verification_bankid ?></option> 
                                <?php } else { ?>
                                <option><?php echo $paysonCheckout2_gui_verification; ?></option>
                                <option> <?php echo $text_verification_none; ?></option>
                                <option><?php echo $text_verification_bankid ?></option>    
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-phone"><span data-toggle="tooltip" title="<?php echo $help_request_phone; ?>"><?php echo $entry_phone; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_request_phone" id="input-phone" class="form-control">
                                <?php if ($paysonCheckout2_request_phone) { ?>
                                <option value="1" selected="selected"><?php echo $text_phone_yes; ?></option>
                                <option value="0"><?php echo $text_phone_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_phone_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_phone_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-color-scheme"><span data-toggle="tooltip" title="<?php echo $help_color_scheme; ?>"><?php echo $entry_color_scheme; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_color_scheme" id="input-color-scheme" class="form-control">   
                                <?php if (!$paysonCheckout2_color_scheme) { ?>
                                <option value="gray" selected="selected"><?php echo $text_color_scheme_gray; ?></option>
                                <option value="blue"> <?php echo $text_color_scheme_blue; ?></option>
                                <option value="white"><?php echo $text_color_scheme_white; ?></option>
                                <?php } else { ?>
                                <option><?php echo $paysonCheckout2_color_scheme; ?></option>
                                <option value="gray"> <?php echo $text_color_scheme_gray; ?></option>
                                <option value="blue"> <?php echo $text_color_scheme_blue; ?></option>
                                <option value="white"><?php echo $text_color_scheme_white; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-iframe-size-width"><span data-toggle="tooltip" title="<?php echo $help_iframe_size_width; ?>"><?php echo $entry_iframe_size_width; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_iframe_size_width" value="<?php echo $paysonCheckout2_iframe_size_width; ?>" placeholder="<?php echo $entry_iframe_size_width; ?>" id="input-iframe-size-width" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-iframe-size-width-type"><span data-toggle="tooltip" title="<?php echo $help_iframe_size_width_type; ?>"><?php echo $entry_iframe_size_width_type; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_iframe_size_width_type" id="input-iframe_size_width_type" class="form-control">
                                <?php if (!$paysonCheckout2_iframe_size_width_type) { ?>
                                <option value="%" selected="selected"><?php echo $text_iframe_size_width_percent; ?></option>
                                <option><?php echo $text_iframe_size_width_px ?></option> 
                                <?php } else { ?>
                                <option><?php echo $paysonCheckout2_iframe_size_width_type; ?></option>
                                <option> <?php echo $text_iframe_size_width_percent; ?></option>
                                <option><?php echo $text_iframe_size_width_px ?></option>    
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-iframe-size-height"><span data-toggle="tooltip" title="<?php echo $help_iframe_size_height; ?>"><?php echo $entry_iframe_size_height; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_iframe_size_height" value="<?php echo $paysonCheckout2_iframe_size_height; ?>" placeholder="<?php echo $entry_iframe_size_height; ?>" id="input-iframe-size-height" class="form-control" />
                        </div>
                    </div>  
                    

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-iframe-size-height-type"><span data-toggle="tooltip" title="<?php echo $help_iframe_size_height_type; ?>"><?php echo $entry_iframe_size_height_type; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_iframe_size_height_type" id="input-iframe_size_height_type" class="form-control">
                                <?php if (!$paysonCheckout2_iframe_size_height_type) { ?>
                                <option value="px" selected="selected"><?php echo $text_iframe_size_height_px; ?></option>
                                <option><?php echo $text_iframe_size_height_percent ?></option> 
                                <?php } else { ?>
                                <option><?php echo $paysonCheckout2_iframe_size_height_type; ?></option>
                                <option> <?php echo $text_iframe_size_height_percent; ?></option>
                                <option><?php echo $text_iframe_size_height_px ?></option>    
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-comments"><span data-toggle="tooltip" title="<?php echo $help_comments; ?>"><?php echo $entry_show_comments; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_comments" id="input-comments" class="form-control">
                                <option value="1" <?php echo ($paysonCheckout2_comments == 1 ? 'selected="selected"' : '""') . '>'  . $entry_show_comments_yes?></option>
                                <option value="0" <?php echo ($paysonCheckout2_comments == 0 ? 'selected="selected"' : '""') . '>' . $entry_show_comments_no?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-receipt"><span data-toggle="tooltip" title="<?php echo $help_receipt; ?>"><?php echo $entry_show_receipt_page; ?></span></label>
                        <div class="col-sm-10">
                            <select name="paysonCheckout2_receipt" id="input-receipt" class="form-control">
                                <option value="1" <?php echo ($paysonCheckout2_receipt == 1 ? 'selected="selected"' : '""') . '>'  . $entry_show_receipt_page_yes?></option>
                                <option value="0" <?php echo ($paysonCheckout2_receipt == 0 ? 'selected="selected"' : '""') . '>' . $entry_show_receipt_page_no?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-ignored-order-totals"><span data-toggle="tooltip" title="<?php echo $help_totals_to_ignore; ?>"><?php echo $entry_totals_to_ignore; ?></span></label>

                        <div class="col-sm-10">
                            <input type="text" name="paysonCheckout2_ignored_order_totals" value="<?php echo ($paysonCheckout2_ignored_order_totals == '' ? '' : $paysonCheckout2_ignored_order_totals); ?>" placeholder="<?php echo $entry_totals_to_ignore; ?>" id="input-ignored-order-totals" class="form-control" />
                            <?php if ($error_ignored_order_totals) { ?>
                            <div class="text-danger"><?php echo $error_ignored_order_totals; ?></div>
                            <?php } ?></div>
                    </div>  
                </form>         
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?> 