<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="scenes" :fields="['id', 'label', 'environment.name_i18n.en']" v-model="selected"></list>
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
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Environment</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.environment_id"
                                                          :nullable="true"
                                                          :options="environments"
                                                          :valuecallback="(environment) => environment.id"
                                                          :labelcallback="(environment) => environment.name_i18n.en">
                                            </selectpicker>
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

                                <tr>
                                    <td>Has No Exit</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.has_no_exit">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Scripted</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_scripted">
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
                scenes: [],
                environments: [],
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
            create: function () {
                this.scenes.push({
                    fresh: true
                });

                this.select(this.scenes[this.scenes.length - 1]);
            },

            saveAll: function () {
                for (let key in this.scenes) {
                    let scene = this.scenes[key];

                    if (scene.fresh) {
                        window.axios.post('/frontend/scenes', scene).then(response => {
                            this.scenes[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/scenes/' + scene.id, scene).then(response => {
                        this.scenes[key] = response.data;
                    });
                }
            },

            load: function () {
                window.axios.get('/frontend/scenes').then(response => {
                    this.scenes = response.data;
                    this.select(this.id === undefined ? this.scenes[0] : this.scenes.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/environments').then(response => {
                    this.environments = response.data;
                });
            },

            select: function (scene) {
                this.selected = scene;
            },

            duplicate: function (scene) {
                let copy = Object.assign({}, scene);
                copy.fresh = true;
                this.scenes.push(copy);
                this.select(copy);
            },

            reset: function (scene) {
                if (scene.fresh) {
                    return;
                }

                let index = this.scenes.indexOf(scene);

                window.axios.get('/frontend/scenes/' + scene.id).then(response => {
                    this.scenes[index] = response.data;
                    this.select(this.scenes[index]);
                });
            },

            save: function (scene) {
                let index = this.scenes.indexOf(scene);

                if (scene.fresh) {
                    window.axios.post('/frontend/scenes', scene).then(response => {
                        this.scenes[index] = response.data;
                        this.select(this.scenes[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/scenes/' + scene.id, scene).then(response => {
                    this.scenes[index] = response.data;
                    this.select(this.scenes[index]);
                });
            },

            destroy: function (scene) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.scenes.indexOf(scene);

                if (scene.fresh) {
                    this.scenes.splice(index, 1);
                    this.select(this.scenes[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/scenes/' + scene.id).then(response => {
                    this.scenes.splice(index, 1);
                    this.select(this.scenes[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>
