<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="currencies" :fields="['id', 'type']" :icon="'icon'" v-model="selected"></list>
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

                                <tr>
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.type">
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
                currencies: [],
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
                window.axios.get('/frontend/currencies').then(response => {
                    this.currencies = response.data;
                    this.select(this.id === undefined ? this.currencies[0] : this.currencies.find((element) => element.id === this.id));
                });
            },

            create: function () {
                this.currencies.push({
                    fresh: true,
                    name_i18n: {en: "currency_"}
                });

                this.select(this.currencies[this.currencies.length - 1]);
            },

            select: function (currency) {
                this.selected = currency;
            },

            duplicate: function (currency) {
                let copy = Object.assign({}, currency);
                copy.fresh = true;
                this.currencies.push(copy);
                this.select(copy);
            },

            reset: function (currency) {
                if (currency.fresh) {
                    return;
                }

                let index = this.currencies.indexOf(currency);

                window.axios.get('/frontend/currencies/' + currency.id).then(response => {
                    this.currencies[index] = response.data;
                    this.select(this.currencies[index]);
                });
            },

            save: function (currency) {
                let index = this.currencies.indexOf(currency);

                if (currency.fresh) {
                    window.axios.post('/frontend/currencies', currency).then(response => {
                        this.currencies[index] = response.data;
                        this.select(this.currencies[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/currencies/' + currency.id, currency).then(response => {
                    this.currencies[index] = response.data;
                    this.select(this.currencies[index]);
                });
            },

            destroy: function (currency) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.currencies.indexOf(currency);

                if (currency.fresh) {
                    this.currencies.splice(index, 1);
                    this.select(this.currencies[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/currencies/' + currency.id).then(() => {
                    this.currencies.splice(index, 1);
                    this.select(this.currencies[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.currencies) {
                    let currency = this.currencies[key];

                    if (currency.fresh) {
                        window.axios.post('/frontend/currencies', currency).then(response => {
                            this.currencies[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/currencies/' + currency.id, currency).then(response => {
                        this.currencies[key] = response.data;
                    });
                }
            }
        }
    };
</script>
