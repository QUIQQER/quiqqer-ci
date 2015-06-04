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
            if (!this.$Grid) {
                return;
            }

            var self = this;

            this.Loader.show();

            Ajax.get('package_quiqqer_quiqqerci_ajax_list', function(result)
            {
                self.$Grid.setData({
                    data : result
                });

                self.Loader.hide();
            }, {
                'package' : 'quiqqer/quiqqerci'
            });
        },

        /**
         * event : on create
         */
        $onCreate : function()
        {
            var self = this;

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
                    header    : 'Name',
                    dataIndex : 'name',
                    dataType  : 'string',
                    width     : 200
                }, {
                    header    : 'Beschreibung',
                    dataIndex : 'description',
                    dataType  : 'string',
                    width     : 500
                }]
            });

            this.$Grid.addEvents({
                onDblClick : function() {
                    self.editProject(
                        self.$Grid.getSelectedData()[0].folder
                    );
                }
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

        /**
         *
         */
        addProject : function()
        {

        },

        /**
         * Edit a ci project
         * Opens the edit window
         *
         * @param {String} folderName - Folder of the project
         */
        editProject : function(folderName)
        {
            if (folderName === '') {
                return;
            }

            new QUIConfirm({
                title : 'Projekt bearbeiten',
                maxWidth : 800,
                maxHeight : 600,
                events :
                {
                    onOpen : function(Win)
                    {
                        Win.Loader.show();

                        Ajax.get([
                            'package_quiqqer_quiqqerci_ajax_get',
                            'package_quiqqer_quiqqerci_ajax_projectEditTemplate'
                        ], function(data, tpl)
                        {
                            var i, len, Setting, BuildCheckbox;

                            var builds = data.settings.builds,
                                settings = data.settings.settings,
                                Content = Win.getContent();

                            Content.set('html', tpl);

                            for (i = 0, len = builds.length; i < len; i++)
                            {
                                BuildCheckbox = Content.getElement(
                                    '.builds-container [name="'+ builds[i] +'"]'
                                );

                                if ( BuildCheckbox ) {
                                    BuildCheckbox.checked = true;
                                }
                            }

                            for (i in settings)
                            {
                                Content.getElements(
                                    '.settings [name="'+ i +'"]'
                                ).set('value', settings[i]);
                            }


                            Win.Loader.hide();
                        }, {
                            'package' : 'quiqqer/quiqqerci',
                            folder : folderName
                        });
                    }
                }
            }).open();
        }
    });
});
