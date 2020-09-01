<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="missiles" :fields="['id', 'name']" v-model="selected"></list>
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
                                    <td>Name</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.name">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Model</td>
                                    <td>
                                        <div class="form-group">
                                            <prefab-field v-model="selected.model"></prefab-field>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Impact Prefab</td>
                                    <td>
                                        <div class="form-group">
                                            <prefab-field v-model="selected.impact_prefab"></prefab-field>
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
                missiles: [],
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
                window.axios.get('/frontend/missiles').then(response => {
                    this.missiles = response.data;
                    this.select(this.id === undefined ? this.missiles[0] : this.missiles.find((element) => element.id === this.id));
                });
            },

            create: function () {
                this.missiles.push({
                    fresh: true,
                    behaviours: [],
                    name: "Just Another Missile"
                });

                this.select(this.missiles[this.missiles.length - 1]);
            },

            select: function (missile) {
                this.selected = missile;
            },

            duplicate: function (missile) {
                let copy = Object.assign({}, missile);
                copy.fresh = true;
                this.missiles.push(copy);
                this.select(copy);
            },

            reset: function (missile) {
                if (missile.fresh) {
                    return;
                }

                let index = this.missiles.indexOf(missile);

                window.axios.get('/frontend/missiles/' + missile.id).then(response => {
                    this.missiles[index] = response.data;
                    this.select(this.missiles[index]);
                });
            },

            save: function (missile) {
                let index = this.missiles.indexOf(missile);

                if (missile.fresh) {
                    window.axios.post('/frontend/missiles', missile).then(response => {
                        this.missiles[index] = response.data;
                        this.select(this.missiles[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/missiles/' + missile.id, missile).then(response => {
                    this.missiles[index] = response.data;
                    this.select(this.missiles[index]);
                });
            },

            destroy: function (missile) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.missiles.indexOf(missile);

                if (missile.fresh) {
                    this.missiles.splice(index, 1);
                    this.select(this.missiles[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/missiles/' + missile.id).then(() => {
                    this.missiles.splice(index, 1);
                    this.select(this.missiles[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.missiles) {
                    let missile = this.missiles[key];

                    if (missile.fresh) {
                        window.axios.post('/frontend/missiles', missile).then(response => {
                            this.missiles[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/missiles/' + missile.id, missile).then(response => {
                        this.missiles[key] = response.data;
                    });
                }
            }
        }
    };
</script>
