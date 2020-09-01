<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="types" :fields="['id', 'type', 'equipment_strategy_type', 'max_socket_count']" v-model="selected"></list>
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.type">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Mastery</td>
                                    <td>
                                        <selectpicker v-model="selected.mastery_id"
                                                      :nullable="true"
                                                      :options="masteries"
                                                      :valuecallback="(mastery) => mastery.id"
                                                      :labelcallback="(mastery) => mastery.name_i18n.en">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Equipment Strategy</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.equipment_strategy_type" :options="window.library.equipmentStrategyTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Max Socket Count</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.max_socket_count">
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
                types: [],
                masteries: [],
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
                window.axios.get('/frontend/item_types').then(response => {
                    this.types = response.data;
                    this.select(this.id === undefined ? this.types[0] : this.types.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/masteries').then(response => {
                    this.masteries = response.data;
                });
            },

            create: function () {
                this.types.push({
                    fresh: true,
                    name_i18n: {en: "type_"}
                });

                this.select(this.types[this.types.length - 1]);
            },

            select: function (type) {
                this.selected = type;
            },

            duplicate: function (type) {
                let copy = Object.assign({}, type);
                copy.fresh = true;
                this.types.push(copy);
                this.select(copy);
            },

            reset: function (type) {
                if (type.fresh) {
                    return;
                }

                let index = this.types.indexOf(type);

                window.axios.get('/frontend/item_types/' + type.id).then(response => {
                    this.types[index] = response.data;
                    this.select(this.types[index]);
                });
            },

            save: function (type) {
                let index = this.types.indexOf(type);

                if (type.fresh) {
                    window.axios.post('/frontend/item_types', type).then(response => {
                        this.types[index] = response.data;
                        this.select(this.types[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/item_types/' + type.id, type).then(response => {
                    this.types[index] = response.data;
                    this.select(this.types[index]);
                });
            },

            destroy: function (type) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.types.indexOf(type);

                if (type.fresh) {
                    this.types.splice(index, 1);
                    this.select(this.types[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/item_types/' + type.id).then(() => {
                    this.types.splice(index, 1);
                    this.select(this.types[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.types) {
                    let type = this.types[key];

                    if (type.fresh) {
                        window.axios.post('/frontend/item_types', type).then(response => {
                            this.types[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/item_types/' + type.id, type).then(response => {
                        this.types[key] = response.data;
                    });
                }
            }
        }
    };
</script>
