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
            const xml = $($.parseXML('<?xml version="1.0" encoding="UTF-8"?>' +
                '<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.0/cXML.dtd">' +
                '<cXML payloadID="'+ $(this).parent(".punchout-options").find(".cxml-payload-id").val()+'" timestamp="2019-08-03T02:06:05+00:00" version="1.2.044"><Header>\n' +
                '        <From>\n' +
                '            <Credential domain="NetworkId">\n' +
                '                <Identity>218171296</Identity>\n' +
                '            </Credential>\n' +
                '        </From>\n' +
                '        <To>\n' +
                '            <Credential domain="DUNS">\n' +
                '                <Identity>acme</Identity>\n' +
                '            </Credential>\n' +
                '        </To>\n' +
                '        <Sender>\n' +
                '            <Credential domain="NetworkId">\n' +
                '                <Identity>'+ $(this).parent(".punchout-options").find(".cxml-sender-identity").val() + '</Identity>\n' +
                '                <SharedSecret>'+$(this).parent(".punchout-options").find(".cxml-shared-secret").val()+'</SharedSecret>\n' +
                '            </Credential>\n' +
                '            <UserAgent>Application Name v1.2.3</UserAgent>\n' +
                '        </Sender>\n' +
                '    </Header>' +
                '<Request deploymentMode="production">\n' +
                '        <PunchOutSetupRequest operation="create">\n' +
                '            <BuyerCookie>'+$(this).parent(".punchout-options").find(".cxml-buyer-cookie").val()+'</BuyerCookie>' +

                '</cXML>'));

            $.ajax({
                type: "POST",
                url: $(this).parent(".punchout-options").find(".cxml-url").val(),
                cache: false,
                dataType: "xml",
                contentType: "text/xml",
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
                    const returnUrl = $(data).find('URL').first().text();
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
