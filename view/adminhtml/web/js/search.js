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
            const timestamp = new Date();
            const xml = '<?xml version="1.0" encoding="UTF-8"?>\n' +
                '<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.044/cXML.dtd">' +
                '<cXML payloadID="'+ $(this).parent(".punchout-options").find(".cxml-payload-id").val()+'" timestamp="'+ timestamp.toISOString() +'" version="1.2.044" xml:lang="en"><Header>\n' +
                '        <From>\n' +
                '            <Credential domain="NetworkId">\n' +
                '                <Identity></Identity>\n' +
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
                '                <SharedSecret>' + $(this).parent(".punchout-options").find(".cxml-shared-secret").val() + '</SharedSecret>\n' +
                '            </Credential>\n' +
                '            <UserAgent>Application Name v1.2.3</UserAgent>\n' +
                '        </Sender>\n' +
                '    </Header>' +
                '<Request deploymentMode="production">\n' +
                '        <PunchOutSetupRequest operation="' + $(this).parent(".punchout-options").find(".cxml-request-operation").val() + '">\n' +
                '            <BuyerCookie>' + $(this).parent(".punchout-options").find(".cxml-buyer-cookie").val() + '</BuyerCookie>' +
                '<Extrinsic name="UserEmail">' + $(this).parent(".punchout-options").find(".cxml-contact-data-email").val() + '</Extrinsic>' +
                '<BrowserFormPost>\n' +
                '                <URL>' + $(this).parent(".punchout-options").find(".cxml-return-url").val() + '</URL>\n' +
                '            </BrowserFormPost>\n' +
                '            <Contact role="endUser">\n' +
                '                <Name xml:lang="en-GB">' + $(this).parent(".punchout-options").find(".cxml-contact-data-name").val() + '</Name>\n' +
                '                <Email>' + $(this).parent(".punchout-options").find(".cxml-contact-data-email").val() + '</Email>\n' +
                '            </Contact>' +
                '       </PunchOutSetupRequest>\n' +
                '    </Request>' +
                '</cXML>';

            $.ajax({
                type: "POST",
                url: $(this).parent(".punchout-options").find(".cxml-url").val(),
                cache: false,
                dataType: "xml",
                contentType: "application/xml",
                data: xml,
                processData: false,
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
