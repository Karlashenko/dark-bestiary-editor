<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="properties" :fields="['id', 'index', 'name_i18n.en', 'type']" v-model="selected"></list>
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

                                <tr>
                                    <td>Index</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.index">
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
                                    <td>Abbreviation</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.abbreviation">
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.type">
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Is Unscalable</td>
                                    <td>
                                        <input type="checkbox" v-model="selected.is_unscalable">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Min</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.min">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Max</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.max">
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
                properties: [],
                attributes: [],
                library: window.library,
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

        updated () {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/properties').then(response => {
                    this.properties = response.data;
                    this.select(this.id === undefined ? this.properties[0] : this.properties.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/attributes').then(response => {
                    this.attributes = response.data;
                });
            },

            create: function () {
                this.properties.push({
                    fresh: true,
                    attributes: []
                });

                this.select(this.properties[this.properties.length - 1]);
            },

            select: function (property) {
                this.selected = property;
            },

            duplicate: function (property) {
                let copy = Object.assign({}, property);
                copy.fresh = true;
                this.properties.push(copy);
                this.select(copy);
            },

            reset: function (property) {
                if (property.fresh) {
                    return;
                }

                let index = this.properties.indexOf(property);

                window.axios.get('/frontend/properties/' + property.id).then(response => {
                    this.properties[index] = response.data;
                    this.select(this.properties[index]);
                });
            },

            save: function (property) {
                let index = this.properties.indexOf(property);

                if (property.fresh) {
                    window.axios.post('/frontend/properties', property).then(response => {
                        this.properties[index] = response.data;
                        this.select(this.properties[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/properties/' + property.id, property).then(response => {
                    this.properties[index] = response.data;
                    this.select(this.properties[index]);
                });
            },

            destroy: function (property) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.properties.indexOf(property);

                if (property.fresh) {
                    this.properties.splice(index, 1);
                    this.select(this.properties[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/properties/' + property.id).then(() => {
                    this.properties.splice(index, 1);
                    this.select(this.properties[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.properties) {
                    let property = this.properties[key];

                    if (property.fresh) {
                        window.axios.post('/frontend/properties', property).then(response => {
                            this.properties[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/properties/' + property.id, property).then(response => {
                        this.properties[key] = response.data;
                    });
                }
            }
        }
    };
</script>
