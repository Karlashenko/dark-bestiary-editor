<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-3">
                    <list :elements="talents" :fields="['id', 'name_i18n.en', 'category.name_i18n.en', 'tier', 'index']" :icon="'icon'" v-model="selected" :filters="getFilters()"></list>
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

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Category</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.category_id"
                                                          :nullable="true"
                                                          :options="categories"
                                                          :valuecallback="(category) => category.id"
                                                          :labelcallback="(category) => category.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Tier</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.tier">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Index</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.index">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.behaviour_id" :behaviours="behaviours"></behaviour-field>
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
                loading: true,
                talents: [],
                behaviours: [],
                categories: [],
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
            getFilters: function () {
                return [
                    {
                        title: "Category",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.categories,
                            valuecallback: (category) => category.id,
                            labelcallback: (category) => category.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.category_id === value
                        },
                        value: null
                    }
                ];
            },

            load: function () {
                this.loading = true;

                window.axios.get("/frontend/talents").then(response => {
                    this.talents = response.data;
                    this.select(this.id === undefined ? this.talents[0] : this.talents.find((element) => element.id === this.id));
                }).then(() => {
                    window.axios.get("/frontend/behaviours").then(response => {
                        this.behaviours = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/talent_categories").then(response => {
                        this.categories = response.data;
                        this.loading = false;
                    });
                });
            },

            create: function () {
                this.talents.push({
                    fresh: true,
                    name_i18n: {en: "talent_"}
                });

                this.select(this.talents[this.talents.length - 1]);
            },

            select: function (talent) {
                this.selected = talent;
            },

            duplicate: function (talent) {
                let copy = Object.assign({}, talent);
                copy.fresh = true;
                this.talents.push(copy);
                this.select(copy);
            },

            reset: function (talent) {
                if (talent.fresh) {
                    return;
                }

                let index = this.talents.indexOf(talent);

                window.axios.get('/frontend/talents/' + talent.id).then(response => {
                    this.talents[index] = response.data;
                    this.select(this.talents[index]);
                });
            },

            save: function (talent) {
                let index = this.talents.indexOf(talent);

                if (talent.fresh) {
                    window.axios.post('/frontend/talents', talent).then(response => {
                        this.talents[index] = response.data;
                        this.select(this.talents[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/talents/' + talent.id, talent).then(response => {
                    this.talents[index] = response.data;
                    this.select(this.talents[index]);
                });
            },

            destroy: function (talent) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.talents.indexOf(talent);

                if (talent.fresh) {
                    this.talents.splice(index, 1);
                    this.select(this.talents[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/talents/' + talent.id).then(() => {
                    this.talents.splice(index, 1);
                    this.select(this.talents[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.talents) {
                    let talent = this.talents[key];

                    if (talent.fresh) {
                        window.axios.post('/frontend/talents', talent).then(response => {
                            this.talents[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/talents/' + talent.id, talent).then(response => {
                        this.talents[key] = response.data;
                    });
                }
            }
        }
    };
</script>
