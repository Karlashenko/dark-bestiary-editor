<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="categories" :fields="['id', 'name_i18n.en', 'type']" v-model="selected"></list>
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Item Types</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="type in selected.types">
                                                <td>
                                                    <div class="form-group">
                                                        <selectpicker v-model="type.id"
                                                                      :options="types"
                                                                      :valuecallback="(element) => element.id"
                                                                      :labelcallback="(element) => element.type">
                                                        </selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeType(selected, type)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addType(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>

                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAllTypes(selected)">
                                                        <span class="glyphicon glyphicon-import"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
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
                categories: [],
                types: [],
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
                window.axios.get('/frontend/item_categories').then(response => {
                    this.categories = response.data;
                    this.select(this.id === undefined ? this.categories[0] : this.categories.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/item_types').then(response => {
                    this.types = response.data;
                });
            },

            create: function () {
                this.categories.push({
                    fresh: true,
                    name_i18n: {en: "item_category_"},
                    types: []
                });

                this.select(this.categories[this.categories.length - 1]);
            },

            select: function (category) {
                this.selected = category;
            },

            addType: function (category) {
                category.types.push({
                    id: this.types[0] ? this.types[0].id : undefined
                });
            },

            addAllTypes: function (category) {
                category.types.splice(0, category.types.length);

                for (let key in this.types) {
                    category.types.push(this.types[key]);
                }
            },

            removeType: function (category, type) {
                let index = category.types.indexOf(type);
                category.types.splice(index, 1);
            },

            duplicate: function (category) {
                let copy = Object.assign({}, category);
                copy.fresh = true;
                this.categories.push(copy);
                this.select(copy);
            },

            reset: function (category) {
                if (category.fresh) {
                    return;
                }

                let index = this.categories.indexOf(category);

                window.axios.get('/frontend/item_categories/' + category.id).then(response => {
                    this.categories[index] = response.data;
                    this.select(this.categories[index]);
                });
            },

            save: function (category) {
                let index = this.categories.indexOf(category);

                if (category.fresh) {
                    window.axios.post('/frontend/item_categories', category).then(response => {
                        this.categories[index] = response.data;
                        this.select(this.categories[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/item_categories/' + category.id, category).then(response => {
                    this.categories[index] = response.data;
                    this.select(this.categories[index]);
                });
            },

            destroy: function (category) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.categories.indexOf(category);

                if (category.fresh) {
                    this.categories.splice(index, 1);
                    this.select(this.categories[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/item_categories/' + category.id).then(() => {
                    this.categories.splice(index, 1);
                    this.select(this.categories[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.categories) {
                    let category = this.categories[key];

                    if (category.fresh) {
                        window.axios.post('/frontend/item_categories', category).then(response => {
                            this.categories[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/item_categories/' + category.id, category).then(response => {
                        this.categories[key] = response.data;
                    });
                }
            }
        }
    };
</script>
