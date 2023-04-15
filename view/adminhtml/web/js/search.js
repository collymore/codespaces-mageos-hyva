define([
    'jquery'
], function ($) {
    'use strict';
    $('#punchout-search').keyup(function () {
        var searchText = $(this).val().toLowerCase();
        $('#punchout-options tr.group-row').each(function () {
            var currentLiText = $(this).find(".group-name").text().toLowerCase(),
                showCurrentLi = currentLiText.indexOf(searchText) !== -1;
            $(this).toggle(showCurrentLi);
        });
    });
    $('.punchout-icon').click(function () {
        $(this).toggleClass("active");
    });
    $('.punchout-option').click(function (e) {
        debugger
        e.preventDefault();
        if($(this).hasClass("oci")){
            let url = $(this).parent(".punchout-options").find(".oci-url").val() + "?username=" + $(this).parent(".punchout-options").find(".oci-username").val()
                + "&password=" + $(this).parent(".punchout-options").find(".oci-password").val()
                + "&hook_url=" + $(this).parent(".punchout-options").find(".oci-hook").val()
                +"&~target="+'_blank'
                +"&~okcode=ADDI"
                +"&~caller=CTLG"
               +"&OCI_VERSION=4.0";
            return window.open(
                url,
                '_blank'
            );
        }
        else if($(this).hasClass("cxml")){
            $.ajax({
                type: "GET",
                url: $(this).parent(".punchout-options").find(".cxml-url").val(),
                cache: false,
                dataType: "xml",
                data: {
                    'testpunchout': "testpunchout",
                    'payloadId': $(this).parent(".punchout-options").find(".cxml-payload-id").val(),
                    'buyer_cookie': $(this).parent(".punchout-options").find(".cxml-buyer-cookie").val(),
                    'shared_secret': $(this).parent(".punchout-options").find(".cxml-shared-secret").val(),
                    'request_operation': $(this).parent(".punchout-options").find(".cxml-request-operation").val(),
                    'sender_identity': $(this).parent(".punchout-options").find(".cxml-sender-identity").val(),
                    'ariba_network_id':$(this).parent(".punchout-options").find(".cxml-ariba-network-id").val(),
                    'extrinsic_data': $(this).parent(".punchout-options").find(".cxml-extrinsic-data").val(),
                    'contact_data': $(this).parent(".punchout-options").find(".cxml-contact-data").val(),
                    'return_url': $(this).parent(".punchout-options").find(".cxml-return-url").val()
                },
                success: function(data) {
                    var returnUrl = $(data).find('URL').first().text();
                    if(returnUrl){
                        window.open(returnUrl, '_blank');
                    }
                },
                error: function (data) {
                    alert('Could not login. ');
                    console.log(data);
                }
            });
            return false;
        }
    });
});
