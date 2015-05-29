
/**
 * Build-Redame Control
 *
 * Shows the readme and highlight it
 *
 * @author www.pcsg.de (Henning Leutz)
 * @module package/quiqqer/quiqqer-ci/bin/controls/BuildReadme
 */

define('package/quiqqer/quiqqer-ci/bin/controls/BuildReadme', [

    'qui/QUI',
    'qui/controls/Control',
    URL_OPT_DIR +'bin/highlightjs/highlightjs-built',
    'css!'+ URL_OPT_DIR +'packages/bin/highlightjs/styles/default.css'

], function(QUI, QUIControl)
{
    "use strict";

    return new Class({

        Extends : QUIControl,
        Type : 'package/quiqqer/quiqqer-ci/bin/controls/BuildReadme',

        Binds : [
            '$onImport'
        ],

        initialize : function(options)
        {
            this.parent(options);

            this.addEvents({
                onImport : this.$onImport
            });
        },

        /**
         * event : on import
         */
        $onImport : function()
        {
            var Elm = this.getElm(),
                Pre = Elm.getElement('pre');

            var FX = moofx(Pre);

            hljs.highlightBlock(Pre.getElement('code'));

            Elm.setStyle('cursor', 'pointer');

            Elm.addEvents({
                click : function()
                {
                    if (Pre.getStyle('display') == 'none')
                    {
                        Pre.setStyles({
                            display: null,
                            overflow : 'hidden',
                            opacity : 0,
                            height : 0
                        });

                        FX.animate({
                            height: 200,
                            opacity: 1,
                            marginTop : 10,
                            marginBottom : 10
                        }, {
                            callback : function()
                            {
                                Pre.setStyles({
                                    overflow : 'auto'
                                });
                            }
                        });

                        return;
                    }

                    Pre.setStyles({
                        overflow : 'hidden'
                    });

                    FX.animate({
                        height : 0,
                        opacity : 0,
                        marginTop : 0,
                        marginBottom : 0
                    }, {
                        callback : function() {
                            Pre.setStyles({
                                display : 'none'
                            });

                        }
                    });
                }
            });
        }
    });
});