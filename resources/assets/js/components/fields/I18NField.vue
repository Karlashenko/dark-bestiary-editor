<template>
    <div class="form-group">
        <div class="modal fade" :id="modalId" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: 1050px; text-align: center;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            I18N
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </h5>
                    </div>

                    <div class="modal-body">
                        <form v-if="selected">
                            <div class="form-group">
                                <p style="text-align: left; font-weight: bold;">Translation</p>
                                <div>
                                    <selectpicker v-model="selected"
                                                  :options="translations"
                                                  :selectcallback="(one, another) => one.id === another.id"
                                                  :labelcallback="(translation) => (translation.fresh ? '* ' : '') + translation.key">
                                    </selectpicker>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <p style="text-align: left; font-weight: bold;">Id</p>
                                <input type="text" class="form-control" v-model="selected.id" disabled>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <p style="text-align: left; font-weight: bold;">Key</p>
                                <input type="text" class="form-control" v-model="selected.key">
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <p style="text-align: left; font-weight: bold;">English</p>
                                <textarea class="form-control" v-model="selected.en"></textarea>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <p style="text-align: left; font-weight: bold;">Русский</p>
                                <textarea class="form-control" v-model="selected.ru"></textarea>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <div class="panel panel-default">
                                    <table class="table table-inner">
                                        <tr>
                                            <td>
                                                <table class="table-inner">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 3%;">Index</th>
                                                        <th>Entity type</th>
                                                        <th>Entity id</th>
                                                        <th>Property</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(variable, index) in selected.variables">
                                                        <td style="text-align: center;">
                                                            {{ index }}
                                                        </td>

                                                        <td style="width: 25%">
                                                            <selectpicker v-model="variable.entity_type" :options="window.library.entityTypes"></selectpicker>
                                                        </td>

                                                        <td style="width: 35%">
                                                            <div :class="variable.entity_type === 'Behaviour' ? '' : 'hidden'">
                                                                <behaviour-field v-model="variable.entity_id" :behaviours="behaviours"></behaviour-field>
                                                            </div>

                                                            <div :class="variable.entity_type === 'Effect' ? '' : 'hidden'">
                                                                <selectpicker v-model="variable.entity_id"
                                                                              :options="effects"
                                                                              :valuecallback="(effect) => effect.id"
                                                                              :labelcallback="(effect) => effect.name">
                                                                </selectpicker>
                                                            </div>

                                                            <div :class="variable.entity_type === 'Mastery' ? '' : 'hidden'">
                                                                <selectpicker v-model="variable.entity_id"
                                                                              :options="masteries"
                                                                              :valuecallback="(mastery) => mastery.id"
                                                                              :labelcallback="(mastery) => mastery.name_i18n.en">
                                                                </selectpicker>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <input style="font-family: monospace;" type="text" class="form-control" v-model="variable.property_name">
                                                        </td>

                                                        <td>
                                                            <button type="button" class="btn btn-default" @click="removeVariable(selected, variable)"><span class="glyphicon glyphicon-trash"></span></button>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td></td>
                                                        <td>
                                                            <button type="button" class="btn btn-default" style="width: 100%; height: 32px;" @click="addVariable(selected)">
                                                                <span class="glyphicon glyphicon-plus"></span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="save(selected)">Apply</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="margin-bottom: 0;">
            <div class="panel-body" v-if="selected">
                <table class="table" style="margin: 0;">
                    <tr>
                        <td style="width: 5%;">Key</td>
                        <td>{{ selected.key }}</td>
                    </tr>

                    <tr>
                        <td style="width: 5%;">En</td>
                        <td>{{ selected.en }}</td>
                    </tr>

                    <tr>
                        <td style="width: 5%;">Ru</td>
                        <td>{{ selected.ru }}</td>
                    </tr>

                    <tr v-if="selected.variables.length > 0"><td colspan="2"><hr></td></tr>

                    <tr v-if="selected.variables.length > 0">
                        <td style="width: 5%;">Variables</td>
                        <td>
                            <ul>
                                <li v-for="variable in selected.variables">
                                    {{ variable.entity_type }} {{ variable.entity_id }} {{ variable.property_name }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="panel-footer">
                <button type="button" class="btn btn-default" @click="create()"><span class="glyphicon glyphicon-file"></span> New</button>
                <button type="button" v-if="selected" class="btn btn-default" @click="edit()"><span class="glyphicon glyphicon-edit"></span> Edit</button>
                <button type="button" v-if="selected" class="btn btn-default" @click="clear()"><span class="glyphicon glyphicon-remove-circle"></span> Clear</button>
                <button type="button" v-if="selected" class="btn btn-danger pull-right" @click="destroy()"><span class="glyphicon glyphicon-trash"></span> Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["value"],

        data() {
            return {
                modalId: "i18n_modal_" + this._uid,
                selected: undefined,
                effects: [],
                masteries: [],
                behaviours: [],
                window: window,
                translations: new Map(),
            };
        },

        mounted() {
            window.$(this.modalId).modal();
            window.$('#' + this.modalId).on('hidden.bs.modal', () => {
                if (this.selected.fresh === true) {
                    this.selected = undefined;
                }
            });

            this.reload();
        },

        watch: {
            value: function (value) {
                this.selected = this.translations[value + "_key"];
            }
        },

        methods: {
            create: function () {
                this.reload(() => {
                    this.modalShow();

                    let length = this.translations.length;

                    let translation = {
                        key: "translation_" + length,
                        fresh: true,
                        variables: [],
                        id: length + 100000
                    };

                    this.translations[translation.id + "_key"] = translation;
                    this.selected = translation;
                })
            },

            edit: function () {
                this.reload(() => {
                    this.modalShow();
                })
            },

            clear: function () {
                this.selected = undefined;
                this.$emit('input', undefined);
            },

            destroy: function () {
                if (!confirm('Are you sure?')) {
                    return;
                }

                window.axios.delete('/frontend/i18n/' + this.selected.id);
                this.selected = undefined;
                this.$emit('input', undefined);
            },

            addVariable: function (translation) {
                translation.variables.push({});
            },

            removeVariable: function (translation, variable) {
                let index = translation.variables.indexOf(variable);
                translation.variables.splice(index, 1);
            },

            modalShow: function() {
                window.$("#" + this.modalId).modal("show");
            },

            modalHide: function() {
                window.$("#" + this.modalId).modal("hide");
            },

            reload: function (callback) {
                window.axios.get('/frontend/effects').then(response => {
                    this.effects = response.data;
                });

                window.axios.get('/frontend/masteries').then(response => {
                    this.masteries = response.data;
                });

                window.axios.get('/frontend/behaviours').then(response => {
                    this.behaviours = response.data;
                });

                window.axios.get('/frontend/i18n').then(response => {
                    this.translations = new Map();

                    for (let key in response.data) {
                        this.translations[response.data[key].id + "_key"] = response.data[key];
                    }

                    if (this.value) {
                        this.selected = this.translations[this.value + "_key"];
                    } else {
                        this.selected = this.translations.values().next().value;
                    }

                    if (callback !== undefined) {
                        callback();
                    }
                });
            },

            save: function (translation) {
                if (translation.fresh) {
                    window.axios.post('/frontend/i18n', translation).then(response => {
                        this.translations[response.data.id + "_key"] = response.data;
                        this.selected = this.translations[response.data.id + "_key"];

                        this.$emit('input', this.selected.id);
                        this.modalHide();
                    });

                    return;
                }

                window.axios.put('/frontend/i18n/' + translation.id, translation).then(response => {
                    this.translations[response.data.id + "_key"] = response.data;
                    this.selected = this.translations[response.data.id + "_key"];

                    this.$emit('input', this.selected.id);
                    this.modalHide();
                });
            }
        }
    };
</script>
