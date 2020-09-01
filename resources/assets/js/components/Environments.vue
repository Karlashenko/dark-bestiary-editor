<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="environments" :fields="['id', 'name_i18n.en', 'index']" v-model="selected"></list>
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
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.index">
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Ambience</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.ambience">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Music</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.music">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Weather</td>
                                    <td>
                                        <table class="table table-inner">
                                            <tr v-for="element in selected.weather">
                                                <td style="width: 90%;">
                                                    <selectpicker v-model="element.id"
                                                                  :options="weather"
                                                                  :valuecallback="(e) => e.id"
                                                                  :labelcallback="(e) => e.type">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeWeather(selected, element)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addWeather(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
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
                environments: [],
                aliases: [],
                weather: [],
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
            addWeather: function (environment) {
                environment.weather.push({
                    id: this.weather[0] ? this.weather[0].id : undefined
                });
            },

            removeWeather: function (environment, weather) {
                let index = environment.weather.indexOf(weather);
                environment.weather.splice(index, 1);
            },

            create: function () {
                this.environments.push({
                    fresh: true,
                    name_i18n: {en: "environment_"}
                });

                this.select(this.environments[this.environments.length - 1]);
            },

            saveAll: function () {
                for (let key in this.environments) {
                    let environment = this.environments[key];

                    if (environment.fresh) {
                        window.axios.post('/frontend/environments', environment).then(response => {
                            this.environments[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/environments/' + environment.id, environment).then(response => {
                        this.environments[key] = response.data;
                    });
                }
            },

            load: function () {
                window.axios.get('/frontend/environments').then(response => {
                    this.environments = response.data;
                    this.select(this.id === undefined ? this.environments[0] : this.environments.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/weather').then(response => {
                    this.weather = response.data;
                });
            },

            select: function (environment) {
                this.selected = environment;
            },

            duplicate: function (environment) {
                let copy = Object.assign({}, environment);
                copy.fresh = true;
                this.environments.push(copy);
                this.select(copy);
            },

            reset: function (environment) {
                if (environment.fresh) {
                    return;
                }

                let index = this.environments.indexOf(environment);

                window.axios.get('/frontend/environments/' + environment.id).then(response => {
                    this.environments[index] = response.data;
                    this.select(this.environments[index]);
                });
            },

            save: function (environment) {
                let index = this.environments.indexOf(environment);

                if (environment.fresh) {
                    window.axios.post('/frontend/environments', environment).then(response => {
                        this.environments[index] = response.data;
                        this.select(this.environments[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/environments/' + environment.id, environment).then(response => {
                    this.environments[index] = response.data;
                    this.select(this.environments[index]);
                });
            },

            destroy: function (environment) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.environments.indexOf(environment);

                if (environment.fresh) {
                    this.environments.splice(index, 1);
                    this.select(this.environments[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/environments/' + environment.id).then(response => {
                    this.environments.splice(index, 1);
                    this.select(this.environments[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>
