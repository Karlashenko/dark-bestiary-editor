<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-4">
                    <list :elements="translations" :fields="['id', 'key', 'en']" v-model="selected"></list>
                </div>

                <div class="col-md-8">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Key</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.key">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>English</td>
                                    <td>
                                        <div class="form-group">
                                            <textarea class="form-control" v-model="selected.en"></textarea>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Russian</td>
                                    <td>
                                        <div class="form-group">
                                            <textarea class="form-control" v-model="selected.ru"></textarea>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Variables</td>
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
                                                <button class="btn btn-default" @click="removeVariable(selected, variable)"><span class="glyphicon glyphicon-trash"></span></button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td>
                                                <button class="btn btn-default" style="width: 100%; height: 32px;" @click="addVariable(selected)">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </tr>
                            </table>
                        </div>

                        <div class="panel-footer">
                            <div class="pull-left">
                                <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                                <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { Events } from './../events';

    export default {
        data() {
            return {
                loading: false,
                translations: [],
                effects: [],
                masteries: [],
                behaviours: [],
                window: window,
                selected: undefined,
                search: undefined,
            };
        },

        mounted() {
            this.load();

            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);
        },

        updated () {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/i18n').then(response => {
                    this.translations = response.data;
                    this.select(this.id === undefined ? this.translations[0] : this.translations.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/masteries').then(response => {
                    this.masteries = response.data;
                });

                window.axios.get('/frontend/behaviours').then(response => {
                    this.behaviours = response.data;
                });

                window.axios.get('/frontend/effects').then(response => {
                    this.effects = response.data;
                });
            },

            select: function (translation) {
                this.selected = translation;
            },

            create: function (translation) {
                this.translations.push({
                    fresh: true,
                    key: "key_name_" + this.translations.length,
                    en: "",
                    ru: ""
                });

                this.select(this.translations[this.translations.length - 1]);
            },

            addVariable: function (translation) {
                translation.variables.push({});
            },

            removeVariable: function (translation, variable) {
                let index = translation.variables.indexOf(variable);
                translation.variables.splice(index, 1);
            },

            duplicate: function (translation) {
                let copy = Object.assign({}, translation);
                copy.fresh = true;
                copy.key = copy.key + "_copy";

                this.translations.push(copy);
                this.select(copy);
            },

            reset: function (translation) {
                if (translation.fresh) {
                    return;
                }

                let index = this.translations.indexOf(translation);

                window.axios.get('/frontend/i18n/' + translation.id).then(response => {
                    this.translations[index] = response.data;
                    this.select(this.translations[index]);
                });
            },

            destroy: function (translation) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.translations.indexOf(translation);

                if (translation.fresh) {
                    this.translations.splice(index, 1);
                    this.select(this.translations[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/i18n/' + translation.id).then(response => {
                    this.translations.splice(index, 1);
                    this.select(this.translations[index <= 0 ? 0 : index - 1]);
                });
            },

            save: function (translation) {
                let index = this.translations.indexOf(translation);

                if (translation.fresh) {
                    window.axios.post('/frontend/i18n', translation).then(response => {
                        this.translations[index] = response.data;
                        this.select(this.translations[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/i18n/' + translation.id, translation).then(response => {
                    this.translations[index] = response.data;
                    this.select(this.translations[index]);
                });
            },

            filterTranslations: function () {
                return this.translations.filter((translation) => {
                    return translation.key.match(this.search);
                });
            },

            saveAll: function () {
                for (let key in this.translations) {
                    let translation = this.translations[key];

                    if (translation.fresh) {
                        window.axios.post('/frontend/translations', translation);
                        continue;
                    }

                    window.axios.put('/frontend/translations/' + translation.id, translation);
                }

                this.load();
            }
        }
    };
</script>
