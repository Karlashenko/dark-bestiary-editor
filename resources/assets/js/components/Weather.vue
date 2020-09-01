<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="weather" :fields="['id', 'type']" v-model="selected"></list>
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
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.type">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Prefab</td>
                                    <td>
                                        <div class="form-group">
                                            <prefab-field v-model="selected.prefab"></prefab-field>
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
                weather: [],
                aliases: [],
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
            create: function () {
                this.weather.push({
                    fresh: true,
                    name: "Just Another Weather"
                });

                this.select(this.weather[this.weather.length - 1]);
            },

            saveAll: function () {
                for (let key in this.weather) {
                    let weather = this.weather[key];

                    if (weather.fresh) {
                        window.axios.post('/frontend/weather', weather).then(response => {
                            this.weather[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/weather/' + weather.id, weather).then(response => {
                        this.weather[key] = response.data;
                    });
                }
            },

            load: function () {
                window.axios.get('/frontend/weather').then(response => {
                    this.weather = response.data;
                    this.select(this.id === undefined ? this.weather[0] : this.weather.find((element) => element.id === this.id));
                });
            },

            select: function (weather) {
                this.selected = weather;
            },

            duplicate: function (weather) {
                let copy = Object.assign({}, weather);
                copy.fresh = true;
                this.weather.push(copy);
                this.select(copy);
            },

            reset: function (weather) {
                if (weather.fresh) {
                    return;
                }

                let index = this.weather.indexOf(weather);

                window.axios.get('/frontend/weather/' + weather.id).then(response => {
                    this.weather[index] = response.data;
                    this.select(this.weather[index]);
                });
            },

            save: function (weather) {
                let index = this.weather.indexOf(weather);

                if (weather.fresh) {
                    window.axios.post('/frontend/weather', weather).then(response => {
                        this.weather[index] = response.data;
                        this.select(this.weather[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/weather/' + weather.id, weather).then(response => {
                    this.weather[index] = response.data;
                    this.select(this.weather[index]);
                });
            },

            destroy: function (weather) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.weather.indexOf(weather);

                if (weather.fresh) {
                    this.weather.splice(index, 1);
                    this.select(this.weather[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/weather/' + weather.id).then(response => {
                    this.weather.splice(index, 1);
                    this.select(this.weather[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>
