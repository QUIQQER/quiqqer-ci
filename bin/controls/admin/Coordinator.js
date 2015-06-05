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
    'qui/controls/windows/Prompt',
    'controls/grid/Grid',
    'Ajax'

], function(QUI, QUIPanel, QUIConfirm, QUIPrompt, Grid, Ajax)
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

            this.setAttributes({
                title : 'CI Projekt Verwaltung',
                icon  : 'icon-refresh fa fa-connectdevelop'
            });

            this.$Grid = null;

            this.addEvents({
                onCreate: this.$onCreate,
                onResize : this.$onResize
            });
        },

        /**
         * Refresh the view / panel
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
                name : 'add',
                events : {
                    onClick : function() {
                        self.addProject();
                    }
                }
            });

            this.addButton({
                text : 'Projekt löschen',
                name : 'delete',
                disabled : true,
                events : {
                    onClick : function() {
                        self.deleteProject(
                            self.$Grid.getSelectedData()[0].folder
                        );
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
                },

                onClick : function() {
                    if (self.$Grid.getSelectedData().length) {
                        self.getButtons('delete').enable();
                    }
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
         * Add a new project - opens the add dialog
         */
        addProject : function()
        {
            var self = this;

            new QUIPrompt({
                title : 'Projekt hinzufügen',
                maxWidth : 600,
                maxHeight : 300,
                autoclose : false,
                cancel_button : {
                    text      : 'Abbrechen',
                    textimage : 'icon-remove fa fa-remove'
                },
                ok_button : {
                    text      : 'Hinzufügen',
                    textimage : 'icon-ok fa fa-check'
                },
                information : 'Fügen Sie bitte die GIT URL Ihres Projektes ein.',
                events :
                {
                    onSubmit: function (value, Win)
                    {
                        Win.Loader.show();

                        Ajax.post('package_quiqqer_quiqqerci_ajax_add', function(folder)
                        {
                            Win.Loader.hide();

                            if (!folder) {
                                Win.getInput().focus();
                                return;
                            }

                            Win.close();

                            self.refresh();
                            self.editProject(folder);

                        }, {
                            'package' : 'quiqqer/quiqqerci',
                            projecturl : value
                        });
                    }
                }
            }).open();
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
                autoclose : false,
                cancel_button : {
                    text      : 'Abbrechen',
                    textimage : 'icon-remove fa fa-remove'
                },
                ok_button : {
                    text      : 'Speichern',
                    textimage : 'icon-ok fa fa-check'
                },
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
                            var i, len;

                            var builds = data.settings.builds,
                                settings = data.settings.settings,
                                Content = Win.getContent();

                            Content.set('html', tpl);

                            for (i = 0, len = builds.length; i < len; i++)
                            {
                                Content.getElements(
                                    '.builds-container [name="'+ builds[i] +'"]'
                                ).set('checked', true);
                            }

                            for (i in settings)
                            {
                                if (settings.hasOwnProperty(i))
                                {
                                    Content.getElements(
                                        '.settings [name="'+ i +'"]'
                                    ).set('value', settings[i]);
                                }
                            }

                            Win.Loader.hide();
                        }, {
                            'package' : 'quiqqer/quiqqerci',
                            folder : folderName
                        });
                    },

                    onSubmit : function(Win)
                    {
                        Win.Loader.show();

                        var Content = Win.getContent();

                        var builds = Content.getElements(
                            '.builds-container [type="checkbox"]:checked'
                        ).map(function(Elm) {
                            return Elm.get('name');
                        });

                        var i, len;

                        var settings = {};
                        var list = Content.getElements(
                            '.settings input'
                        );

                        for (i = 0, len = list.length; i < len; i++) {
                            settings[ list[i].get('name') ] = list[i].get('value');
                        }

                        Ajax.post('package_quiqqer_quiqqerci_ajax_save', function()
                        {
                            Win.close();
                        }, {
                            'package' : 'quiqqer/quiqqerci',
                            folder : folderName,
                            data : JSON.encode({
                                builds : builds,
                                settings : settings
                            })
                        });
                    }
                }
            }).open();
        },

        /**
         * Delete a project
         *
         * @param {String} folder - Folder of the project
         */
        deleteProject : function(folder)
        {
            var self = this;

            new QUIConfirm({
                title: 'Projekt löschen',
                maxWidth : 600,
                maxHeight : 300,
                text : 'Möchten Sie wirklich das Projekt '+ folder +' löschen?',
                information : 'Alle Daten und Statistiken gehen somit verloren.',
                cancel_button: {
                    text: 'Abbrechen',
                    textimage: 'icon-remove fa fa-remove'
                },
                ok_button: {
                    text: 'Löschen',
                    textimage: 'icon-ok fa fa-check'
                },

                events :
                {
                    onSubmit: function (Win)
                    {
                        Win.Loader.show();

                        Ajax.post('package_quiqqer_quiqqerci_ajax_del',
                            function ()
                            {
                                Win.close();
                                self.refresh();
                            }, {
                                'package': 'quiqqer/quiqqerci',
                                folder: folder
                            });
                    }
                }
            }).open();
        }
    });
});
