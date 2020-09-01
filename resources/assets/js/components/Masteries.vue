<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="masteries" :fields="['id', 'name_i18n.en', 'type']" v-model="selected"></list>
                </div>

                <div class="col-md-9">
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.type">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Damage Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.damage_type" :nullable="true" :options="window.library.damageTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Item Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.item_type_id"
                                                          :nullable="true"
                                                          :options="itemTypes"
                                                          :valuecallback="(itemType) => itemType.id"
                                                          :labelcallback="(itemType) => itemType.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Modifier</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.item_modifier_id"
                                                          :nullable="true"
                                                          :options="itemModifiers"
                                                          :valuecallback="(itemModifier) => itemModifier.id"
                                                          :labelcallback="(itemModifier) => window.getPropertyByPath(itemModifier, 'suffix_i18n.en|label')">
                                            </selectpicker>
                                        </div>
                                    </td>
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
    import {Events} from './../events';

    export default {
        data() {
            return {
                masteries: [],
                itemModifiers: [],
                itemTypes: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);

            this.load();
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/masteries').then(response => {
                    this.masteries = response.data;
                    this.select(this.id === undefined ? this.masteries[0] : this.masteries.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/item_modifiers').then(response => {
                    this.itemModifiers = response.data;
                });

                window.axios.get('/frontend/item_types').then(response => {
                    this.itemTypes = response.data;
                });
            },

            create: function () {
                this.masteries.push({
                    fresh: true,
                    name_i18n: {en: "mastery_"}
                });

                this.select(this.masteries[this.masteries.length - 1]);
            },

            select: function (mastery) {
                this.selected = mastery;
            },

            duplicate: function (mastery) {
                let copy = Object.assign({}, mastery);
                copy.fresh = true;
                this.masteries.push(copy);
                this.select(copy);
            },

            reset: function (mastery) {
                if (mastery.fresh) {
                    return;
                }

                let index = this.masteries.indexOf(mastery);

                window.axios.get('/frontend/masteries/' + mastery.id).then(response => {
                    this.masteries[index] = response.data;
                    this.select(this.masteries[index]);
                });
            },

            save: function (mastery) {
                let index = this.masteries.indexOf(mastery);

                if (mastery.fresh) {
                    window.axios.post('/frontend/masteries', mastery).then(response => {
                        this.masteries[index] = response.data;
                        this.select(this.masteries[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/masteries/' + mastery.id, mastery).then(response => {
                    this.masteries[index] = response.data;
                    this.select(this.masteries[index]);
                });
            },

            destroy: function (mastery) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.masteries.indexOf(mastery);

                if (mastery.fresh) {
                    this.masteries.splice(index, 1);
                    this.select(this.masteries[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/masteries/' + mastery.id).then(() => {
                    this.masteries.splice(index, 1);
                    this.select(this.masteries[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.masteries) {
                    let mastery = this.masteries[key];

                    if (mastery.fresh) {
                        window.axios.post('/frontend/masteries', mastery).then(response => {
                            this.masteries[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/masteries/' + mastery.id, mastery).then(response => {
                        this.masteries[key] = response.data;
                    });
                }
            }
        }
    };
</script>
