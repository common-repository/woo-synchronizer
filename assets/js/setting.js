/* =========================================================
 * admin-setting v1.0.0
 * =========================================================
 * Copyright 2018 S.J.Hosseini
 *
 * Woo Sync dashboard js
 * ========================================================= */
jQuery(document).ready(function($) {

        ///// Check copyrights
        if (!$('.woo-sync-footer').length) {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if (!$('.woo-sync-copyright-name').length) {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if (!$('.woo-sync-copyright-telegram').length) {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if ($('.woo-sync-footer').css('display') == 'none') {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if ($('.woo-sync-copyright-name').css('display') == 'none') {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if ($('.woo-sync-copyright-telegram').css('display') == 'none') {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if ($('.woo-sync-footer').css('visibility') == 'hidden') {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if ($('.woo-sync-copyright-name').css('visibility') == 'hidden') {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }
        if ($('.woo-sync-copyright-telegram').css('visibility') == 'hidden') {
                $(".woo_sync_body").html("Developer : Seyed Jafar Hosseini | Telegram : @ttmga");
        }


		
		
	
        var lastotype = '';
        var globalintervals = [];
        $("#is_on_localhost").change(function() {
                if (this.checked) {
                        $(".woo_sync_in_local_host").show();
                }
                else {
                        $(".woo_sync_in_local_host").hide();
                }
        });


        if ($('input#is_on_localhost').is(':checked')) {
                $(".woo_sync_in_local_host").show();
        }


        $(document).on('click', '#test_connection_restapi', function() {
                $("#woo_sync_loader1").show();

                var url = jQuery(this).data('ajax');
                var target = $("#woo_sync_target_url").val();
                var ck = $("#woo_sync_customer_key").val();
                var sk = $("#woo_sync_secret_key").val();

                var err = 0;
                var errors = '';
                if (target.length === 0) {
                        err = 1;
                        $("#woo_sync_target_url").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.storeurlerr + '<br>';
                }

                if (ck.length === 0) {
                        err = 1;
                        $("#woo_sync_customer_key").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.ckerror + '<br>';
                }

                if (sk.length === 0) {
                        err = 1;
                        $("#woo_sync_secret_key").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.skerror + '<br>';
                }

                if (err == 0) {
                        jQuery.ajax({
                                type: "POST"
                                , url: url
                                , data: { 'action': 'woo_sync_test_connection_rest', 'target': target, 'ck': ck, 'sk': sk }
                                , success: function(textStatus) {
                                        $("#woo_sync_loader1").hide();
                                        $("#woo_sync_test_restapi_result").html('<code id="woo_sync_response">' + textStatus + '</code>');
                                }
                                , error: function(MLHttpRequest, textStatus, errorThrown) {
                                        alert(errorThrown);
                                }
                        });
                }
                else {
                        $("#woo_sync_loader1").hide();
                        $("#woo_sync_test_restapi_result").html('<code id="woo_sync_response">' + errors + '</code>');
                }

        });




        $(document).on('click', '#test_connection_ftp', function() {
                $("#woo_sync_loader2").show();

                var url = jQuery(this).data('ajax');
                var target = $("#woo_sync_target_url").val();
                var ck = $("#woo_sync_customer_key").val();
                var sk = $("#woo_sync_secret_key").val();

                var port = $("#woo_sync_server_port").val();
                var user = $("#woo_sync_ftp_user").val();
                var pass = $("#woo_sync_ftp_pass").val();

                var err = 0;
                var errors = '';
                if (target.length === 0) {
                        err = 1;
                        $("#woo_sync_target_url").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.storeurlerr + '<br>';
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                }

                if (ck.length === 0) {
                        err = 1;
                        $("#woo_sync_customer_key").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.ckerror + '<br>';
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                }

                if (sk.length === 0) {
                        err = 1;
                        $("#woo_sync_secret_key").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.skerror + '<br>';
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                }

                if (port.length === 0) {
                        err = 1;
                        $("#woo_sync_server_port").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.ftpporterr + '<br>';
                }

                if (user.length === 0) {
                        err = 1;
                        $("#woo_sync_ftp_user").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.ftpusererror + '<br>';
                }

                if (pass.length === 0) {
                        err = 1;
                        $("#woo_sync_ftp_pass").css({ "border": "2px solid red" });
                        errors += woo_sync_trans.ftppasserror + '<br>';
                }

                if (err == 0) {

                        jQuery.ajax({
                                type: "POST"
                                , url: url
                                , data: { 'action': 'woo_sync_test_connection_ftp', 'target': target, 'ck': ck, 'sk': sk, 'port': port, 'user': user, 'pass': pass }
                                , success: function(textStatus) {
                                        var response = JSON.parse(textStatus);
                                        $("#woo_sync_loader2").hide();
                                        if (response.OK == "created") {
                                                $("#woo_sync_test_ftp_result").html('<code id="woo_sync_response">' + response.message + '</code>');
                                                $("#woo_sync_server_ip").val(response.SERVER);
                                                $("#woo_sync_url_cdn").val(response.UPATH);
                                                $("#woo_sync_server_path").val(response.DPATH);
                                        }

                                        if (response.OK == "success") {
                                                $("#woo_sync_test_ftp_result").html('<code id="woo_sync_response">' + response.message + '</code>');
                                                $("#woo_sync_server_ip").val(response.SERVER);
                                                $("#woo_sync_url_cdn").val(response.UPATH);
                                                $("#woo_sync_server_path").val(response.DPATH);												
                                        }

                                        if (response.OK == "Faild") {
                                                $("#woo_sync_test_ftp_result").html('<code id="woo_sync_response">' + response.reason + '</code>');
                                                if (response.KIND != 'auth') {
                                                        $('#woo_sync_server_ip').show();
                                                        $('#woo_sync_server_url').show();
                                                        $('#woo_sync_server_path_tr').show();
                                                }
                                        }

                                }
                                , error: function(MLHttpRequest, textStatus, errorThrown) {
                                        alert(errorThrown);
                                }
                        });

                }
                else {
                        $("#woo_sync_loader2").hide();
                        $("#woo_sync_test_ftp_result").html('<code id="woo_sync_response">' + errors + '</code>');
                }


        });



        $(document).on('click', '.woo-sync-dismiss', function() {
                var url = jQuery(this).data('ajax');
                var disid = jQuery(this).attr('data-disid');
                var kind = jQuery(this).attr('data-kind');
                var objid = jQuery(this).attr('data-objid');
                var elem = '#woo-row' + disid;
                $(elem).css({ "background-color": "red" });
                jQuery.ajax({
                        type: "POST"
                        , url: url
                        , data: { 'action': 'woo_sync_delete_future_list', 'id': disid, 'kind': kind, 'objid': objid }
                        , success: function(textStatus) {

                                $(elem).remove();

                        }
                        , error: function(MLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                        }
                });

        });



	
        $(document).on('click', '#woo_sync_publish_future', function() {
                var url = jQuery(this).data('ajax');

                $("#woo_sync_loader3").fadeIn();
                $("#woo_sync_publish_future").hide();

                jQuery.ajax({
                        type: "POST"
                        , url: url
                        , data: { 'action': 'woo_sync_future_list_elements' }
                        , success: function(textStatus) {
                                $("#woo_sync_response_publish").show();


                                try {
                                        var products = JSON.parse(textStatus).reverse();
                                }
                                catch (e) {
                                        $("#woo_sync_publish_future").show();
                                        $("#woo_sync_response_publish").html(textStatus);
                                }

                                woo_sync_publishing(products);

                                function woo_sync_publishing(products) {
                                        $("#woo_sync_loader3").show();
                                        var item = products[0];
                                        var next = products;

                                        var elem = '#woo-row' + item;
                                        $(elem).css({ "background-color": "#4CAF50" });


                                        jQuery.ajax({
                                                type: "POST"
                                                , url: url
                                                , async: true
                                                , data: { 'action': 'woo_sync_future_list_publisher', 'id': item }
                                                , success: function(result) {


                                                        try {
                                                                var check = JSON.parse(result);
                                                                $("#woo_sync_response_publish").append(check.info);

                                                                if (check.OK == 'Down') {
                                                                        $(elem).fadeOut();
                                                                }
                                                                else {
                                                                        $(elem).css({ "background-color": "#f50000a8" });
                                                                }
                                                        }
                                                        catch (e) {

                                                        }


                                                        if (!Array.isArray(next) || !next.length) {
													        setTimeout(function(){
                                                            jQuery.ajax({
                                                                   type: "POST"
                                                                 , url: url
                                                                 , data: { 'action': 'woo_sync_future_list_delete_server_files'}
                                                                 , success: function(textStatus) {

                                                                    $("#woo_sync_loader3").fadeOut();
                                                                    $("#woo_sync_response_publish").append(textStatus);																	
                                                                    console.log("down");

                                                                 }
                                                                 , error: function(MLHttpRequest, textStatus, errorThrown) {
                                                                 alert(errorThrown);
                                                                }
                                                            });
                                                             }, 2000);															
																
                                                        }
                                                        else {
                                                                console.log(item);
                                                                next.splice(0, 1);
                                                                woo_sync_publishing(next);
                                                        }
                                                }
                                                , error: function(MLHttpRequest, textStatus, errorThrown) {
                                                        alert(errorThrown);
                                                }
                                        });
                                }



                        }
                        , error: function(MLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                        }
                });

        });






        $(document).on('click', '#woo_sync_update_changes_target', function() {
                var url = jQuery(this).data('ajax');

                $("#woo_sync_loader32").show();
                $("#woo_sync_update_changes_target").hide();
                $("#woo_sync_delete_changes_log").hide();
                $("#woo_sync_response_updater").show();

                jQuery.ajax({
                        type: "POST"
                        , url: url
                        , data: { 'action': 'woo_sync_update_before_publish' }
                        , success: function(textStatus) {

                                console.log(textStatus);

                                $("#woo_sync_update_changes_target").show();
                                $("#woo_sync_delete_changes_log").show();
                                $("#woo_sync_response_updater").append(textStatus);
                                $("#woo_sync_loader32").hide();


                        }
                        , error: function(MLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                        }
                });

        });





        $(document).on('click', '#woo_sync_delete_changes_log', function() {
                var url = jQuery(this).data('ajax');



                var txt;
                var r = confirm(woo_sync_trans.ConfirmreqTime);
                if (r == true) {
                        $("#woo_sync_loader32").show();
                        $("#woo_sync_update_changes_target").hide();
                        $("#woo_sync_delete_changes_log").hide();
                        jQuery.ajax({
                                type: "POST"
                                , url: url
                                , data: { 'action': 'woo_sync_delete_change_log' }
                                , success: function(textStatus) {

                                        if (textStatus == '"Down"') {
                                                alert(woo_sync_trans.success);
                                                location.reload();
                                        }
                                        else {
                                                alert(textStatus);
                                                $("#woo_sync_loader32").hide();
                                                $("#woo_sync_update_changes_target").show();
                                                $("#woo_sync_delete_changes_log").show();
                                        }

                                }
                        });
                }

                if (r == false) {
                        $("#woo_sync_loader32").hide();
                }


        });



        $(document).on('click', '#woo_sync_download_objects', function() {
                var url = jQuery(this).data('ajax');
                var result = jQuery(this).data('result');
                $("#woo_sync_loader4").show();
                $("#woo_sync_download_objects").hide();
                $("#woo_sync_continue_download_objects").hide();
                $("#woo_sync_Delete_objects").hide();
                $("#woo_sync_response_download").show();
                $("#woo_sync_live_res_terminal").show();
                $("#woo_sync_response_download").html(woo_sync_trans.preaper + '</br>');

			
                jQuery.ajax({
                        type: "POST"
                        , url: url
                        , data: { 'action': 'woo_sync_delete_error_log' }
                });

                window.wooInterval = setInterval(function() {
                        var ms = new Date().getTime();
                        $.get(result + "?dummy=" + ms, function(data) {
                                $("#woo_sync_live_res_terminal").html(data);
                        }, 'text');
                }, 500);

                var objects = ['tax','shipping','attribute', 'category', 'tag', 'product'];
                woo_sync_preapering_download(objects);

                function woo_sync_preapering_download(objects) {
                        var otype = objects[0];
                        var next = objects;

                        if (otype == 'attribute') {
                                $("#woo_sync_response_download").append(woo_sync_trans.attribute + '</br><p id="woo_sync_live_res_attribute_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                        }
                        if (otype == 'tag') {
                                $("#woo_sync_response_download").append(woo_sync_trans.tags + '</br><p id="woo_sync_live_res_tag_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                        }
                        if (otype == 'category') {
                                $("#woo_sync_response_download").append(woo_sync_trans.category + '</br><p id="woo_sync_live_res_category_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                        }
                        if (otype == 'product') {
                                $("#woo_sync_response_download").append(woo_sync_trans.product + '</br><p id="woo_sync_live_res_product_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                        }
                        if (otype == 'tax') {
                                $("#woo_sync_response_download").append(woo_sync_trans.tax + '</br><p id="woo_sync_live_res_product_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                        }
                        if (otype == 'shipping') {
                                $("#woo_sync_response_download").append(woo_sync_trans.shipping + '</br><p id="woo_sync_live_res_product_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                        }

                        jQuery.ajax({
                                type: "POST"
                                , url: url
                                , async: true
                                , data: { 'action': 'woo_sync_preapering_downloads', 'otype': otype }
                                , success: function(textStatus) {
                                        if (textStatus != 'loseInternetConnection') {
                                                $("#woo_sync_response_download").append(textStatus);
                                        }
                                        $('#woo_sync_response_download').scrollTop($('#woo_sync_response_download')[0].scrollHeight);

                                        if (!Array.isArray(next) || !next.length) {

                                                var objects = ['tax','shipping','attribute', 'category', 'tag', 'product'];
                                                woo_sync_start_downloading(objects, result, url, lastotype);

                                        }
                                        else {

                                                if (textStatus == 'loseInternetConnection') {
                                                                        $("#woo_sync_response_download").append('<h2 style="color:red;">' + woo_sync_trans.connectionlost + '</h2><br>');
                                                                        console['debug']('Waitting For network...');
																		
													setTimeout(function(){ woo_sync_preapering_download(next); }, 5000);
                                                 $('#woo_sync_response_download').scrollTop($('#woo_sync_response_download')[0].scrollHeight);                                                                       
                                                }
                                                else {
                                                        next.splice(0, 1);
                                                        woo_sync_preapering_download(next, lastotype);
                                                }

                                        }


                                }
                                , error: function(MLHttpRequest, textStatus, errorThrown) {
                                        alert(errorThrown);
                                }
                        });

                }
        });





        function woo_sync_start_downloading(objects, result, url, lastotype) {

                var otype = objects[0];
                var next = objects;

                if (otype == 'attribute') {
                        $("#woo_sync_response_download").append(woo_sync_trans.attribute + '</br><p id="woo_sync_live_res_attribute_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                }
                if (otype == 'tag') {
                        $("#woo_sync_response_download").append(woo_sync_trans.tags + '</br><p id="woo_sync_live_res_tag_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                }
                if (otype == 'category') {
                        $("#woo_sync_response_download").append(woo_sync_trans.category + '</br><p id="woo_sync_live_res_category_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                }
                if (otype == 'product') {
                        $("#woo_sync_response_download").append(woo_sync_trans.product + '</br><p id="woo_sync_live_res_product_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                }
                if (otype == 'tax') {
                        $("#woo_sync_response_download").append(woo_sync_trans.tax + '</br><p id="woo_sync_live_res_product_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                }
                if (otype == 'shipping') {
                        $("#woo_sync_response_download").append(woo_sync_trans.shipping + '</br><p id="woo_sync_live_res_product_dl" style="font-size:10px;color: #efff00;text-align: right;    direction: rtl;"></p>');
                }				

                if (otype != 'product') {
                        jQuery.ajax({
                                type: "POST"
                                , url: url
                                , async: true
                                , data: { 'action': 'woo_sync_start_downloads', 'otype': otype }
                                , success: function(textStatus) {

                                        if (textStatus != 'loseInternetConnection') {
                                                $("#woo_sync_response_download").append(textStatus);
                                        }

                                        $('#woo_sync_response_download').scrollTop($('#woo_sync_response_download')[0].scrollHeight);

                                        if (!Array.isArray(next) || !next.length) {
                                                window.clearInterval(window.wooInterval);
                                                
                                                lastotype = '';
                                                $("#woo_sync_loader4").hide();
                                        }
                                        else {


                                                if (textStatus == 'loseInternetConnection') {

                                                    $("#woo_sync_response_download").append('<h2 style="color:red;">' + woo_sync_trans.connectionlost + '</h2><br>');
                                                    console['debug']('Waitting For network...');
													setTimeout(function(){ woo_sync_start_downloading(next, result, url, lastotype); }, 5000);																	
                                                $('#woo_sync_response_download').scrollTop($('#woo_sync_response_download')[0].scrollHeight);                                                                        																		


                                                }
                                                else {
                                                        lastotype = '';
                                                        
                                                        next.splice(0, 1);
                                                        woo_sync_start_downloading(next, result, url, lastotype);
                                                }

                                        }


                                }
                                , error: function(MLHttpRequest, textStatus, errorThrown) {
                                        alert(errorThrown);
                                }
                        });

                }
                else {


                        window.clearInterval(window.wooInterval);
                        var data = '';
                        $("#woo_sync_live_res_terminal").html('');
                        woo_sync_create_products_loop(url, data, result);
                }


        }



		
        $(document).on('click', '#woo_sync_continue_download_objects', function() {
                window.wooInterval = window.setInterval(function() {
                        var ms = new Date().getTime();
                        $.get(result + "?dummy=" + ms, function(data) {
                                $("#woo_sync_live_res_terminal").html(data);
                        }, 'text');
                }, 500);
                var url = jQuery(this).data('ajax');
                var result = jQuery(this).data('result');
                $("#woo_sync_loader4").show();
                $("#woo_sync_download_objects").hide();
                $("#woo_sync_continue_download_objects").hide();
                $("#woo_sync_Delete_objects").hide();
                $("#woo_sync_response_download").show();
                $("#woo_sync_live_res_terminal").show();
                $("#woo_sync_response_download").html(woo_sync_trans.preaper + '</br>');
                var objects = ['tax','shipping','attribute', 'category', 'tag', 'product'];
                woo_sync_start_downloading(objects, result, url);
        });




        function woo_sync_create_products_loop(url, data, result) {
                
                jQuery.ajax({
                        type: "POST"
                        , url: url
                        , async: true
                        , data: { 'action': 'woo_sync_create_product_with_wp_ajax', 'data': data }
                        , success: function(textStatus) {


                               //console['warn'](textStatus);
                                var response = JSON.parse(textStatus);
                                if (response.DATA == 'Continue') {
									
									if (response.OK){
                                        console['info'](response.OK);
									}else{
										$("#woo_sync_response_download").append(response.OKnotconsol);
									}
                                        woo_sync_create_products_loop(url, response.DATA, result);

                                }
                                else {


                                        if (response.DATA == 'loseInternetConnection') {
                                                $("#woo_sync_response_download").append('<h2 style="color:red;">' + woo_sync_trans.connectionlost + '</h2><br>');
                                                console['debug']('waiting for network...');
												setTimeout(function(){ woo_sync_create_products_loop(url, 'Continue', result); }, 5000);												
                                                $('#woo_sync_response_download').scrollTop($('#woo_sync_response_download')[0].scrollHeight);                                                

                                        }
                                        else {

                                                var str = result;
                                                var res = str.replace("results.txt", "error.txt");

                                                var ms = new Date().getTime();
                                                $.get(res + "?dummy=" + ms, function(data) {
                                                        $("#woo_sync_live_res_terminal").html(data);
                                                }, 'text');


                                                jQuery.ajax({
                                                        type: "POST"
                                                        , url: url
                                                        , data: { 'action': 'woo_sync_delete_error_log' }
                                                });

                                                $("#woo_sync_response_download").append('<h2 style="color:green;" >' + response.OK + '</h2>');
                                                $('#woo_sync_response_download').scrollTop($('#woo_sync_response_download')[0].scrollHeight);
                                                $("#woo_sync_loader4").hide();
                                        }
                                }

                        }
                        , error: function(MLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                        }
                });


        }







        $(document).on('click', '#woo_sync_Delete_objects', function() {

                var url = jQuery(this).data('ajax');
                var result = jQuery(this).data('result');

                $("#woo_sync_loader4").show();
                $("#woo_sync_download_objects").hide();
                $("#woo_sync_continue_download_objects").hide();
                $("#woo_sync_Delete_objects").hide();



                var txt;
                var r = confirm(woo_sync_trans.Confirmreq);
                if (r == true) {
                        jQuery.ajax({
                                type: "POST"
                                , url: url
                                , data: { 'action': 'woo_sync_delete_All_Downloaded_files' }
                                , success: function(textStatus) {
                                        alert(woo_sync_trans.success);
                                        location.reload();

                                }
                        });
                }

                if (r == false) {
                        $("#woo_sync_download_objects").show();
                        $("#woo_sync_continue_download_objects").show();
                        $("#woo_sync_Delete_objects").show();
                        $("#woo_sync_loader4").hide();
                }



        });








        rewireLoggingToElement(
                () => document.getElementById("woo_sync_live_res_terminal")
                , () => document.getElementById("woo_sync_download_result"), true);

        function rewireLoggingToElement(eleLocator, eleOverflowLocator, autoScroll) {
                fixLoggingFunc('log');
                fixLoggingFunc('debug');
                fixLoggingFunc('warn');
                fixLoggingFunc('error');
                fixLoggingFunc('info');

                function fixLoggingFunc(name) {
                        console['old' + name] = console[name];
                        console[name] = function(...arguments) {
                                const output = produceOutput(name, arguments);
                                const eleLog = eleLocator();

                                if (autoScroll) {
                                        const eleContainerLog = eleOverflowLocator();
                                        const isScrolledToBottom = eleContainerLog.scrollHeight - eleContainerLog.clientHeight <= eleContainerLog.scrollTop + 1;
                                        eleLog.innerHTML += output + "<br>";
                                        if (isScrolledToBottom) {
                                                eleContainerLog.scrollTop = eleContainerLog.scrollHeight - eleContainerLog.clientHeight;
                                        }
                                }
                                else {
                                        eleLog.innerHTML += output + "<br>";
                                }

                                console['old' + name].apply(undefined, arguments);
                        };
                }

                function produceOutput(name, arguments) {
                        return arguments.reduce((output, arg) => {
                                return output +
                                        "<span class=\"log-" + (typeof arg) + " log-" + name + "\">" +
                                        (typeof arg === "object" && (JSON || {}).stringify ? JSON.stringify(arg) : arg) +
                                        "</span>&nbsp;";
                        }, '');
                }
        }



});
