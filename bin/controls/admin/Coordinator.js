/**
 * Coordinator
 *
 * Manage all your ci projects over the administration
 *
 * @author www.pcsg.de (Henning Leutz)
 * @module package/quiqqer/quiqqerci/bin/controls/admin/Coordinator
 *
 * @require qui/QUI
 * @require qui/controls/Control
 */

define('package/quiqqer/quiqqerci/bin/controls/admin/Coordinator', [

    'qui/QUI',
    'qui/controls/desktop/Panel',
    'qui/controls/windows/Confirm',
    'controls/grid/Grid',
    'Ajax'

], function(QUI, QUIPanel, QUIConfirm, Grid, Ajax)
{
    "use strict";

    return new Class({

        Extends: QUIPanel,
        Type: 'package/quiqqer/quiqqerci/bin/controls/admin/Coordinator',

        Binds: [
            '$onCreate',
            '$onResize'
        ],

        initialize: function (options)
        {
            this.parent(options);

            this.setAttribute('title', 'CI Projekt Verwaltung');

            this.$Grid = null;

            this.addEvents({
                onCreate: this.$onCreate,
                onResize : this.$onResize
            });
        },

        /**
         *
         */
        refresh : function()
        {
            var self = this;

            this.Loader.show();

            Ajax.get('package_quiqqer_quiqqerci_ajax_list', function(result)
            {
                console.log( result );
            }, {
                'package' : 'quiqqer/quiqqerci'
            });
        },

        /**
         * event : on create
         */
        $onCreate : function()
        {
            // Buttons
            this.addButton({
                text : 'Projekt hinzufügen',
                events : {
                    onClick : function() {

                    }
                }
            });

            this.addButton({
                text : 'Projekt löschen',
                disabled : true,
                events : {
                    onClick : function() {

                    }
                }
            });

            // Grid
            var Container = new Element('div').inject(
                this.getContent()
            );

            this.$Grid = new Grid(Container, {
                columnModel : [{

               }]
            });

            this.refresh();
        },

        /**
         * event : resize
         */
        $onResize : function()
        {
            if ( !this.$Grid ) {
                return;
            }

            var Body = this.getContent();

            if ( !Body ) {
                return;
            }


            var size = Body.getSize();

            this.$Grid.setHeight( size.y - 40 );
            this.$Grid.setWidth( size.x - 40 );
        },

        addProject : function()
        {

        }
    });
});
