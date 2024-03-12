define([
    'jquery',
    'jquery/jquery.cookie',
    'jquery/jquery.storageapi.min',
    'mage/cookies'
    //'Magento_Cookie/js/jquery.storageapi.extended',
], function ($) {
    'use strict';

    console.info('poc jquery cookies init');

    //almost fully-duplicated (with changes) part of code from 2.4.3 for
    //CE/EE below 2.4.3
    /**
     *
     * @param {Object} storage
     * @private
     */
    function _extend(storage) {
        //var cookiesConfig = window.cookiesConfig || {};
        $.extend(storage, {
            //-----------------------------------//
            //hardcoded values
            _secure: true,
            _samesite: 'None',
            //-----------------------------------//

            /**
             * Set value under name
             * @param {String} name
             * @param {String} value
             * @param {Object} [options]
             */
            setItem: function (name, value, options) {
                //-----------------------------------//
                //hardcoded values
                options = options || {};
                options.samesite = 'None';
                options.secure = true;
                //-----------------------------------//
                var _default = {
                    expires: this._expires,
                    path: this._path,
                    domain: this._domain,
                    secure: this._secure,
                    samesite: this._samesite
                };

                $.cookie(this._prefix + name, value, $.extend(_default, options || {}));
            },

            /**
             * Set default options
             * @param {Object} c
             * @returns {storage}
             */
            setConf: function (c) {
                //-----------------------------------//
                //hardcoded values
                c = c || {};
                c.samesite = 'None';
                c.secure = true;
                //-----------------------------------//
                if (c.path) {
                    this._path = c.path;
                }

                if (c.domain) {
                    this._domain = c.domain;
                }

                if (c.expires) {
                    this._expires = c.expires;
                }

                if (typeof c.secure !== 'undefined') {
                    this._secure = c.secure;
                }

                if (typeof c.samesite !== 'undefined') {
                    this._samesite = c.samesite;
                }

                return this;
            }
        });
    }

    function _extendMage() {
        //fully-duplicated part of code from 2.4.3 for
        //CE/EE below 2.4.3
        function lifetimeToExpires(options, defaults) {
            var expires,
              lifetime;

            lifetime = options.lifetime || defaults.lifetime;

            if (lifetime && lifetime > 0) {
                expires = options.expires || new Date();

                return new Date(expires.getTime() + lifetime * 1000);
            }

            return null;
        }

        //almost fully-duplicated (with changes) part of code from 2.4.3 for
        //CE/EE below 2.4.3
        $.extend($.mage.cookies, {
            set: function (name, value, options) {
                //-----------------------------------//
                // hardcoded values
                options = options || {};
                options.samesite = 'None';
                options.secure = true;
                //-----------------------------------//

                var expires,
                  path,
                  domain,
                  secure,
                  samesite;

                options = $.extend({}, this.defaults, options || {});
                expires = lifetimeToExpires(options, this.defaults) || options.expires;
                path = options.path;
                domain = options.domain;
                secure = options.secure;
                samesite = options.samesite;

                document.cookie = name + '=' + encodeURIComponent(value) +
                  (expires ? '; expires=' + expires.toUTCString() :  '') +
                  (path ? '; path=' + path : '') +
                  (domain ? '; domain=' + domain : '') +
                  (secure ? '; secure' : '') +
                  '; samesite=' + (samesite ? samesite : 'lax');
            }
        });
    }

    if (window.cookieStorage) {
        _extend(window.cookieStorage);
    }

    if ($.mage && $.mage.cookies) {
        _extendMage();
    }
});
